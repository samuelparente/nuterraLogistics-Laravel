<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WooController extends Controller
{
    /**
     * (Opcional) Endpoint para criar via POST.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:255',
            'barcode'       => 'nullable|string|max:255',
            'tax_group_id'  => 'nullable|in:1,2,3', // 1=Normal, 2=Intermédia, 3=Reduzida
        ]);

        $created = $this->createFromArray($data);
        return response()->json($created, 201);
    }

    /**
     * Cria produto no WooCommerce com o básico:
     * - name, sku
     * - status private
     * - manage_stock=true, stock_quantity=0
     * - barcode em meta_data
     * - IVA conforme tax_group_id (1/2/3)
     *
     * Espera: ['name','sku','barcode'?,'tax_group_id'?]
     * 
     * --FIX: Adicionado o simbolo # antes do sku do produto devido a regra da integracao do erp para nao abrir o produto
     *  para venda antes de corrigir e editar tudo no woocommerce--
     */
    public function createFromArray(array $payload): array
    {
        $baseUrl = rtrim(config('services.woocommerce.url'), '/');
        $version = trim(config('services.woocommerce.version', 'wc/v3'), '/');

        // Mapeia 1/2/3 para tax_status / tax_class do Woo
        [$taxStatus, $taxClass] = $this->mapTaxByGroupId($payload['tax_group_id'] ?? null);

        $temporary_sku = '#' . $payload['sku']; // Adiciona o simbolo # antes do sku do produto para evitar abertura para venda

        $data = [
            'name'           => $payload['name'],
            'sku'            => $temporary_sku,
            'status'         => 'private',
            'type'           => 'simple',
            'manage_stock'   => true,
            'stock_quantity' => 0,
            'tax_status'     => $taxStatus, // 'taxable' | 'none'
            // 'tax_class' só envia se não for standard (string vazia)
        ];

        if ($taxClass !== null && $taxClass !== '') {
            $data['tax_class'] = $taxClass; // ex.: 'intermediate-rate' | 'reduced-rate'
        }

        // Meta: barcode
        if (!empty($payload['barcode'])) {
            $data['meta_data'] = [
                ['key' => 'barcode', 'value' => $payload['barcode']],
            ];
        }

        $response = Http::withBasicAuth(
                config('services.woocommerce.key'),
                config('services.woocommerce.secret')
            )
            ->timeout(30)
            ->post("{$baseUrl}/wp-json/{$version}/products", $data);

        if ($response->successful()) {
            return $response->json();
        }

        $msg = $response->json('message') ?? $response->body() ?? 'Erro desconhecido';
        throw new \RuntimeException("WooCommerce: falha a criar produto — {$msg}");
    }

    /**
     * 1 = Normal (Standard)      -> tax_status=taxable, tax_class=''
     * 2 = Intermédia (13%)       -> tax_status=taxable, tax_class='intermediate-rate'
     * 3 = Reduzida (6%)          -> tax_status=taxable, tax_class='reduced-rate'
     *
     * Se vier null/desconhecido: assume Standard.
     */
    private function mapTaxByGroupId($groupId): array
    {
        // 1 = Normal, 2 = Intermédia, 3 = Reduzida
        switch ((string) $groupId) {
            case '2':
                // Taxa Intermédia (13%) -> tua classe personalizada
                return ['taxable', 'iva-taxa-intermedia'];

            case '3':
                // Taxa Reduzida (6%) -> tua classe personalizada
                return ['taxable', 'iva-taxa-reduzida'];

            case '1':
            default:
                // Taxa Normal -> usa Standard (classe vazia) para aproveitar as regras do separador "Padrão"
                // (23% continente, 22% Madeira, 16% Açores, conforme CP)
                return ['taxable', 'iva-taxa-normal']; 
        }
    }

}