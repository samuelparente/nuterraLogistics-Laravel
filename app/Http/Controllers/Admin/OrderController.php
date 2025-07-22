<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bonus;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{

    public function dashboard(Request $request)
    {
        $openOrder = Order::whereHas('status', function ($query) {
            $query->where('code', 'pending');
        })->first();

        return view('layouts.admin.orders.dashboard', compact('openOrder'));
    }


    public function edit(Request $request)
    {
        $order = Order::whereHas('status', function ($query) {
            $query->where('code', 'pending');
        })->with(['items.brand'])->first();

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Nenhum pedido em aberto encontrado.');
        }

        return view('layouts.admin.orders.edit', compact('order'));
    }

    public function createEmpty(Request $request)
    {
        // Verifica se já existe uma order pendente para o utilizador
        $existingOrder = Order::where('status_id', 3) // 3 = pendente
            ->whereNull('deleted_at') // ignora eliminadas
            ->first();

        if ($existingOrder) {
            return redirect()->back()->with('error', 'Já existe um pedido pendente.');
        }

        $order = Order::create([
            'status_id' => 3, //  "pendente"
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pedido aberto com sucesso!');

    }

    public function addItems(Request $request)
    {
        try {
            $request->validate([
                'products' => 'required|array|min:1',
                'products.*.item_id' => 'required|string',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $openOrder = Order::whereHas('status', fn($q) => $q->where('code', 'pending'))->first();

            if (!$openOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não existe pedido em aberto para adicionar produtos.',
                ], 400);
            }

            $alreadyInCart = [];
            $isMassAddition = $request->boolean('mass');

            foreach ($request->products as $product) {
                $existingItem = OrderItem::where('order_id', $openOrder->id)
                    ->where('product_sku', $product['item_id'])
                    ->first();

                if ($existingItem) {
                    if (!$isMassAddition) {
                        $alreadyInCart[] = $product['item_id'];
                    }
                    continue; // ignora se já existe
                }

                OrderItem::create([
                    'order_id' => $openOrder->id,
                    'product_sku' => $product['item_id'],
                    'quantity' => (int) $product['quantity'],
                    'product_barcode' => $product['bar_code'] ?? null,
                    'product_name' => $product['product_name'] ?? null,
                    'supplier_id' => $product['supplier_id'] ?? null,
                    'brand_id' => $product['brand_id'] ?? null,
                ]);
            }

            if (count($alreadyInCart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este produto já existe no pedido actual.',
                ], 200); // ou 409 se quiseres sinalizar conflito
            }

            
            return response()->json([
                'success' => true,
                'message' => 'Produto(s) adicionados ao pedido com sucesso.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function cartItemCount()
    {
        $openOrder = Order::withCount('orderItems')
            ->whereHas('status', fn($q) => $q->where('code', 'pending'))
            ->first();

        return response()->json([
            'count' => $openOrder?->order_items_count ?? 0,
        ]);
    }

    public function destroyItem(OrderItem $item)
    {
        $item->delete();

        return back()->with('success', 'Item removido do pedido com sucesso.');
    }

    public function destroy(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                // Eliminar os items da ordem
                //$order->orderItems()->delete();

                // Eliminar a ordem
                $order->delete();
            });

            return redirect()->route('orders.dashboard')->with('success', 'Pedido eliminado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao eliminar o pedido: ' . $e->getMessage());
        }
    }

}
