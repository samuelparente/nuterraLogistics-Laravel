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

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        $startDate = $request->start_date ?? now()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

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
            ORDER BY i.ItemID
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
                $item['HasBonus'] = $bonus !== null;
                $item['BonusName'] = $bonus?->name;
                $item['BonusDescription'] = $bonus?->description;

                return $item;
            });

        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getProductBySkuOrBarcode(string $value, array $filters = [])
    {
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
            WHERE i.ItemID = '{$escaped}' OR i.BarCode = '{$escaped}'
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

        $bonus = $bonuses->first(fn ($b) => $b->brand_id === $brandId)
            ?? $bonuses->first(fn ($b) => $b->supplier_id === $supplierId);

        $product['HasBonus'] = $bonus !== null;
        $product['BonusName'] = $bonus?->name;
        $product['BonusDescription'] = $bonus?->description;

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

        return $product;
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


}
