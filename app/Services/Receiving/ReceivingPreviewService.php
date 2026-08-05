<?php

namespace App\Services\Receiving;

use App\Http\Controllers\Admin\ErpController;
use App\Models\Admin\Receiving;
use App\Models\Admin\ReceivingItem;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\Carbon;
use RuntimeException;

class ReceivingPreviewService
{
    public function __construct(
        private readonly ErpController $erp,
        private readonly ReceivingFxCalculator $calculator,
    ) {
    }

    public function build(
        Receiving $receiving,
        string $currency,
        string|int|float|null $rate,
        string $taxMode,
        int $warehouseId,
    ): array {
        $receiving->loadMissing([
            'supplier',
            'items.batches',
        ]);

        if ($receiving->received_at !== null || $receiving->erp_submission_status === 'submitted') {
            throw new RuntimeException('Esta receção já foi finalizada.');
        }

        $currency = strtoupper(trim($currency));
        $currentRate = $this->calculator->normalizeRate($rate, $currency);

        $rows = [];
        $lines = [];
        $itemSnapshots = [];
        $taxIncludedFromLastPurchase = null;

        foreach ($receiving->items as $item) {
            $positiveBatches = $item->batches
                ->filter(fn ($batch) => (float) $batch->quantity > 0)
                ->values();

            $quantity = (int) $positiveBatches->sum('quantity');

            if ($quantity <= 0) {
                $rows[] = [
                    'receiving_item_id' => $item->id,
                    'sku' => (string) $item->product_sku,
                    'name' => (string) $item->product_name,
                    'quantity' => 0,
                    'batch_count' => 0,
                    'last_price_eur' => null,
                    'previous_currency' => null,
                    'previous_rate' => null,
                    'current_rate' => $currentRate,
                    'final_price_eur' => null,
                    'discount_percent' => null,
                    'status' => 'not_received',
                    'status_label' => 'Não recebido',
                    'status_class' => 'secondary',
                    'formula' => 'Sem lotes com quantidade positiva; não será enviado ao Sage.',
                ];

                continue;
            }

            $previousItem = $this->previousFinalizedItem($receiving, $item);
            $previousCurrency = $previousItem?->fx_currency;
            $previousRate = $previousItem?->fx_rate_to_eur;

            $priceData = $this->resolvePrice(
                $item,
                $currency,
                $currentRate,
                $previousCurrency,
                $previousRate,
            );

            if ($priceData['tax_included'] !== null && $taxIncludedFromLastPurchase === null) {
                $taxIncludedFromLastPurchase = $priceData['tax_included'];
            }

            $rows[] = [
                'receiving_item_id' => $item->id,
                'sku' => (string) $item->product_sku,
                'name' => (string) $item->product_name,
                'quantity' => $quantity,
                'batch_count' => $positiveBatches->count(),
                'last_price_eur' => $priceData['last_price_eur'],
                'previous_currency' => $previousCurrency,
                'previous_rate' => $previousRate !== null
                    ? $this->formatRate($previousRate)
                    : null,
                'current_rate' => $currentRate,
                'final_price_eur' => $priceData['final_price_eur'],
                'discount_percent' => $priceData['discount_percent'],
                'status' => $priceData['status'],
                'status_label' => $this->statusLabel($priceData['status']),
                'status_class' => $this->statusClass($priceData['status']),
                'formula' => $priceData['formula'],
            ];

            $itemSnapshots[(string) $item->id] = [
                'currency' => $priceData['persist_currency'],
                'rate' => $priceData['persist_rate'],
                'source_unit_price' => $priceData['source_price'],
                'unit_price_eur' => $priceData['final_price_eur'],
                'received_qty' => $quantity,
            ];

            foreach ($positiveBatches as $batch) {
                $lines[] = [
                    'receiving_item_id' => $item->id,
                    'batch_id' => $batch->id,
                    'itemID' => (string) $item->product_sku,
                    'quantity' => (float) $batch->quantity,
                    'price' => $priceData['final_price_eur'],
                    'DiscountPercent' => $priceData['discount_percent'],
                    'wharehouseId' => $warehouseId,
                    'unitOfSaleID' => 'UNI',
                    'propriedade1' => (string) $batch->batch_number,
                    'validade1' => Carbon::parse($batch->expiry_date)->format('Y-m-d'),
                    'colorID' => 0,
                    'sizeID' => 0,
                ];
            }
        }

        if ($lines === []) {
            throw new RuntimeException('Nenhum produto tem lotes com quantidade positiva para enviar ao Sage.');
        }

        $transactionTaxIncluded = match ($taxMode) {
            'com_impostos' => true,
            'sem_impostos' => false,
            'anterior' => (bool) ($taxIncludedFromLastPurchase ?? false),
            default => throw new RuntimeException('Modo de impostos inválido.'),
        };

        return [
            'version' => 1,
            'currency' => $currency,
            'rate' => $currentRate,
            'tax_mode' => $taxMode,
            'transaction_tax_included' => $transactionTaxIncluded,
            'supplier' => [
                'id' => $receiving->supplier_id,
                'name' => $receiving->supplier?->name ?? 'Fornecedor',
            ],
            'receiving_id' => $receiving->id,
            'order_id' => $receiving->order_id,
            'product_count' => count($rows),
            'line_count' => count($lines),
            'items' => $rows,
            'item_snapshots' => $itemSnapshots,
            'lines' => $lines,
            'batch_signature' => $this->batchSignatureFromLines($lines),
        ];
    }

    public function assertSnapshotStillMatches(Receiving $receiving, array $snapshot): void
    {
        $receiving->load([
            'items.batches',
        ]);

        $currentLines = [];

        foreach ($receiving->items as $item) {
            foreach ($item->batches as $batch) {
                if ((float) $batch->quantity <= 0) {
                    continue;
                }

                $currentLines[] = [
                    'receiving_item_id' => $item->id,
                    'batch_id' => $batch->id,
                    'quantity' => (float) $batch->quantity,
                    'propriedade1' => (string) $batch->batch_number,
                    'validade1' => Carbon::parse($batch->expiry_date)->format('Y-m-d'),
                ];
            }
        }

        if (($snapshot['batch_signature'] ?? null) !== $this->batchSignatureFromLines($currentLines)) {
            throw new RuntimeException(
                'Os lotes ou quantidades foram alterados depois da pré-visualização. Gere uma nova pré-visualização.'
            );
        }
    }

    private function resolvePrice(
        ReceivingItem $item,
        string $currency,
        string $currentRate,
        ?string $previousCurrency,
        string|int|float|null $previousRate,
    ): array {
        if ($item->is_new && $item->fx_unit_price_eur !== null) {
            $createdCurrency = strtoupper((string) ($item->fx_currency ?: 'EUR'));
            $createdRate = $this->calculator->normalizeRate(
                $item->fx_rate_to_eur,
                $createdCurrency,
            );

            if ($createdCurrency !== $currency || (
                $createdCurrency === 'USD'
                && ! BigDecimal::of($createdRate)->isEqualTo(BigDecimal::of($currentRate))
            )) {
                throw new RuntimeException(
                    "A moeda ou taxa usada na criação do produto '{$item->product_name}' "
                    . 'é diferente da escolhida para a receção.'
                );
            }

            $priceEur = BigDecimal::of((string) $item->fx_unit_price_eur)
                ->toScale(ReceivingFxCalculator::PRICE_SCALE, RoundingMode::HALF_UP)
                ->__toString();

            return [
                'last_price_eur' => $priceEur,
                'final_price_eur' => $priceEur,
                'source_price' => (string) (
                    $item->fx_source_unit_price
                    ?? $item->fx_unit_price_eur
                ),
                'discount_percent' => '0.000000',
                'tax_included' => null,
                'status' => 'new_product',
                'formula' => 'Preço convertido durante a criação do produto; não foi convertido novamente.',
                'persist_currency' => $createdCurrency,
                'persist_rate' => $createdRate,
            ];
        }

        $conditions = $this->erp->getLastBuyConditions((string) $item->product_sku);
        $source = 'last_purchase';

        if ($conditions !== null && is_numeric($conditions['UnitPrice'] ?? null)) {
            $lastPriceEur = (string) $conditions['UnitPrice'];
            $discount = (string) ($conditions['DiscountPercent'] ?? 0);
            $taxIncluded = (bool) ($conditions['TransactionTaxIncluded'] ?? false);
        } else {
            $product = $this->erp->getProductBySkuOrBarcode((string) $item->product_sku);

            if ($product === null || ! is_numeric($product['CostPrice'] ?? null)) {
                throw new RuntimeException(
                    "Não foi possível obter um preço Sage para '{$item->product_name}'."
                );
            }

            $lastPriceEur = (string) $product['CostPrice'];
            $discount = '0';
            $taxIncluded = false;
            $source = 'cost_fallback';
        }

        $calculation = $this->calculator->calculate(
            $lastPriceEur,
            $currency,
            $currentRate,
            $previousCurrency,
            $previousRate,
        );

        $status = $calculation['status'];

        if ($source === 'cost_fallback' && $status === 'kept_eur') {
            $status = 'cost_fallback';
        } elseif ($source === 'cost_fallback' && $status === 'usd_baseline') {
            $status = 'cost_fallback_baseline';
        }

        return [
            'last_price_eur' => BigDecimal::of($lastPriceEur)
                ->toScale(ReceivingFxCalculator::PRICE_SCALE, RoundingMode::HALF_UP)
                ->__toString(),
            'final_price_eur' => $calculation['final_price_eur'],
            'source_price' => $calculation['source_price'],
            'discount_percent' => BigDecimal::of($discount)
                ->toScale(ReceivingFxCalculator::PRICE_SCALE, RoundingMode::HALF_UP)
                ->__toString(),
            'tax_included' => $taxIncluded,
            'status' => $status,
            'formula' => $calculation['formula'],
            'persist_currency' => $currency,
            'persist_rate' => $currentRate,
        ];
    }

    private function previousFinalizedItem(
        Receiving $receiving,
        ReceivingItem $item,
    ): ?ReceivingItem {
        return ReceivingItem::query()
            ->select('receiving_items.*')
            ->join('receivings', 'receivings.id', '=', 'receiving_items.receiving_id')
            ->where('receiving_items.product_sku', $item->product_sku)
            ->where('receivings.supplier_id', $receiving->supplier_id)
            ->where('receivings.id', '!=', $receiving->id)
            ->whereNotNull('receivings.received_at')
            ->where('receiving_items.received_qty', '>', 0)
            ->whereNull('receivings.deleted_at')
            ->orderByDesc('receivings.received_at')
            ->orderByDesc('receiving_items.id')
            ->first();
    }

    private function batchSignatureFromLines(array $lines): string
    {
        $signatureRows = collect($lines)
            ->map(fn (array $line) => [
                'receiving_item_id' => (int) $line['receiving_item_id'],
                'batch_id' => (int) $line['batch_id'],
                'quantity' => (string) $line['quantity'],
                'batch_number' => (string) $line['propriedade1'],
                'expiry_date' => (string) $line['validade1'],
            ])
            ->sortBy([
                ['receiving_item_id', 'asc'],
                ['batch_id', 'asc'],
            ])
            ->values()
            ->all();

        return hash('sha256', json_encode($signatureRows, JSON_THROW_ON_ERROR));
    }

    private function formatRate(string|int|float $rate): string
    {
        return BigDecimal::of((string) $rate)
            ->toScale(ReceivingFxCalculator::RATE_SCALE, RoundingMode::HALF_UP)
            ->__toString();
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'converted_usd' => 'Convertido de USD',
            'usd_baseline' => 'Sem histórico cambial',
            'kept_eur' => 'Mantido em EUR',
            'new_product' => 'Convertido na criação',
            'cost_fallback' => 'Custo Sage mantido',
            'cost_fallback_baseline' => 'Custo Sage sem histórico',
            default => 'Preparado',
        };
    }

    private function statusClass(string $status): string
    {
        return match ($status) {
            'converted_usd' => 'success',
            'usd_baseline', 'cost_fallback_baseline' => 'warning',
            'new_product' => 'info',
            'cost_fallback' => 'secondary',
            default => 'primary',
        };
    }
}
