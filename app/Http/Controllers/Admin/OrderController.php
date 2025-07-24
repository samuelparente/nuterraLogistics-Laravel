<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bonus;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\Status;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderFilesMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    public function dashboard(Request $request)
    {
        $openOrder = Order::whereHas('status', function ($query) {
            $query->where('code', 'pending');
        })->first();

        return view('layouts.admin.orders.dashboard', compact('openOrder'));
    }

    public function index(Request $request)
    {
        try {
            $statuses = Status::all(); // Estados disponíveis para filtro

            $orders = Order::with(['items.supplier', 'status']) // Carrega também os fornecedores
                ->when($request->status, fn($q) => $q->where('status_id', $request->status))
                ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
                ->orderByDesc('created_at')
                ->paginate(paginationPerPage())
                ->appends($request->only(['status', 'date'])); // Mantém os filtros ao navegar

            return view('layouts.admin.orders.index', compact('orders', 'statuses'));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }


    public function downloadFile(Request $request)
    {
        $relativePath = $request->query('path');

        // Segurança contra caminhos maliciosos
        if (!$relativePath || Str::contains($relativePath, ['..', './', '//'])) {
            abort(400, 'Caminho inválido');
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($relativePath)) {
            abort(404, 'Ficheiro não encontrado');
        }

        $absolutePath = $disk->path($relativePath);
        $filename = basename($relativePath);

        return response()->download($absolutePath, $filename);
    }

    public function edit(Request $request)
    {
        $order = Order::whereHas('status', fn ($q) => $q->where('code', 'pending'))
            ->with(['items.brand.bonuses', 'items.supplier.bonuses'])
            ->first();

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Nenhum pedido em aberto encontrado.');
        }

        foreach ($order->items as $item) {
            $brandBonuses = $item->brand?->bonuses;
            $supplierBonuses = $item->supplier?->bonuses;

            if ($brandBonuses && $brandBonuses->isNotEmpty()) {
                // Prioriza o primeiro bónus da marca
                $item->bonusLabel = $brandBonuses->first()?->name;
                $item->bonusTooltip = $brandBonuses->first()?->description ?? 'Sem descrição';
            } elseif ($supplierBonuses && $supplierBonuses->isNotEmpty()) {
                // Caso não exista bónus da marca, junta todos os do fornecedor
                $item->bonusLabel = 'Bónus Fornecedor';
                $item->bonusTooltip = $supplierBonuses->pluck('name')->implode(', ');
            } else {
                // Nenhum bónus
                $item->bonusLabel = null;
                $item->bonusTooltip = null;
            }
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

    public function addSingle(Request $request)
    {
        try {
            $request->validate([
                'item_sku' => 'required|string',
                'quantity' => 'required|integer|min:1',
            ]);

            $openOrder = Order::whereHas('status', fn($q) => $q->where('code', 'pending'))->first();

            if (!$openOrder) {
                return redirect()->back()->with('error', 'Não existe pedido em aberto para adicionar produtos.');
            }

            $existingItem = OrderItem::where('order_id', $openOrder->id)
                ->where('product_sku', $request->item_sku)
                ->first();

            if ($existingItem) {
                return redirect()->back()->with('error', 'Este produto já foi adicionado ao pedido.');
            }

            OrderItem::create([
                'order_id' => $openOrder->id,
                'product_sku' => $request->item_sku,
                'quantity' => (int) $request->quantity,
                'product_barcode' => $request->barcode ?? null,
                'product_name' => $request->product_name ?? null,
                'supplier_id' => $request->supplier_id ?? null,
                'brand_id' => $request->brand_id ?? null,
            ]);

            return redirect()->route('lists.single')->with('success', 'Produto adicionado ao pedido com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Erro ao adicionar produto: ' . $e->getMessage());
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


    public function update(Request $request, Order $order)
    {
        try {

            if ($order->items()->count() === 0) {
                return redirect()->back()->with('error', 'O pedido está vazio. Adicione itens antes de enviar.');
            }

            // Atualizar quantidades
            if ($request->has('quantities')) {
                foreach ($request->input('quantities') as $itemId => $qty) {
                    $order->items()->where('id', $itemId)->update(['quantity' => $qty]);
                }
            }

            // Atualizar status para "Ativo"
            $order->status_id = 1;
            $order->save();

            // Nome da pasta: "julho_2025"
            $folder = Str::slug(Carbon::now()->locale('pt_PT')->translatedFormat('F_Y'), '_');
            $storagePath = "orders/{$folder}";

            // Criar pasta se não existir
            if (!Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->makeDirectory($storagePath);
            }

            // Carregar itens com relações
            $order->load(['items.supplier', 'items.brand']);

            $fornecedores = $order->items->groupBy(fn($item) => optional($item->supplier)->id ?? 'sem_fornecedor');

            $downloadLinks = [];
            $fileRecords = [];

            foreach ($fornecedores as $items) {
                $supplier = $items->first()->supplier;
                $supplierName = Str::slug($supplier->name ?? 'fornecedor', '_');
                $data = Carbon::now()->format('Y-m-d');
                $filename = "{$supplierName}_{$data}.xlsx";
                $filePath = "{$storagePath}/{$filename}";

                // Gerar Excel apenas com os itens deste fornecedor
                Excel::store(new OrderExport($items), $filePath, 'public');

                $downloadLinks[] = $filename;

                // Registo estruturado para JSON e envio
                $fileRecords[] = [
                    'path' => $filePath,
                    'filename' => $filename,
                    'supplier' => $supplier->name ?? 'Desconhecido',
                    'created_at' => now()->toDateTimeString(),
                ];
            }

            // Salvar o JSON na coluna 'files'
            $order->files = $fileRecords;
            $order->save();

            // Enviar email com ficheiros anexos
            Mail::to('desenvolvimento@peixeverde.pt')
                ->cc('desenvolvimento.peixeverde@gmail.com')
                ->send(new OrderFilesMail($fileRecords, collect($fileRecords)->pluck('filename')->toArray()));

            $msg = "Pedido enviado com sucesso!<br>Ficheiros gerados:<br>" . implode('<br>', $downloadLinks);

            return redirect()->route('backoffice.dashboard')->with('success', $msg);

        } catch (\Throwable $e) {
            // Reverter estado para "pending" em caso de falha
            $order->status_id = 3;
            $order->save();

            return redirect()->back()->with('error', 'Erro ao enviar o pedido. Contacte o suporte.' . $e);
        }
    }

}
