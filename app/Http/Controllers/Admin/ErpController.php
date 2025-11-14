<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use App\Models\Admin\Bonus;
use Illuminate\Http\RedirectResponse;

class ErpController extends Controller
{
    protected $endpoint = 'http://nuterra.dyndns.biz:45248/api_warehouse/v1/api_sage_sql_read.php';
    protected $token = 'Qv1FC5Olt4DDFu7H6vKFUn0pvTT!n/!W8QLpY9JXBJA=p0uWVSPECJOY?Qn?MHJHZmAKscV8Syqb6QH58DV//Uelema=g0RKNFoZa?-qW3Fytp3IGfTHcs/EiGGEq/09zkIg9P=O6/qWfYyHSe/x=8g0fCVowY?!ncAlWxc-143u8ZG3qKQiAlQa6d7DUe=Rhz1HZafXnHWBhtQylHUfEC2fK9AMrj717BqX1h7WakVAD3BPK9DSq0X4LsYb/BM7';

    public function getProducts(Request $request)
    {
        $where = [];

        // Obter erp_id correspondente ao supplier_id/brand_id local
        $supplierErpId = $request->filled('supplier_id')
            ? Supplier::find($request->supplier_id)?->erp_id
            : null;

        $brandErpId = $request->filled('brand_id')
            ? Brand::find($request->brand_id)?->erp_id
            : null;

        if ($supplierErpId) {
            $where[] = "i.SupplierID = {$supplierErpId}";
        }

        if ($brandErpId) {
            $where[] = "i.FamilyID = {$brandErpId}";
        }

        // 🔍 Adicionar pesquisa por nome, SKU ou código de barras
        $search = $request->input('search');

        if ($search) {
            $search = trim($search);
            $searchEscaped = str_replace("'", "''", $search);

            $where[] = "(LOWER(n.Description) LIKE LOWER('%{$searchEscaped}%') 
                    OR CAST(i.ItemID AS VARCHAR) LIKE '%{$searchEscaped}%' 
                    OR i.BarCode LIKE '%{$searchEscaped}%')";
        }

        // Excluir packs (ItemIDs que terminam em -1, -2, -3, etc.)
        $where[] = "i.ItemID NOT LIKE '%-[0-9]'";

        // Excluir produtos descontinuados
        $where[] = "i.Discontinued = 0";

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $startDate = $request->start_date ?? now()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        // Ordenação dinâmica (ProductName mapeado para n.Description)
        $sortInput = $request->input('sort', 'ProductName'); // valor amigável da view
        $directionInput = strtolower($request->input('direction', 'asc'));

        $sort = match ($sortInput) {
            'ProductName' => 'n.Description COLLATE Latin1_General_CI_AI',
            'StockQty' => 'StockQty',
            'CostPrice' => 'CostPrice',
            'SalesLastPeriod' => 'SalesLastPeriod',
            'SalesPreviousYearPeriod' => 'SalesPreviousYearPeriod',
            default => 'i.ItemID',
        };

        $direction = $directionInput === 'desc' ? 'DESC' : 'ASC';


        $query = "
            SELECT 
                i.ItemID,
                i.BarCode,
                i.SupplierID,
                i.FamilyID,
                i.LastOutgoingDate,
                i.SupplierOrderQty,
                i.PhysicalQty,
                n.Description AS ProductName,
                n.ShortDescription AS ShortDescription,
                (
                    SELECT TOP 1 UnitPrice
                    FROM dbo.ItemSellingPrices
                    WHERE ItemID = i.ItemID AND PriceLineID = 0
                ) AS CostPrice,
                (
                    SELECT TOP 1 AvailableQty
                    FROM dbo.Stock
                    WHERE ItemID = i.ItemID AND WarehouseID = 1
                ) AS StockQty,
                (
                    SELECT SUM(z.Quantity)
                    FROM dbo.SaleTransactionDetails z
                    WHERE z.ItemID = i.ItemID
                    AND z.TransDocument = 'FR'
                    AND z.CreateDate BETWEEN '{$startDate}' AND '{$endDate}'
                ) AS SalesLastPeriod,
                (
                    SELECT SUM(z.Quantity)
                    FROM dbo.SaleTransactionDetails z
                    WHERE z.ItemID = i.ItemID
                    AND z.TransDocument = 'FR'
                    AND z.CreateDate BETWEEN 
                            DATEADD(YEAR, -1, '{$startDate}') AND 
                            DATEADD(YEAR, -1, '{$endDate}')
                ) AS SalesPreviousYearPeriod
            FROM dbo.Item i
            LEFT JOIN dbo.ItemNames n ON i.ItemID = n.ItemID 
            $whereClause
            ORDER BY {$sort} {$direction}

        ";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
            ])
            ->withoutVerifying()
            ->post($this->endpoint, ['query' => $query]);

            if (!$response->successful() || !isset($response['data'])) {

                
                return collect();
            }

            // dd($response['data']);

            $daysInPeriod = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

            // Obter todos os suppliers e brands locais indexados pelo erp_id
            $localSuppliers = Supplier::select('id', 'erp_id', 'name')->get()->keyBy('erp_id');
            $localBrands = Brand::select('id', 'erp_id', 'name')->get()->keyBy('erp_id');

            return collect($response['data'])->map(function ($item) use ($daysInPeriod, $localSuppliers, $localBrands) {
                
                // Normalizar data do último movimento
                $rawDate = $item['LastOutgoingDate'] ?? null;

                try {
                    if (is_array($rawDate) && isset($rawDate['date'])) {
                        $item['LastOutgoingDate'] = Carbon::parse($rawDate['date'])->format('Y-m-d');
                    } elseif (is_string($rawDate)) {
                        $item['LastOutgoingDate'] = Carbon::parse($rawDate)->format('Y-m-d');
                    } else {
                        $item['LastOutgoingDate'] = null;
                    }
                } catch (\Exception $e) {
                    $item['LastOutgoingDate'] = null;
                }

                // Cálculo de sugestão
                $sales = $item['SalesLastPeriod'] ?? 0;
                $stock = $item['StockQty'] ?? 0;

                $averageDaily = $daysInPeriod > 0 ? $sales / $daysInPeriod : 0;
                $neededForNext30Days = ceil($averageDaily * 30);

                $item['SuggestedQty'] = max($neededForNext30Days - $stock, 0);

                // Mapear para os dados locais
                $supplier = $localSuppliers[$item['SupplierID']] ?? null;
                $brand = $localBrands[$item['FamilyID']] ?? null;

                // Bonificações
                $bonuses = Bonus::whereNull('deleted_at')->get();

                $supplierId = $supplier?->id;
                $brandId = $brand?->id;

                // 1º tenta encontrar bónus pela marca
                $bonus = $bonuses->first(fn ($b) => $b->brand_id === $brandId);

                // Se não houver para a marca, tenta pelo fornecedor
                if (!$bonus) {
                    $bonus = $bonuses->first(fn ($b) => $b->supplier_id === $supplierId);
                }

                $item['SupplierID_Local'] = $supplier?->id;
                $item['BrandID_Local'] = $brand?->id;
                $item['SupplierName'] = $supplier?->name;
                $item['BrandName'] = $brand?->name;
                
                // Buscar todos os bónus aplicáveis por marca ou fornecedor
                $applicableBonuses = $bonuses->filter(function ($b) use ($brandId, $supplierId) {
                    return $b->brand_id === $brandId || $b->supplier_id === $supplierId;
                });

                $item['HasBonus'] = $applicableBonuses->isNotEmpty();
                $item['Bonuses'] = $applicableBonuses->values(); // <- remove chaves preservadas para JSON limpo

                // Para compatibilidade com código antigo (se ainda usares estas colunas em algum lado):
                $firstBonus = $applicableBonuses->first();
                $item['BonusName'] = $firstBonus?->name;
                $item['BonusDescription'] = $firstBonus?->description;


                return $item;
            });

        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getProductBySkuOrBarcode(string $value, array $filters = [])
    {

        // TaxableGroupID =1 Iva a 23 Taxa normal
        // TaxableGroupID = 2 Iva taxa intermédia 13%
        // TaxableGroupID= 3 Iva reduzido 6%

        $value = trim($value);
        $escaped = str_replace("'", "''", $value);

        $startDate = $filters['start_date'] ?? now()->subDays(30)->toDateString();
        $endDate = $filters['end_date'] ?? now()->toDateString();

        $query = "
            SELECT TOP 1
                i.ItemID,
                i.BarCode,
                i.SupplierID,
                i.FamilyID,
                i.LastOutgoingDate,
                i.SupplierOrderQty,
                i.PhysicalQty,
                i.TaxableGroupID,
                n.Description AS ProductName,
                (
                    SELECT TOP 1 UnitPrice
                    FROM dbo.ItemSellingPrices
                    WHERE ItemID = i.ItemID AND PriceLineID = 0
                ) AS CostPrice,
                (
                    SELECT TOP 1 AvailableQty
                    FROM dbo.Stock
                    WHERE ItemID = i.ItemID AND WarehouseID = 1
                ) AS StockQty,
                (
                    SELECT SUM(z.Quantity)
                    FROM dbo.SaleTransactionDetails z
                    WHERE z.ItemID = i.ItemID
                    AND z.TransDocument = 'FR'
                    AND z.CreateDate BETWEEN '{$startDate}' AND '{$endDate}'
                ) AS SalesLastPeriod,
                (
                    SELECT SUM(z.Quantity)
                    FROM dbo.SaleTransactionDetails z
                    WHERE z.ItemID = i.ItemID
                    AND z.TransDocument = 'FR'
                    AND z.CreateDate BETWEEN 
                        DATEADD(YEAR, -1, '{$startDate}') AND 
                        DATEADD(YEAR, -1, '{$endDate}')
                ) AS SalesPreviousYearPeriod
            FROM dbo.Item i
            LEFT JOIN dbo.ItemNames n ON i.ItemID = n.ItemID
            WHERE i.ItemID = '{$escaped}' 
            OR i.BarCode = '{$escaped}' 
            OR i.ItemID IN (
                SELECT ItemID 
                FROM dbo.POSIdentity 
                WHERE POSItemID = '{$escaped}'
            )
        ";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ])
        ->withoutVerifying()
        ->post($this->endpoint, ['query' => $query]);

        if (!$response->successful() || !isset($response['data'][0])) {
            return null;
        }

        $product = $response['data'][0];

       

        $supplier = Supplier::where('erp_id', $product['SupplierID'])->first();
        $brand = Brand::where('erp_id', $product['FamilyID'])->first();

        $product['SupplierName'] = $supplier?->name;
        $product['BrandName'] = $brand?->name;
        $product['SupplierID_Local'] = $supplier?->id;
        $product['BrandID_Local'] = $brand?->id;

        // Bonificações
        $bonuses = Bonus::whereNull('deleted_at')->get();
        $supplierId = $supplier?->id;
        $brandId = $brand?->id;

        $applicableBonuses = $bonuses->filter(function ($b) use ($supplierId, $brandId) {
            return $b->supplier_id === $supplierId || $b->brand_id === $brandId;
        })->values(); // garantir array indexado sequencialmente

        $product['Bonuses'] = $applicableBonuses;
        $product['HasBonus'] = $applicableBonuses->isNotEmpty();


        // Format data
        try {
            $rawDate = $product['LastOutgoingDate'] ?? null;
            if (is_array($rawDate) && isset($rawDate['date'])) {
                $product['LastOutgoingDate'] = \Carbon\Carbon::parse($rawDate['date'])->format('Y-m-d');
            } elseif (is_string($rawDate)) {
                $product['LastOutgoingDate'] = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
            } else {
                $product['LastOutgoingDate'] = null;
            }
        } catch (\Exception $e) {
            $product['LastOutgoingDate'] = null;
        }

        // Cálculo de sugestão
        $daysInPeriod = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        $sales = $product['SalesLastPeriod'] ?? 0;
        $stock = $product['StockQty'] ?? 0;

        $averageDaily = $daysInPeriod > 0 ? $sales / $daysInPeriod : 0;
        $neededForNext30Days = ceil($averageDaily * 30);
        $product['SuggestedQty'] = max($neededForNext30Days - $stock, 0);

        $productId =  $product['ItemID'];
        $taxRate = $this->getProductTaxRate($productId);
        $product['taxRate'] = $taxRate;
        
        //$result = $this->createProduct($product);

        //dd($result);

        return $product;
    }

    // Insere novo produto no erp
    public function erpCreateProduct(array $product): array
    {
        // Endpoint testes
        //$url = 'http://nuterra.dyndns.biz:45248/testes/api/Artigos';

        // Endpoint producao
        $url = 'http://nuterra.dyndns.biz:45248/sage/api/Artigos';

        
        // Montar payload com base no $product
        $payload = [
            "ItemID"           => $product['ItemID'] ?? '',
            "ItemType"         => $product['ItemType'] ?? 0,
            "BarCode"          => $product['BarCode'] ?? '',
            "Description"      => $product['Description'] ?? '',
            "ShortDescription" => $product['ShortDescription'] ?? '',
            "BarCodeType"      => $product['BarCodeType'] ?? 0,
            "UnitOfSaleID"     => $product['UnitOfSaleID'] ?? 'UNI',
            "TaxableGroupID"   => $product['TaxableGroupID'] ?? 1,
            "pc"               => $product['pc']     ?? 0,
            "ReorderPoint" => $product['ReorderPoint'] ?? 0,
            "RestockLevel" => $product['RestockLevel'] ?? 0,
            "SupplierID"   => $product['SupplierID'] ?? 0,
            "FamilyID"     => $product['FamilyID'] ?? 0,

            "ItemFirstGroupID"  => $product['ItemFirstGroupID']  ?? 1, //define MARCA no campo extra
            "ItemSecondGroupID" => $product['ItemSecondGroupID'] ?? 0,
            "ItemThirdGroupID"  => $product['ItemThirdGroupID']  ?? 0,

            "Descontinuado" => $product['Descontinuado'] ?? false,
            "NaoAgrupar"    => $product['NaoAgrupar'] ?? false,
            "cores"          => $product['cores'] ?? [],
            "tamanhos"       => $product['tamanhos'] ?? [],
            "ChavePropriedade1" => 'LOTE',

            "ProductCategory" => 1, // Define M - Mercadoria
        ];

        
        // Enviar request
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30)
                ->asJson()
                ->post($url, $payload);

            $json = null;
            try {
                $json = $response->json();
            } catch (\Throwable $ignore) {}

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status'  => $response->status(),
                    'data'    => $json,
                    'payload' => $payload, // para debug
                ];
            }

            return [
                'success' => false,
                'status'  => $response->status(),
                'error'   => $json['message'] ?? (string)$response->body(),
                'payload' => $payload,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'status'  => 0,
                'error'   => 'Erro: ' . $e->getMessage(),
                'payload' => $payload,
            ];
        }
    }

    // Insere documento de compra no ERP
    public function erpInsertDocument(array $orderReceived = []): array
    {

        // Endpoint testes
        //$url = 'http://nuterra.dyndns.biz:45248/testes/api/DocumentoVenda';

        // Endpoint producao
        $url = 'http://nuterra.dyndns.biz:45248/sage/api/DocumentoVenda';

        /**
    
         * NOTA: clientID deve ser o ID do FORNECEDOR no ERP.
         */
        if (empty($orderReceived)) {
            $orderReceived = [
                "clientID"      => 20,      // SupplierID
                "wharehouseID"  => 1,
                "salesmanID"    => 1,
                "paymentID"     => 1,
                "tenderID"      => 1,
                "unloadAddress1"=> "",
                "transDocument" => "FGR",   // doc de  ENTRADA
                "comments"      => "Documento de teste via API — 5 lotes do mesmo artigo",
                "lines" => [
                    [
                        "itemID"       => "NW005052",
                        "quantity"     => 12,
                        "price"        => 5.99,
                        "unitOfSaleID" => "UNI",
                        "propriedade1" => "ABCD123",
                        "validade1"    => "2026-01-31",
                        "colorID"      => 0,
                        "sizeID"       => 0,
                    ],
                    [
                        "itemID"       => "NW005052",
                        "quantity"     => 8,
                        "price"        => 5.99,
                        "unitOfSaleID" => "UNI",
                        "propriedade1" => "ABCD124",
                        "validade1"    => "2026-06-30",
                        "colorID"      => 0,
                        "sizeID"       => 0,
                    ],
                    [
                        "itemID"       => "NW005052",
                        "quantity"     => 15,
                        "price"        => 5.99,
                        "unitOfSaleID" => "UNI",
                        "propriedade1" => "ABCD125",
                        "validade1"    => "2026-12-31",
                        "colorID"      => 0,
                        "sizeID"       => 0,
                    ],
                    [
                        "itemID"       => "NW005052",
                        "quantity"     => 20,
                        "price"        => 5.99,
                        "unitOfSaleID" => "UNI",
                        "propriedade1" => "ABCD126",
                        "validade1"    => "2027-06-30",
                        "colorID"      => 0,
                        "sizeID"       => 0,
                    ],
                    [
                        "itemID"       => "NW005052",
                        "quantity"     => 5,
                        "price"        => 5.99,
                        "unitOfSaleID" => "UNI",
                        "propriedade1" => "ABCD127",
                        "validade1"    => "2027-12-31",
                        "colorID"      => 0,
                        "sizeID"       => 0,
                    ],
                ],
            ];
        }

        // Montar payload final
        //"transactionTaxIncluded" => false, tem de ser dinamico e de acordo com a fatura 
        // para já vamos so verificar se o fornecedor era com tax ou nao
        // futuramente validamos e recalculamos os produtos se fornecedor mudar de true para false e nao bater certo
        // com o documento anterior

        $payload = [
            "clientID"               => $orderReceived['clientID']     ?? 20,
            "wharehouseID"           => $orderReceived['wharehouseID'] ?? 1,
            "transDocument"          => $orderReceived['transDocument'] ?? "FGR",
            "transactionTaxIncluded" => false,
            "comments"               => $orderReceived['comments'] ?? "",
        ];

        // (Opcional mas útil) envia também estes se vierem
        if (isset($orderReceived['paymentID']))  { $payload['paymentID']  = (int) $orderReceived['paymentID']; }
        if (isset($orderReceived['tenderID']))   { $payload['tenderID']   = (int) $orderReceived['tenderID']; }
        if (isset($orderReceived['salesmanID'])) { $payload['salesmanID'] = (int) $orderReceived['salesmanID']; }

        // Normalizar e garantir tipos nas linhas
        $payload['lines'] = array_map(function ($l) {
            return [
                "itemID"       => $l['itemID'],
                "quantity"     => (float) $l['quantity'],
                "price"        => (float) $l['price'],
                "DiscountPercent" => (float) ($l['DiscountPercent'] ?? 0),
                "unitOfSaleID" => $l['unitOfSaleID'] ?? "UNI",
                "propriedade1" => $l['propriedade1'] ?? null,   // obrigatório se o artigo usa propriedades
                "validade1"    => $l['validade1'] ?? null,      // YYYY-MM-DD
                "colorID"      => (int) ($l['colorID'] ?? 0),
                "sizeID"       => (int) ($l['sizeID'] ?? 0),
            ];
        }, $orderReceived['lines'] ?? []);

        
        // Enviar request
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30)
                ->asJson()
                ->post($url, $payload);

            $json = null;
            try { $json = $response->json(); } catch (\Throwable $ignore) {}

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status'  => $response->status(),
                    'data'    => $json,
                    'payload' => $payload, // debug
                ];
            }

            return [
                'success' => false,
                'status'  => $response->status(),
                'error'   => $json['message'] ?? (string)$response->body(),
                'payload' => $payload,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'status'  => 0,
                'error'   => 'Erro: ' . $e->getMessage(),
                'payload' => $payload,
            ];
        }
    }


    // Ler a taxa de iva associada ao produto
    public function getProductTaxRate(string $value, string $taxGroup = 'IVA'): ?float
    {
        $value   = trim($value);
        $escaped = str_replace("'", "''", $value);
        $taxGrp  = str_replace("'", "''", $taxGroup);

        $query = "
            SELECT TOP 1
                CAST(tt.TaxRate AS FLOAT) AS TaxRate
            FROM dbo.Item i
            JOIN dbo.TaxableGroupRules tgr
                ON tgr.TaxableGroupID = i.TaxableGroupID
            JOIN dbo.TaxTable tt
                ON tt.TaxGroupID   = tgr.TaxGroupID
            AND tt.TaxSequenceID = tgr.TaxSequenceID
            WHERE (i.ItemID  = '{$escaped}'
                OR i.BarCode = '{$escaped}'
                OR i.ItemID IN (
                    SELECT pi.ItemID
                    FROM dbo.POSIdentity pi
                    WHERE pi.POSItemID = '{$escaped}'
                ))
            AND tgr.TaxGroupID = '{$taxGrp}'
            AND tt.CountryID   = 'PRT'
            AND tt.TaxRegionID = 'CON'
            AND (tt.TaxExpirationDate IS NULL OR tt.TaxExpirationDate >= GETDATE())
            ORDER BY tt.TaxSequenceID
        ";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type'  => 'application/json',
        ])
        ->withoutVerifying()
        ->post($this->endpoint, ['query' => $query]);

        if (!$response->successful() || empty($response['data'][0]['TaxRate'])) {
            return null;
        }

        return (float) $response['data'][0]['TaxRate'];
    }

    // não usados. metodos para ir buscar diretamente sempre as marcas e fornecedores
    // public function getErpSuppliers()
    // {
    //     try {
    //         $query = 'SELECT SupplierID, OrganizationName FROM dbo.Supplier ORDER BY OrganizationName';

    //         $response = $this->postQuery($query);

    //         if (!$response || !isset($response['data'])) {
    //             return collect();
    //         }

    //         return collect($response['data'])->map(function ($supplier) {
    //             return [
    //                 'id' => $supplier['SupplierID'],
    //                 'name' => $supplier['OrganizationName'],
    //             ];
    //         });
    //     } catch (\Exception $e) {
    //         return collect();
    //     }
    // }

    // public function getErpBrands()
    // {
    //     try {
    //         $query = 'SELECT FamilyID, Description FROM dbo.Family ORDER BY Description';

    //         $response = $this->postQuery($query);

    //         if (!$response || !isset($response['data'])) {
    //             return collect();
    //         }

    //         return collect($response['data'])->map(function ($brand) {
    //             return [
    //                 'id' => $brand['FamilyID'],
    //                 'name' => $brand['Description'],
    //             ];
    //         });
    //     } catch (\Exception $e) {
    //         return collect();
    //     }
    // }

    // protected function postQuery(string $query)
    // {
    //     return Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $this->token,
    //         'Content-Type' => 'application/json',
    //     ])
    //     ->withoutVerifying()
    //     ->timeout(8)
    //     ->post($this->endpoint, ['query' => $query])
    //     ->json();
    // }

    public function syncErpData(): void
    {
        $endpoint = 'http://nuterra.dyndns.biz:45248/api_warehouse/v1/api_sage_sql_read.php';
        $token = 'Qv1FC5Olt4DDFu7H6vKFUn0pvTT!n/!W8QLpY9JXBJA=p0uWVSPECJOY?Qn?MHJHZmAKscV8Syqb6QH58DV//Uelema=g0RKNFoZa?-qW3Fytp3IGfTHcs/EiGGEq/09zkIg9P=O6/qWfYyHSe/x=8g0fCVowY?!ncAlWxc-143u8ZG3qKQiAlQa6d7DUe=Rhz1HZafXnHWBhtQylHUfEC2fK9AMrj717BqX1h7WakVAD3BPK9DSq0X4LsYb/BM7';

        $queries = [
            'suppliers' => 'SELECT SupplierID, OrganizationName FROM dbo.Supplier ORDER BY OrganizationName',
            'brands'    => 'SELECT FamilyID, Description FROM dbo.Family ORDER BY Description',
        ];

        foreach ($queries as $type => $query) {
            try {
                $res = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type'  => 'application/json',
                ])
                ->withoutVerifying()
                ->post($endpoint, ['query' => $query]);

                if (!$res->successful() || !isset($res['data'])) {
                    continue;
                }

                foreach ($res['data'] as $row) {
                    if ($type === 'suppliers') {
                        Supplier::updateOrCreate(
                            ['erp_id' => $row['SupplierID']],
                            ['name' => $row['OrganizationName']]
                        );
                    } elseif ($type === 'brands') {
                        Brand::updateOrCreate(
                            ['erp_id' => $row['FamilyID']],
                            ['name' => $row['Description']]
                        );
                    }
                }
            } catch (\Exception $e) {
                // Podes logar ou ignorar falhas silenciosamente
                // logger($e->getMessage());
                continue;
            }
        }
    }
public function getLastBuyConditions(string $itemId): ?array
{
    // 1) Buscar os 3 campos na ItemCostChange
    $query1 = "
        SELECT LastTransSerial, LastTransDocument, LastTransDocNumber
        FROM dbo.ItemCostChange
        WHERE ItemID = '{$itemId}'
    ";

    $res1 = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type'  => 'application/json',
        ])
        ->withoutVerifying()
        ->post($this->endpoint, ['query' => $query1]);

    if (!$res1->successful() || empty($res1['data'][0])) {
        return null;
    }

    // Usa a primeira linha devolvida
    $serial    = $res1['data'][0]['LastTransSerial'];
    $document  = $res1['data'][0]['LastTransDocument'];
    $docNumber = $res1['data'][0]['LastTransDocNumber'];

    // 2) Buscar Units, UnitPrice, DiscountPercent na BuyTransactionDetails
    $query2 = "
        SELECT Units, UnitPrice, DiscountPercent
        FROM dbo.BuyTransactionDetails
        WHERE ItemID = '{$itemId}'
          AND TransSerial   = '{$serial}'
          AND TransDocument = '{$document}'
          AND TransDocNumber= '{$docNumber}'
    ";

    $res2 = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type'  => 'application/json',
        ])
        ->withoutVerifying()
        ->post($this->endpoint, ['query' => $query2]);

    if (!$res2->successful() || empty($res2['data'][0])) {
        return null;
    }
        
    // dd([
    //     'status' => $res2->status(),
    //     'body'   => $res2->body(),     // string JSON crua
    //     'json'   => $res2->json(),     // array decodificado
    // ]);

    // Retorna exatamente os campos pedidos
    return [
        'Units'           => $res2['data'][0]['Units'],
        'UnitPrice'       => $res2['data'][0]['UnitPrice'],
        'DiscountPercent' => $res2['data'][0]['DiscountPercent'],
    ];
}


public function lastBuyConditions(string $itemId)
{
    $data = $this->getLastBuyConditions($itemId);

    return response()->json([
        'success' => (bool) $data,
        'data'    => $data,
    ]);
}

}
