<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bonus;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $query = Bonus::with(['supplier', 'brand'])->latest();

        // Filtros
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $bonuses = $query->paginate(15)->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('layouts.admin.bonuses.index', compact('bonuses', 'suppliers', 'brands'));
    }


    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        return view('layouts.admin.bonuses.create', compact('suppliers', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes'       => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id'    => 'nullable|exists:brands,id',
        ]);

        Bonus::create($request->all());

        return redirect()->route('bonuses.index')->with('success', 'Registo criado com sucesso.');
    }

    public function edit(Bonus $bonus) 
    {
        
        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        return view('layouts.admin.bonuses.edit', compact('bonus', 'suppliers', 'brands'));
    }

    public function update(Request $request, Bonus $bonus)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes'       => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id'    => 'nullable|exists:brands,id',
        ]);

        
        $bonus->update($request->all());

        return redirect()->route('bonuses.index')->with('success', 'Registo actualizado com sucesso.');
    }

    public function destroy(Bonus $bonus)
    {
        $bonus->delete();
        return redirect()->route('bonuses.index')->with('success', 'Registo eliminado com sucesso.');
    }
}
