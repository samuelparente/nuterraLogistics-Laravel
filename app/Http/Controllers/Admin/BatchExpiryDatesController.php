<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Brand;
use App\Models\Admin\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BatchExpiryDatesController extends Controller
{
    // Mesma ligação do ErpController (hardcoded)
    protected string $endpoint = 'http://nuterra.dyndns.biz:45248/api_warehouse/v1/api_sage_sql_read.php';
    protected string $token    = 'Qv1FC5Olt4DDFu7H6vKFUn0pvTT!n/!W8QLpY9JXBJA=p0uWVSPECJOY?Qn?MHJHZmAKscV8Syqb6QH58DV//Uelema=g0RKNFoZa?-qW3Fytp3IGfTHcs/EiGGEq/09zkIg9P=O6/qWfYyHSe/x=8g0fCVowY?!ncAlWxc-143u8ZG3qKQiAlQa6d7DUe=Rhz1HZafXnHWBhtQylHUfEC2fK9AMrj717BqX1h7WakVAD3BPK9DSq0X4LsYb/BM7';

    public function index(Request $request)
    {
        // ---------
        // Filtros
        // ---------
        $months      = (int) ($request->input('months', 6));
        $months      = max(1, min($months, 120)); // safety

        $warehouseId = (int) ($request->input('warehouse_id', 1));
        $warehouseId = max(1, $warehouseId);

        $supplierErpId = $request->filled('supplier_id')
            ? Supplier::find($request->supplier_id)?->erp_id
            : null;

        $brandErpId = $request->filled('brand_id')
            ? Brand::find($request->brand_id)?->erp_id
            : null;

        $search = trim((string) $request->input('search', ''));
        $searchEscaped = str_replace("'", "''", $search);

        // datas (vamos passar como string para a query)
        $today = Carbon::today()->format('Y-m-d');

        // --------------------------------------------
        // Query (ATENÇÃO: endpoint só permite SELECT)
        // => nada de DECLARE, nada de ; múltiplos
        // --------------------------------------------
        $where = [];
        $where[] = "sp.WarehouseID = {$warehouseId}";
        $where[] = "sp.PropertyValue1 IS NOT NULL";
        $where[] = "sp.ExpirationDate IS NOT NULL";
        $where[] = "sp.PhysicalQty > 0";

        // período
        $where[] = "CAST(sp.ExpirationDate AS DATE) >= '{$today}'";
        $where[] = "CAST(sp.ExpirationDate AS DATE) <= DATEADD(MONTH, {$months}, '{$today}')";

        // fornecedor/marca (assumindo Item.SupplierID / Item.FamilyID)
        if ($supplierErpId) {
            $where[] = "i.SupplierID = {$supplierErpId}";
        }
        if ($brandErpId) {
            $where[] = "i.FamilyID = {$brandErpId}";
        }

        // pesquisa por SKU / nome
        if ($search !== '') {
            $where[] = "(i.ItemID LIKE '%{$searchEscaped}%' OR LOWER(n.Description) LIKE LOWER('%{$searchEscaped}%'))";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $query = "
            SELECT
                sp.ItemID,
                n.Description AS ProductName,
                sp.PropertyValue1 AS BatchNumber,
                CAST(sp.ExpirationDate AS DATE) AS ExpirationDate,
                CAST(sp.PhysicalQty AS INT) AS StockQty,
                DATEDIFF(DAY, '{$today}', CAST(sp.ExpirationDate AS DATE)) AS DaysLeft
            FROM dbo.StockProperty sp
            LEFT JOIN dbo.ItemNames n ON n.ItemID = sp.ItemID
            LEFT JOIN dbo.Item i ON i.ItemID = sp.ItemID
            {$whereClause}
            ORDER BY CAST(sp.ExpirationDate AS DATE) ASC, sp.ItemID ASC, sp.PropertyValue1 ASC
        ";

        $products = collect();

        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
            ])
                ->withoutVerifying()
                ->post($this->endpoint, ['query' => $query]);

            $data = collect($res['data'] ?? []);

            // Normalizar a data (algumas vezes vem em array {date:...})
            $products = $data->map(function ($row) {
                $exp = $row['ExpirationDate'] ?? null;

                // vem array? ({"date":"2026-...","timezone"...})
                if (is_array($exp) && isset($exp['date'])) {
                    $row['ExpirationDate'] = substr((string) $exp['date'], 0, 10);
                } else {
                    // string "2026-06-30..." ou "2026-06-30"
                    $row['ExpirationDate'] = $exp ? substr((string) $exp, 0, 10) : null;
                }

                $row['DaysLeft'] = isset($row['DaysLeft']) ? (int) $row['DaysLeft'] : null;
                $row['StockQty'] = isset($row['StockQty']) ? (int) $row['StockQty'] : 0;

                return $row;
            });

        } catch (\Throwable $e) {
            $products = collect();
        }

        // Agrupar para facilitar a view (por SKU)
        $grouped = $products->groupBy('ItemID');

        // dropdowns iguais ao teu padrão (id + name)
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get()->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
        ]);

        $brands = Brand::select('id', 'name')->orderBy('name')->get()->map(fn($b) => [
            'id' => $b->id,
            'name' => $b->name,
        ]);

        $filters = [
            'search'      => $search,
            'supplier_id' => (string) $request->input('supplier_id', ''),
            'brand_id'    => (string) $request->input('brand_id', ''),
            'months'      => $months,
            'warehouse_id'=> $warehouseId,
        ];

        return view('layouts.admin.batchesexpirydates.index', compact(
            'grouped',
            'products',
            'suppliers',
            'brands',
            'filters'
        ));
    }

    public function countAlert(Request $request)
    {
        // se já fechou, nem calcula
        if ($request->cookie('hide_expiry_alert') === '1') {
            return response()->json(['success' => true, 'count' => 0, 'months' => 0]);
        }

        $warehouseId = 1;
        $monthsAhead = (int) ($request->get('months', 6));
        $monthsAhead = max(1, min(120, $monthsAhead));

        $query = "
            SELECT COUNT(1) AS Total
            FROM dbo.StockProperty sp
            WHERE sp.WarehouseID = {$warehouseId}
              AND sp.PropertyValue1 IS NOT NULL
              AND sp.ExpirationDate IS NOT NULL
              AND sp.PhysicalQty > 0
              AND CAST(sp.ExpirationDate AS DATE) >= CAST(GETDATE() AS DATE)
              AND CAST(sp.ExpirationDate AS DATE) <= CAST(DATEADD(MONTH, {$monthsAhead}, GETDATE()) AS DATE)
        ";

        $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(20)
            ->post($this->endpoint, ['query' => $query]);

        $count = 0;
        if ($res->successful()) {
            $total = $res->json('data.0.Total');
            $count = is_numeric($total) ? (int) $total : 0;
        }

        return response()->json([
            'success' => true,
            'count'   => $count,
            'months'  => $monthsAhead,
        ]);
    }

    public function hideAlert(Request $request)
    {
        // minutos até ao fim do dia (para a cookie expirar à meia-noite)
        $minutes = max(1, (int) ceil(now()->diffInSeconds(now()->endOfDay()) / 60));

        return response()->json(['success' => true])
            ->cookie(
                'hide_expiry_alert', // nome
                '1',                 // valor
                $minutes             // duração em minutos
            );
    }
}
