<?php

use App\Http\Controllers\Admin\ErpDocumentTestController;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

it('mostra uma página com formulário e o payload sem chamar o ERP', function () {
    config()->set(
        'sage.url_docs_venda',
        'http://nuterra.example/testes/api/DocumentoVenda'
    );

    Http::fake();

    $view = app(ErpDocumentTestController::class)->show();

    expect($view->name())->toBe('layouts.admin.erp.test-document')
        ->and($view->getData()['endpoint'])
        ->toBe('http://nuterra.example/testes/api/DocumentoVenda')
        ->and($view->getData()['scenarios']['official_sample']['payload']['transDocument'])->toBe('AAA')
        ->and($view->getData()['scenarios']['official_sample']['payload'])->not->toHaveKey('terminalID')
        ->and($view->getData()['scenarios']['official_sample']['payload'])->not->toHaveKey('TillID')
        ->and($view->getData()['scenarios']['receiving_110']['payload']['transDocument'])->toBe('FGR');

    Http::assertNothingSent();
});

it('envia o sample oficial sem terminal e sem caixa', function () {
    config()->set(
        'sage.url_docs_venda',
        'http://nuterra.example/testes/api/DocumentoVenda'
    );

    Http::fake([
        'http://nuterra.example/testes/api/DocumentoVenda' => Http::response([
            'document' => 'PVXX AAA/1',
        ], 201),
    ]);

    request()->replace(['scenario' => 'official_sample']);

    $response = app(ErpDocumentTestController::class)->store();
    $data = $response->getData(true);

    expect($response->getStatusCode())->toBe(201)
        ->and($data['success'])->toBeTrue()
        ->and($data['request']['scenario'])->toBe('official_sample')
        ->and($data['response']['json']['document'])->toBe('PVXX AAA/1');

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $request->url() === 'http://nuterra.example/testes/api/DocumentoVenda'
            && ! array_key_exists('terminalID', $payload)
            && ! array_key_exists('TillID', $payload)
            && $payload['clientID'] === '7252'
            && $payload['salesmanID'] === '1'
            && $payload['paymentID'] === 1
            && $payload['tenderID'] === 1
            && $payload['transSerial'] === 'PVXX'
            && $payload['transDocument'] === 'AAA'
            && $payload['transactionConverted'] === false
            && $payload['transactionTaxIncluded'] === false
            && $payload['suspended'] === false
            && $payload['lines'][0]['itemID'] === '000035'
            && $payload['lines'][0]['wharehouseOutgoing'] === 1
            && $payload['lines'][0]['wharehouseId'] === 1
            && $payload['lines'][0]['wharehouseReceipt'] === 1;
    });
});

it('envia o payload completo da receção 110 como cenário separado', function () {
    config()->set(
        'sage.url_docs_venda',
        'http://nuterra.example/testes/api/DocumentoVenda'
    );

    Http::fake([
        'http://nuterra.example/testes/api/DocumentoVenda' => Http::response([
            'document' => 'PV FGR/1',
        ], 201),
    ]);

    request()->replace(['scenario' => 'receiving_110']);

    $response = app(ErpDocumentTestController::class)->store();
    $data = $response->getData(true);

    expect($response->getStatusCode())->toBe(201)
        ->and($data['success'])->toBeTrue()
        ->and($data['request']['scenario'])->toBe('receiving_110')
        ->and($data['response']['json']['document'])->toBe('PV FGR/1');

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $payload['terminalID'] === 1
            && $payload['TillID'] === '001'
            && $payload['clientID'] === '20'
            && $payload['transSerial'] === 'PV'
            && $payload['transDocument'] === 'FGR';
    });
});

it('rejeita um cenário desconhecido sem chamar o ERP', function () {
    config()->set(
        'sage.url_docs_venda',
        'http://nuterra.example/testes/api/DocumentoVenda'
    );

    Http::fake();
    request()->replace(['scenario' => 'desconhecido']);

    $response = app(ErpDocumentTestController::class)->store();
    $data = $response->getData(true);

    expect($response->getStatusCode())->toBe(422)
        ->and($data['success'])->toBeFalse()
        ->and($data['message'])->toBe('Cenário de teste inválido.');

    Http::assertNothingSent();
});

it('bloqueia a chamada quando o endpoint configurado não é o ERP de testes', function () {
    config()->set(
        'sage.url_docs_venda',
        'http://nuterra.example/sage/api/DocumentoVenda'
    );

    Http::fake();
    request()->replace(['scenario' => 'official_sample']);

    $response = app(ErpDocumentTestController::class)->store();
    $data = $response->getData(true);

    expect($response->getStatusCode())->toBe(403)
        ->and($data['success'])->toBeFalse()
        ->and($data['message'])->toContain('endpoint configurado não é o ERP de testes');

    Http::assertNothingSent();
});
