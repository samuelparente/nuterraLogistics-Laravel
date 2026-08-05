<?php

use App\Services\Receiving\ReceivingFxCalculator;

it('mantém o último preço Sage quando a receção é em EUR', function () {
    $result = app(ReceivingFxCalculator::class)->calculate(
        '100.123456',
        'EUR',
        null,
        'USD',
        '0.900000000000',
    );

    expect($result)
        ->final_price_eur->toBe('100.123456')
        ->source_price->toBe('100.123456')
        ->conversion_applied->toBeFalse()
        ->status->toBe('kept_eur');
});

it('reavalia o preço EUR usando a taxa USD imediatamente anterior', function () {
    $result = app(ReceivingFxCalculator::class)->calculate(
        '100',
        'USD',
        '0.950000000000',
        'USD',
        '0.920000000000',
    );

    expect($result)
        ->final_price_eur->toBe('103.260870')
        ->source_price->toBe('108.695652')
        ->conversion_applied->toBeTrue()
        ->status->toBe('converted_usd');
});

it('mantém o valor EUR e cria uma base quando não existe histórico USD fiável', function () {
    $result = app(ReceivingFxCalculator::class)->calculate(
        '100',
        'USD',
        '0.950000000000',
        'EUR',
        '1.000000000000',
    );

    expect($result)
        ->final_price_eur->toBe('100.000000')
        ->source_price->toBe('105.263158')
        ->conversion_applied->toBeFalse()
        ->status->toBe('usd_baseline');
});

it('normaliza a taxa com doze casas sem usar aritmética float', function () {
    $calculator = app(ReceivingFxCalculator::class);

    expect($calculator->normalizeRate('0.923456789012', 'USD'))
        ->toBe('0.923456789012')
        ->and($calculator->convertSourcePriceToEur('10', 'USD', '0.923456789012'))
        ->toBe('9.234568');
});

it('rejeita uma taxa USD nula ou não positiva', function (?string $rate) {
    app(ReceivingFxCalculator::class)->normalizeRate($rate, 'USD');
})->with([null, '', '0', '-0.1'])->throws(InvalidArgumentException::class);
