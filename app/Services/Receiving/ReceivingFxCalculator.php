<?php

namespace App\Services\Receiving;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

class ReceivingFxCalculator
{
    public const RATE_SCALE = 12;

    public const PRICE_SCALE = 6;

    public function normalizeRate(string|int|float|null $rate, string $currency): string
    {
        $currency = strtoupper(trim($currency));

        if ($currency === 'EUR') {
            return BigDecimal::one()
                ->toScale(self::RATE_SCALE)
                ->__toString();
        }

        if ($currency !== 'USD') {
            throw new InvalidArgumentException('Moeda não suportada.');
        }

        if ($rate === null || trim((string) $rate) === '') {
            throw new InvalidArgumentException('A taxa de câmbio é obrigatória para receções em USD.');
        }

        $normalized = BigDecimal::of((string) $rate);

        if ($normalized->isLessThanOrEqualTo(0)) {
            throw new InvalidArgumentException('A taxa de câmbio tem de ser superior a zero.');
        }

        return $normalized
            ->toScale(self::RATE_SCALE, RoundingMode::HALF_UP)
            ->__toString();
    }

    public function convertSourcePriceToEur(
        string|int|float $sourcePrice,
        string $currency,
        string|int|float|null $rate,
    ): string {
        $price = BigDecimal::of((string) $sourcePrice);

        if ($price->isLessThan(0)) {
            throw new InvalidArgumentException('O preço de custo não pode ser negativo.');
        }

        $normalizedRate = $this->normalizeRate($rate, $currency);

        return $price
            ->multipliedBy($normalizedRate)
            ->toScale(self::PRICE_SCALE, RoundingMode::HALF_UP)
            ->__toString();
    }

    /**
     * Reavalia um último preço Sage, sempre expresso em EUR.
     *
     * Quando a receção anterior também foi USD, recupera o valor USD implícito
     * através da taxa anterior e aplica a taxa atual. Sem histórico USD fiável,
     * mantém o preço EUR e estabelece apenas uma nova base cambial.
     *
     * @return array{
     *     final_price_eur: string,
     *     source_price: string,
     *     conversion_applied: bool,
     *     status: string,
     *     formula: string
     * }
     */
    public function calculate(
        string|int|float $lastPriceEur,
        string $currentCurrency,
        string|int|float|null $currentRate,
        ?string $previousCurrency,
        string|int|float|null $previousRate
    ): array {
        $priceEur = BigDecimal::of((string) $lastPriceEur);

        if ($priceEur->isLessThan(0)) {
            throw new InvalidArgumentException('O último preço Sage não pode ser negativo.');
        }

        $currency = strtoupper(trim($currentCurrency));
        $normalizedCurrentRate = $this->normalizeRate($currentRate, $currency);
        $priceEur = $priceEur->toScale(self::PRICE_SCALE, RoundingMode::HALF_UP);

        if ($currency === 'EUR') {
            return [
                'final_price_eur' => $priceEur->__toString(),
                'source_price' => $priceEur->__toString(),
                'conversion_applied' => false,
                'status' => 'kept_eur',
                'formula' => 'Preço Sage mantido em EUR.',
            ];
        }

        $previousCurrency = strtoupper(trim((string) $previousCurrency));
        $hasPreviousUsdRate = $previousCurrency === 'USD'
            && $previousRate !== null
            && trim((string) $previousRate) !== ''
            && BigDecimal::of((string) $previousRate)->isGreaterThan(0);

        if (! $hasPreviousUsdRate) {
            $implicitUsdPrice = $priceEur
                ->dividedBy($normalizedCurrentRate, self::PRICE_SCALE, RoundingMode::HALF_UP);

            return [
                'final_price_eur' => $priceEur->__toString(),
                'source_price' => $implicitUsdPrice->__toString(),
                'conversion_applied' => false,
                'status' => 'usd_baseline',
                'formula' => 'Sem histórico USD: mantém o preço Sage em EUR.',
            ];
        }

        $normalizedPreviousRate = BigDecimal::of((string) $previousRate)
            ->toScale(self::RATE_SCALE, RoundingMode::HALF_UP);

        $sourceUsdPrice = $priceEur
            ->dividedBy($normalizedPreviousRate, self::PRICE_SCALE + 6, RoundingMode::HALF_UP);

        $convertedPrice = $sourceUsdPrice
            ->multipliedBy($normalizedCurrentRate)
            ->toScale(self::PRICE_SCALE, RoundingMode::HALF_UP);

        return [
            'final_price_eur' => $convertedPrice->__toString(),
            'source_price' => $sourceUsdPrice
                ->toScale(self::PRICE_SCALE, RoundingMode::HALF_UP)
                ->__toString(),
            'conversion_applied' => true,
            'status' => 'converted_usd',
            'formula' => sprintf(
                '%s EUR ÷ %s × %s = %s EUR',
                $priceEur->__toString(),
                $normalizedPreviousRate->__toString(),
                $normalizedCurrentRate,
                $convertedPrice->__toString()
            ),
        ];
    }
}
