<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Supplier;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Receiving;
use App\Models\Admin\ReceivingItem;
use App\Models\Admin\ReceivingBatch;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceivingController extends Controller
{
    public function dashboard(Request $request)
    {
        $hasPendingReceivings = Receiving::whereNull('received_at')->exists(); // ou status_id = 7
        $hasActiveOrders = Order::where('status_id',1)->exists();

        return view('layouts.admin.receivings.dashboard', compact('hasPendingReceivings', 'hasActiveOrders'));
    }


    public function index()
    {
        $orders = Order::with(['items.supplier'])
            ->whereHas('status', fn($q) => $q->where('code', 'active'))
            ->orderByDesc('created_at')
            ->paginate(paginationPerPage());

        return view('layouts.admin.receivings.index', compact('orders'));
    }

    public function startReceiving(Order $order)
    {
        try {
            $suppliers = $order->items
                ->filter(fn($item) => $item->supplier)
                ->groupBy('supplier_id');

            foreach ($suppliers as $supplierId => $items) {
                $receiving = Receiving::firstOrCreate(
                    [
                        'order_id'    => $order->id,
                        'supplier_id' => $supplierId,
                    ],
                    [
                        'started_at' => now(),
                        'status_id'  => 7, // em curso
                    ]
                );

                foreach ($items as $item) {
                    ReceivingItem::firstOrCreate(
                        [
                            'receiving_id'  => $receiving->id,
                            'order_item_id' => $item->id,
                        ],
                        [
                            'product_sku'     => $item->product_sku,
                            'product_name'    => $item->product_name,
                            'product_barcode' => $item->product_barcode,
                            'supplier_id'     => $item->supplier_id,
                            'brand_id'        => $item->brand_id,
                            'ordered_qty'     => $item->quantity,
                            'received_qty'    => $item->quantity,
                            'notes'           => null,
                        ]
                    );
                }
            }

            return redirect()->route('receivings.index')
                ->with('success', 'Entrada de mercadorias iniciada!');
        } catch (\Throwable $e) {
            return redirect()->route('receivings.index')
                ->with('error', 'Ocorreu um erro, Contacte o suporte.');
        }
    }



    public function pending()
    {
        $receivings = Receiving::with(['order', 'supplier', 'items'])
            ->where('status_id', 7) // em curso
            ->latest()
            ->get();

        return view('layouts.admin.receivings.pending', compact('receivings'));
    }

    public function showForm(Order $order, Supplier $supplier)
    {
        $receiving = Receiving::where('order_id', $order->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        $items = $receiving->items()->with(['orderItem', 'batches'])->get();

        return view('layouts.admin.receivings.form', compact('order', 'supplier', 'receiving', 'items'));
    }

    public function store(Request $request, Order $order, Supplier $supplier)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $receiving = Receiving::where('order_id', $order->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        DB::transaction(function () use ($receiving, $validated) {
            foreach ($receiving->items as $item) {
                $totalQty = $item->batches()->sum('quantity');
                $item->update([
                    'received_qty' => $totalQty,
                ]);
            }

            $receiving->update([
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('receivings.form', ['order' => $receiving->order_id, 'supplier' => $receiving->supplier_id])
            ->with('success', 'Receção guardada com sucesso.');
    }


    public function finalize(Request $request, Order $order, Supplier $supplier)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $receiving = Receiving::where('order_id', $order->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        DB::transaction(function () use ($receiving, $validated) {
            foreach ($receiving->items as $item) {
                $totalQty = $item->batches()->sum('quantity');

                $item->update([
                    'received_qty' => $totalQty ?? 0, // Se não houver lotes, grava 0
                ]);
            }

            $receiving->update([
                'notes' => $validated['notes'] ?? null,
                'received_at' => now(),
                'received_by' => auth()->id(),
                'status_id' => 5, // archived
            ]);
        });

        return redirect()
            ->route('receivings.pending')
            ->with('success', 'Receção finalizada e arquivada com sucesso.');
    }


    // GET: formulário de pesquisa para adicionar produto extra à receção
    public function searchSingle(Request $request, Receiving $receiving, ErpController $erpController)
    {
        $product = null;

        if ($request->filled('search')) {
            $product = $erpController->getProductBySkuOrBarcode($request->search);

            if (!$product) {
                return redirect()
                    ->route('receivings.items.single', $receiving->id)
                    ->with('error', 'Produto não encontrado.');
            }
        }

        return view('layouts.admin.receivings.single', compact('product', 'receiving'));
    }

    // GET: formulário de pesquisa para adicionar produto com scanner
    public function searchSingleScanner(Request $request, Receiving $receiving, ErpController $erpController)
    {
        $product = null;

        if ($request->filled('search')) {
            $product = $erpController->getProductBySkuOrBarcode($request->search);

            if (!$product) {
                return redirect()
                    ->route('receivings.items.singleScanner', $receiving->id)
                    ->with('error', 'Produto não encontrado.');
            }
        }

        return view('layouts.admin.receivings.singleScanner', compact('product', 'receiving'));
    }

    public function addSingle(Request $request, Receiving $receiving)
    {
        $request->validate([
            'item_sku'      => 'required|string',
            'quantity'      => 'required|integer|min:1',
            'product_name'  => 'required|string',
            'batch_number'  => 'required|string|max:255',
            'expiry_date'   => 'required|date|after:today',
        ]);

        $exists = ReceivingItem::where('receiving_id', $receiving->id)
            ->where('product_sku', $request->item_sku)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Este produto já foi adicionado à receção.');
        }

        DB::transaction(function () use ($request, $receiving) {
            $item = ReceivingItem::create([
                'receiving_id'      => $receiving->id,
                'product_sku'       => $request->item_sku,
                'product_name'      => $request->product_name,
                'product_barcode'   => $request->bar_code ?? null,
                'supplier_id'       => $request->supplier_id ?? null,
                'brand_id'          => $request->brand_id ?? null,
                'ordered_qty'       => 0,
                'received_qty'      => $request->quantity,
            ]);

            $item->batches()->create([
                'quantity'     => $request->quantity,
                'batch_number' => $request->batch_number,
                'expiry_date'  => $request->expiry_date,
            ]);
        });

        return redirect()
            ->route('receivings.form', [
                'order' => $receiving->order_id,
                'supplier' => $receiving->supplier_id,
            ])
            ->with('success', 'Produto e lote adicionados com sucesso.');
    }

    public function addSingleScanner(Request $request, Receiving $receiving)
    {
        $request->validate([
            'item_sku'      => 'required|string',
            'quantity'      => 'required|integer|min:1',
            'product_name'  => 'required|string',
            'batch_number'  => 'required|string|max:255',
            'expiry_date'   => 'required|date|after:today',
        ]);

        DB::transaction(function () use ($request, $receiving) {
            // Tenta encontrar o item já existente com mesmo SKU
            $item = ReceivingItem::where('receiving_id', $receiving->id)
                ->where('product_sku', $request->item_sku)
                ->first();

            if ($item) {
                // Se já existe, apenas adiciona novo lote e soma quantidade
                $item->batches()->create([
                    'quantity'     => $request->quantity,
                    'batch_number' => $request->batch_number,
                    'expiry_date'  => $request->expiry_date,
                ]);

                // Atualiza a quantidade total recebida
                $totalQty = $item->batches()->sum('quantity');
                $item->update(['received_qty' => $totalQty]);
            } else {
                // Cria novo item e primeiro lote
                $item = ReceivingItem::create([
                    'receiving_id'      => $receiving->id,
                    'product_sku'       => $request->item_sku,
                    'product_name'      => $request->product_name,
                    'product_barcode'   => $request->bar_code ?? null,
                    'supplier_id'       => $request->supplier_id ?? null,
                    'brand_id'          => $request->brand_id ?? null,
                    'ordered_qty'       => 0,
                    'received_qty'      => $request->quantity,
                ]);

                $item->batches()->create([
                    'quantity'     => $request->quantity,
                    'batch_number' => $request->batch_number,
                    'expiry_date'  => $request->expiry_date,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Item inserido com sucesso.');
    }


    public function destroyItem(ReceivingItem $item)
    {
        // Só permite apagar se for um item extra (adicionado manualmente)
        if ($item->order_item_id !== NULL) {
            return redirect()->back()->with('error', 'Não é possível remover itens que pertencem ao pedido original.');
        }

        $item->delete();

        return redirect()->back()->with('success', 'Item removido com sucesso.');
    }

    public function destroyBatch(ReceivingBatch $batch)
    {
        try {
            DB::transaction(function () use ($batch) {
                $item = $batch->receivingItem;

                // Eliminar o lote
                $batch->delete();

                // Recalcular quantidade total dos lotes restantes
                $newTotal = $item->batches()->sum('quantity');

                // Atualizar campo received_qty
                $item->update(['received_qty' => $newTotal]);
            });

            return redirect()->back()->with('success', 'Lote eliminado e quantidade atualizada.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erro ao eliminar o lote.');
        }
    }



    public function destroy(Receiving $receiving)
    {
        try {
            DB::transaction(function () use ($receiving) {
                $allToDelete = Receiving::where('order_id', $receiving->order_id)
                    ->whereNull('received_at')
                    ->get();

                foreach ($allToDelete as $rec) {
                    $rec->items()->delete();
                    $rec->delete(); // soft delete
                }
            });

            return redirect()->back()->with('success', 'Entradas de mercadorias eliminadas com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }


    public function storeBatch(Request $request, ReceivingItem $item)
    {
        $validated = $request->validate([
            'quantity'      => 'required|integer|min:1',
            'batch_number'  => 'required|string|max:255',
            'expiry_date'   => 'required|date|after:today',
        ]);

        try {
            $item->batches()->create([
                'quantity'     => $validated['quantity'],
                'batch_number' => $validated['batch_number'],
                'expiry_date'  => $validated['expiry_date'],
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao guardar o lote.' . $e], 500);
        }
    }

}
