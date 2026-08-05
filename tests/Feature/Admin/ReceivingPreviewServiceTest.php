<?php

use App\Http\Controllers\Admin\ErpController;
use App\Models\Admin\Order;
use App\Models\Admin\Receiving;
use App\Models\Admin\ReceivingItem;
use App\Models\Admin\Supplier;
use App\Services\Receiving\ReceivingFxCalculator;
use App\Services\Receiving\ReceivingPreviewService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    // O esquema atual referencia `statuses`, mas essa tabela não tem migration
    // no projeto. Cria o alvo mínimo apenas na base SQLite deste teste.
    if (! Schema::hasTable('statuses')) {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
        });
    }

    if (! Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });
    }
});

function receivingForFxTest(Supplier $supplier, ?string $receivedAt = null): Receiving
{
    $order = Order::create();

    return Receiving::create([
        'order_id' => $order->id,
        'supplier_id' => $supplier->id,
        'started_at' => now(),
        'received_at' => $receivedAt,
    ]);
}

function receivingItemForFxTest(
    Receiving $receiving,
    array $attributes = [],
): ReceivingItem {
    return ReceivingItem::create(array_merge([
        'receiving_id' => $receiving->id,
        'product_sku' => 'SKU-USD',
        'product_name' => 'Produto de teste',
        'supplier_id' => $receiving->supplier_id,
        'ordered_qty' => 10,
        'received_qty' => 10,
    ], $attributes));
}

function previewServiceForFxTest(ErpController $erp): ReceivingPreviewService
{
    return new ReceivingPreviewService(
        $erp,
        app(ReceivingFxCalculator::class),
    );
}

it('usa a taxa da receção finalizada imediatamente anterior do mesmo artigo e fornecedor', function () {
    $supplier = Supplier::create(['name' => 'Fornecedor USD', 'erp_id' => 20]);

    $previous = receivingForFxTest($supplier, now()->subDay()->toDateTimeString());
    receivingItemForFxTest($previous, [
        'fx_currency' => 'USD',
        'fx_rate_to_eur' => '0.920000000000',
        'fx_source_unit_price' => '108.695652',
        'fx_unit_price_eur' => '100.000000',
    ]);

    $current = receivingForFxTest($supplier);
    $currentItem = receivingItemForFxTest($current, ['received_qty' => 0]);
    $currentItem->batches()->create([
        'batch_number' => 'LOTE-1',
        'expiry_date' => now()->addYear()->toDateString(),
        'quantity' => 10,
    ]);

    $erp = Mockery::mock(ErpController::class);
    $erp->shouldReceive('getLastBuyConditions')
        ->once()
        ->with('SKU-USD')
        ->andReturn([
            'UnitPrice' => '100',
            'DiscountPercent' => '12.500000',
            'TransactionTaxIncluded' => true,
        ]);

    $snapshot = previewServiceForFxTest($erp)->build(
        $current,
        'USD',
        '0.950000000000',
        'anterior',
        1,
    );

    expect($snapshot['items'][0])
        ->previous_rate->toBe('0.920000000000')
        ->final_price_eur->toBe('103.260870')
        ->discount_percent->toBe('12.500000')
        ->status->toBe('converted_usd')
        ->and($snapshot['transaction_tax_included'])->toBeTrue()
        ->and($snapshot['lines'][0]['price'])->toBe('103.260870')
        ->and($snapshot['lines'][0]['DiscountPercent'])->toBe('12.500000');
});

it('não salta uma receção EUR recente para procurar uma taxa USD mais antiga', function () {
    $supplier = Supplier::create(['name' => 'Fornecedor misto', 'erp_id' => 21]);

    $olderUsd = receivingForFxTest($supplier, now()->subDays(2)->toDateTimeString());
    receivingItemForFxTest($olderUsd, [
        'fx_currency' => 'USD',
        'fx_rate_to_eur' => '0.900000000000',
    ]);

    $recentEur = receivingForFxTest($supplier, now()->subDay()->toDateTimeString());
    receivingItemForFxTest($recentEur, [
        'fx_currency' => 'EUR',
        'fx_rate_to_eur' => '1.000000000000',
    ]);

    $current = receivingForFxTest($supplier);
    $currentItem = receivingItemForFxTest($current, ['received_qty' => 0]);
    $currentItem->batches()->create([
        'batch_number' => 'LOTE-2',
        'expiry_date' => now()->addYear()->toDateString(),
        'quantity' => 10,
    ]);

    $erp = Mockery::mock(ErpController::class);
    $erp->shouldReceive('getLastBuyConditions')->once()->andReturn([
        'UnitPrice' => '100',
        'DiscountPercent' => '0',
        'TransactionTaxIncluded' => false,
    ]);

    $snapshot = previewServiceForFxTest($erp)->build(
        $current,
        'USD',
        '0.950000000000',
        'sem_impostos',
        1,
    );

    expect($snapshot['items'][0])
        ->previous_currency->toBe('EUR')
        ->previous_rate->toBe('1.000000000000')
        ->final_price_eur->toBe('100.000000')
        ->status->toBe('usd_baseline');
});

it('não converte novamente o custo de um produto criado durante a receção', function () {
    $supplier = Supplier::create(['name' => 'Fornecedor novo', 'erp_id' => 22]);
    $current = receivingForFxTest($supplier);
    $item = receivingItemForFxTest($current, [
        'product_sku' => 'NOVO-USD',
        'is_new' => true,
        'received_qty' => 0,
        'fx_currency' => 'USD',
        'fx_rate_to_eur' => '0.923456789012',
        'fx_source_unit_price' => '10.000000',
        'fx_unit_price_eur' => '9.234568',
    ]);
    $item->batches()->create([
        'batch_number' => 'LOTE-NOVO',
        'expiry_date' => now()->addYear()->toDateString(),
        'quantity' => 5,
    ]);

    $erp = Mockery::mock(ErpController::class);
    $erp->shouldNotReceive('getLastBuyConditions');
    $erp->shouldNotReceive('getProductBySkuOrBarcode');

    $snapshot = previewServiceForFxTest($erp)->build(
        $current,
        'USD',
        '0.923456789012',
        'sem_impostos',
        1,
    );

    expect($snapshot['items'][0])
        ->final_price_eur->toBe('9.234568')
        ->status->toBe('new_product')
        ->and($snapshot['lines'][0]['price'])->toBe('9.234568');
});
