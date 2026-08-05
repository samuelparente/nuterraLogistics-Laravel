<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ErpDocumentTestController extends Controller
{
    public function show(): View
    {
        return view('layouts.admin.erp.test-document', [
            'endpoint' => (string) config('sage.url_docs_venda'),
            'scenarios' => $this->scenarios(),
        ]);
    }

    public function store(): JsonResponse
    {
        $endpoint = (string) config('sage.url_docs_venda');
        $debugId = (string) Str::uuid();

        if (! str_contains(strtolower($endpoint), '/testes/api/documentovenda')) {
            Log::warning('Pedido ERP de diagnóstico bloqueado', [
                'debug_id' => $debugId,
                'endpoint' => $endpoint,
            ]);

            return response()->json([
                'success' => false,
                'debug_id' => $debugId,
                'message' => 'Rota de diagnóstico bloqueada: o endpoint configurado não é o ERP de testes.',
                'endpoint' => $endpoint,
            ], 403);
        }

        $scenario = request()->input('scenario');
        $scenarios = $this->scenarios();

        if (! is_string($scenario) || ! isset($scenarios[$scenario])) {
            return response()->json([
                'success' => false,
                'debug_id' => $debugId,
                'message' => 'Cenário de teste inválido.',
            ], 422);
        }

        $payload = $scenarios[$scenario]['payload'];
        $startedAt = microtime(true);

        Log::info('A iniciar pedido ERP de diagnóstico', [
            'debug_id' => $debugId,
            'scenario' => $scenario,
            'endpoint' => $endpoint,
            'payload' => $payload,
        ]);

        //dd($payload);
        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(60)
                ->post($endpoint, $payload);

            Log::info('Resposta do pedido ERP de diagnóstico', [
                'debug_id' => $debugId,
                'scenario' => $scenario,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => $response->successful(),
                'debug_id' => $debugId,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'request' => [
                    'scenario' => $scenario,
                    'endpoint' => $endpoint,
                    'payload' => $payload,
                ],
                'response' => [
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'json' => $response->json(),
                    'body' => $response->body(),
                ],
            ], $response->status());
        } catch (Throwable $e) {
            Log::error('Exceção no pedido ERP de diagnóstico', [
                'debug_id' => $debugId,
                'scenario' => $scenario,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'debug_id' => $debugId,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'request' => [
                    'scenario' => $scenario,
                    'endpoint' => $endpoint,
                    'payload' => $payload,
                ],
                'response' => [
                    'status' => 0,
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                ],
            ], 502);
        }
    }

    private function scenarios(): array
    {
        return [
            'official_sample' => [
                'title' => 'Sample oficial da documentação',
                'description' => 'Payload fornecido para compra, sem terminalID e sem TillID.',
                'button' => 'Enviar sample oficial (PVXX / AAA)',
                'payload' => $this->officialSamplePayload(),
            ],
            'receiving_110' => [
                'title' => 'Receção 110',
                'description' => 'Payload montado com os dados reais da receção 110.',
                'button' => 'Enviar receção 110 (PV / FGR)',
                'payload' => $this->receivingPayload(),
            ],
        ];
    }

    private function officialSamplePayload(): array
    {
        return [
            'clientID' => '7252',
            'salesmanID' => '1',
            'paymentID' => 1,
            'tenderID' => 1,
            'transSerial' => 'PVXX',
            'wharehouseID' => 1,
            'unloadAddress1' => '',
            'contractReferenceNumber' => 'CHAVEDOC',
            'transDocument' => 'AAA',
            'comments' => '',
            'transactionConverted' => false,
            'transactionTaxIncluded' => true,
            'suspended' => false,
            'totalDocumento' => '1647.580',
            'lines' => [
                [
                    'itemID' => '000035',
                    'description' => '10A Base Tortilha 25cm 7un 420gr Hiper Delícia',
                    'barCode' => '',
                    'comments' => '',
                    'quantity' => '1',
                    'price' => '1.650',
                    'sizeID' => 0,
                    'colorID' => 0,
                    'DiscountPercent' => '10.000',
                    'wharehouseOutgoing' => 1,
                    'wharehouseId' => 1,
                    'wharehouseReceipt' => 1,
                    'unitOfSaleID' => 'UNI',
                    'propriedade1' => 'LOTEABC',
                    'validade1' => '2025-01-01',
                ],
                [
                    'itemID' => '000106',
                    'description' => 'CR Caramelos Nata 150gr Hiper Delícia',
                    'barCode' => '',
                    'comments' => '',
                    'quantity' => '1',
                    'price' => '0.680',
                    'sizeID' => 0,
                    'colorID' => 0,
                    'DiscountPercent' => '20.000',
                    'wharehouseOutgoing' => 1,
                    'wharehouseId' => 1,
                    'wharehouseReceipt' => 1,
                    'unitOfSaleID' => 'UNI',
                    'propriedade1' => 'LOTEABC',
                    'validade1' => '2025-01-01',
                ],
            ],
        ];
    }

    private function receivingPayload(): array
    {
        return [
            'terminalID' => 1,
            'clientID' => '20',
            'salesmanID' => '1',
            'paymentID' => 59,
            'TillID' => '001',
            'tenderID' => 8,
            'transSerial' => 'PV',
            'wharehouseID' => 1,
            'unloadAddress1' => '',
            'contractReferenceNumber' => 'TESTE-RECECAO-110',
            'transDocument' => 'FGR',
            'comments' => 'TESTE isolado da criação de documento de compra',
            'transactionConverted' => false,
            'transactionTaxIncluded' => true,
            'suspended' => false,
            'totalDocumento' => '82.533',
            'lines' => [
                [
                    'itemID' => 'FD011969',
                    'description' => 'Go Go Slim Depur 500ml Farmodiética',
                    'barCode' => '5601653011969',
                    'comments' => '',
                    'quantity' => '4',
                    'price' => '20.290',
                    'sizeID' => 0,
                    'colorID' => 0,
                    'DiscountPercent' => '50.000',
                    'wharehouseOutgoing' => 1,
                    'wharehouseId' => 1,
                    'wharehouseReceipt' => 1,
                    'unitOfSaleID' => 'UNI',
                    'propriedade1' => '322323423',
                    'validade1' => '2027-05-12',
                ],
                [
                    'itemID' => 'FD009249',
                    'description' => 'Omegamil Infantil Xarope Manga 200ml Farmodiética',
                    'barCode' => '5601653009249',
                    'comments' => '',
                    'quantity' => '2',
                    'price' => '26.520',
                    'sizeID' => 0,
                    'colorID' => 0,
                    'DiscountPercent' => '50.000',
                    'wharehouseOutgoing' => 1,
                    'wharehouseId' => 1,
                    'wharehouseReceipt' => 1,
                    'unitOfSaleID' => 'UNI',
                    'propriedade1' => 'tg33g',
                    'validade1' => '2026-08-21',
                ],
            ],
        ];
    }
}
