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

        $allowedSorts = ['StockQty', 'SuggestedQty', 'ProductName'];
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
        'search'      => $request->input('search'),
        'start_date'  => $startDate,
        'end_date'    => $endDate,
    ];


        if ($filters['supplier_id'] || $filters['brand_id'] || $filters['search']) {
            $products = $erpController->getProducts(new Request($filters));

            
            // Só ordena se houver produtos
            if (in_array($sort, ['StockQty', 'SuggestedQty']) && $products->isNotEmpty()) {
                $products = $direction === 'desc'
                    ? $products->sortByDesc($sort)
                    : $products->sortBy($sort);
            }

            elseif ($sort === 'ProductName' && $products->isNotEmpty()) {
                // Mapeamento de acentos para equivalente sem acento
                $normalize = function ($string) {
                    $map = [
                        'Á'=>'A','À'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A',
                        'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
                        'É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E',
                        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
                        'Í'=>'I','Ì'=>'I','Î'=>'I','Ï'=>'I',
                        'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
                        'Ó'=>'O','Ò'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O',
                        'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
                        'Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U',
                        'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
                        'Ç'=>'C','ç'=>'c',
                        'Ñ'=>'N','ñ'=>'n',
                    ];

                    return strtr(mb_strtolower($string), $map);
                };

                $products = $direction === 'desc'
                    ? $products->sortByDesc(fn ($p) => $normalize($p['ProductName']))
                    : $products->sortBy(fn ($p) => $normalize($p['ProductName']));
            }



        }

      

        return view('layouts.admin.lists.index', compact(
            'products', 'suppliers', 'brands', 'filters', 'startDate', 'endDate', 'sort', 'direction'
        ));
    }

    public function single(Request $request, ErpController $erpController)
    {
        $product = null;

        if ($request->filled('search')) {
            $product = $erpController->getProductBySkuOrBarcode($request->search);
            if ($product) {
                return view('layouts.admin.lists.single', compact('product'));
            } else {
                return redirect()->route('lists.single')
                    ->with('error', 'Produto não encontrado.');
            }
        }

        return view('layouts.admin.lists.single');
    }


}
