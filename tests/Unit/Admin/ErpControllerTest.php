<?php

use App\Http\Controllers\Admin\ErpController;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

it('obtém pagamento e meio de pagamento do fornecedor Sage', function () {
    Http::fake([
        '*api_sage_sql_read.php' => Http::response([
            'data' => [[
                'PaymentID' => 59,
                'TenderID' => 8,
            ]],
        ]),
    ]);

    $defaults = app(ErpController::class)->getSupplierDocumentDefaults(20);

    expect($defaults)->toBe([
        'paymentID' => 59,
        'tenderID' => 8,
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request->data()['query'] ?? '', 'WHERE SupplierID = 20');
    });
});

it('envia os campos explícitos do documento de compra e o armazém em cada linha', function () {
    config()->set('sage.url_docs_venda', 'https://erp.test/api/DocumentoVenda');

    Http::fake([
        'https://erp.test/api/DocumentoVenda' => Http::response([
            'success' => true,
        ], 200),
    ]);

    $result = app(ErpController::class)->erpInsertDocument([
        'clientID' => 20,
        'salesmanID' => 1,
        'paymentID' => 59,
        'tenderID' => 8,
        'transSerial' => 'PV',
        'wharehouseID' => 1,
        'transDocument' => 'FGR',
        'contractReferenceNumber' => 'NUTERRA-RECEIVING-42-test-key',
        'transactionTaxIncluded' => false,
        'comments' => 'Entrada finalizada via plataforma NUTERRA | Logistics',
        'lines' => [[
            'itemID' => 'FD016537',
            'quantity' => 14,
            'price' => '25.123456',
            'DiscountPercent' => '57.498000',
            'wharehouseId' => 1,
            'unitOfSaleID' => 'UNI',
            'propriedade1' => '1213123123',
            'validade1' => '2027-04-27',
            'colorID' => 0,
            'sizeID' => 0,
        ]],
    ]);

    expect($result['success'])->toBeTrue();

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $request->url() === 'https://erp.test/api/DocumentoVenda'
            && $payload['clientID'] === 20
            && $payload['salesmanID'] === 1
            && $payload['paymentID'] === 59
            && $payload['tenderID'] === 8
            && $payload['transSerial'] === 'PV'
            && $payload['wharehouseID'] === 1
            && $payload['contractReferenceNumber'] === 'NUTERRA-RECEIVING-42-test-key'
            && $payload['lines'][0]['wharehouseId'] === 1
            && $payload['lines'][0]['price'] === '25.123456'
            && $payload['lines'][0]['DiscountPercent'] === '57.498000';
    });
});

it('converte o custo USD de um produto novo com precisão decimal', function () {
    config()->set('sage.url_artigos', 'https://erp.test/api/Artigo');

    Http::fake([
        'https://erp.test/api/Artigo' => Http::response(['success' => true], 200),
    ]);

    $result = app(ErpController::class)->erpCreateProduct([
        'ItemID' => 'NOVO-USD',
        'Description' => 'Produto USD',
        'ShortDescription' => 'Produto USD',
        'pc' => '10',
        'moedaId' => 'USD',
        'taxaCambio' => '0.923456789012',
    ]);

    expect($result['success'])->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://erp.test/api/Artigo'
            && $request->data()['pc'] === '9.234568';
    });
});

it('não chama o ERP quando faltam campos obrigatórios do documento', function () {
    config()->set('sage.url_docs_venda', 'https://erp.test/api/DocumentoVenda');
    Http::fake();

    $result = app(ErpController::class)->erpInsertDocument([
        'clientID' => 20,
        'wharehouseID' => 1,
        'transDocument' => 'FGR',
        'transactionTaxIncluded' => false,
        'lines' => [],
    ]);

    expect($result)
        ->success->toBeFalse()
        ->status->toBe(422)
        ->error->toStartWith('Payload ERP inválido:');

    Http::assertNothingSent();
});
