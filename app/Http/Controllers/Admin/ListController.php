<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ListController extends Controller
{
    public function index(Request $request, ErpController $erpController)
    {
        $defaultStart = Carbon::yesterday()->subDays(29)->format('Y-m-d');
        $defaultEnd = Carbon::yesterday()->format('Y-m-d');

        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', $defaultEnd);

        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $products = collect();

        $allowedSorts = ['StockQty', 'SuggestedQty'];
        $sort = in_array($request->input('sort'), $allowedSorts) ? $request->input('sort') : null;
        $direction = in_array($request->input('direction'), ['asc', 'desc']) ? $request->input('direction') : 'asc';

        // Obter erp_id dos filtros
        $supplierErpId = null;
        $brandErpId = null;

        if ($request->filled('supplier_id')) {
            $supplierErpId = Supplier::find($request->input('supplier_id'))?->erp_id;
        }

        if ($request->filled('brand_id')) {
            $brandErpId = Brand::find($request->input('brand_id'))?->erp_id;
        }

        $filters = [
        'supplier_id' => $request->input('supplier_id'),
        'brand_id'    => $request->input('brand_id'),
        'start_date'  => $startDate,
        'end_date'    => $endDate,
    ];


        if ($filters['supplier_id'] || $filters['brand_id']) {
            $products = $erpController->getProducts(new Request($filters));

            // Só ordena se houver produtos
            if (in_array($sort, ['StockQty', 'SuggestedQty']) && $products->isNotEmpty()) {
                $products = $direction === 'desc'
                    ? $products->sortByDesc($sort)
                    : $products->sortBy($sort);
            }

        }

        return view('layouts.admin.lists.index', compact(
            'products', 'suppliers', 'brands', 'filters', 'startDate', 'endDate', 'sort', 'direction'
        ));
    }

}
