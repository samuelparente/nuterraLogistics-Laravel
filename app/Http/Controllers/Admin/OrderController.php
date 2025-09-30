<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AppSetting;
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
        $brandBonuses = $item->brand?->bonuses ?? collect();
        $supplierBonuses = $item->supplier?->bonuses ?? collect();

        $allBonuses = $brandBonuses->merge($supplierBonuses)->unique('id');

        // 👉 Adiciona uma propriedade 'Bonuses' no mesmo formato usado nas outras views
        $item->Bonuses = $allBonuses->map(function ($b) {
            return [
                'description' => $b->description ?? $b->name,
                'notes' => $b->notes ?? null,
            ];
        })->values()->all(); // Garante array limpo com índices numéricos
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
                    'message' => 'Não existe um pedido em aberto para adicionar produtos.',
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
                ], 200); 
            }

            
            return response()->json([
                'success' => true,
                'message' => 'Produto(s) adicionados com sucesso.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro inesperado. Contacte o suporte' .$e,
            ], 500);
        }
    }

    public function addSingle(Request $request)
    {
        try {
            $request->validate([
                'item_sku' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'product_name'    => 'required','string','max:255',
            ]);

            $openOrder = Order::whereHas('status', fn($q) => $q->where('code', 'pending'))->first();

            if (!$openOrder) {
                return redirect()->back()->with('error', 'Não existe um pedido em aberto para adicionar produtos.');
            }

            $existingItem = OrderItem::where('order_id', $openOrder->id)
                ->where('product_sku', $request->item_sku)
                ->first();

            if ($existingItem) {
                return redirect()->back()->with('error', 'Este produto já foi adicionado.');
            }

            OrderItem::create([
                'order_id' => $openOrder->id,
                'product_sku' => $request->item_sku,
                'quantity' => (int) $request->quantity,
                'product_barcode' => $request->bar_code ?? null,
                'product_name' => $request->product_name ?? null,
                'supplier_id' => $request->supplier_id ?? null,
                'brand_id' => $request->brand_id ?? null,
            ]);

            return redirect()->route('lists.single')->with('success', 'Produto adicionado com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte' . $e);
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

        return back()->with('success', 'Produto removido com sucesso.');
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
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }


    public function update(Request $request, Order $order)
{
    try {
        if ($order->items()->count() === 0) {
            return redirect()->back()->with('error', 'O pedido está vazio. Adicione produtos antes de enviar.');
        }

        // Validação
        $validated = $request->validate([
            'quantities' => ['array'],
            'quantities.*' => ['required', 'integer', 'min:1'],
        ]);

        // Atualizar quantidades (apenas itens deste pedido)
        foreach ($validated['quantities'] ?? [] as $itemId => $qty) {
            $order->items()
                ->where('order_id', $order->id)
                ->where('id', $itemId)
                ->update(['quantity' => (int) $qty]);
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

        // Carregar itens com relações necessárias
        $order->load(['items.supplier', 'items.brand']);

        // Agrupar apenas por fornecedores válidos
        $gruposPorFornecedor = $order->items
            ->filter(fn($item) => $item->supplier)   // ignora itens sem fornecedor
            ->groupBy('supplier_id');

        $fileRecords = [];
        $suppliersSummary = [];

        foreach ($gruposPorFornecedor as $supplierId => $items) {
            $supplier    = $items->first()->supplier;
            $supplierName = $supplier?->name ?? 'Fornecedor';
            $supplierSlug = Str::slug($supplierName, '_');
            $data        = Carbon::now()->format('Y-m-d');
            $filename    = "{$supplierSlug}_{$data}.xlsx";
            $filePath    = "{$storagePath}/{$filename}";

            // Gerar Excel apenas com os itens deste fornecedor
            Excel::store(new OrderExport($items), $filePath, 'public');

            // Registo para anexar e guardar no pedido
            $fileRecords[] = [
                'path'       => $filePath,
                'filename'   => $filename,
                'supplier'   => $supplierName,
                'created_at' => now()->toDateTimeString(),
            ];

            // ===== Resumo por fornecedor (marcas e nº linhas) =====
            $brands = $items
                ->pluck('brand.name') // usa relação carregada
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $suppliersSummary[] = [
                'supplier' => $supplierName,
                'brands'   => $brands,           // array de strings
                'lines'    => $items->count(),   // nº de linhas/itens neste pedido para o fornecedor
            ];
        }

        // Salvar os ficheiros no próprio pedido (assumindo cast para array/json na model)
        $order->files = $fileRecords;
        $order->save();

        // Configuração dinâmica de SMTP (se existir)
        $settings = AppSetting::first();
        if ($settings) {
            config([
                'mail.mailers.smtp.host'       => $settings->smtp_host,
                'mail.mailers.smtp.port'       => $settings->smtp_port,
                'mail.mailers.smtp.username'   => $settings->smtp_user,
                'mail.mailers.smtp.password'   => $settings->smtp_password,
                'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                'mail.from.address'            => $settings->smtp_from_address,
                'mail.from.name'               => $settings->smtp_from_name,
            ]);
        }

        // Destinatários
        $to = collect(json_decode($settings->notification_to ?? '[]', true))
            ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->values()
            ->toArray();

        $cc = collect(json_decode($settings->notification_cc ?? '[]', true))
            ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->values()
            ->toArray();

        // Construir o resumo a ser enviado para a view
        $orderSummary = [
            'suppliers' => $suppliersSummary,
            'count'     => count($suppliersSummary),
        ];

        // Enviar email (anexos + resumo; sem listar nomes no corpo)
        if (!empty($to)) {
            Mail::to($to)
                ->cc($cc)
                ->send(new OrderFilesMail(
                    attachments: $fileRecords,
                    downloadLinks: [],              // deixamos vazio; os ficheiros estão em anexo
                    orderSummary: $orderSummary     // <-- novo resumo para a view
                ));
        }

        return redirect()
            ->route('backoffice.dashboard')
            ->with('success', 'Pedido enviado com sucesso!');

    } catch (\Throwable $e) {
        // Reverter estado para "pending" em caso de falha
        $order->status_id = 3;
        $order->save();

        return redirect()->back()->with('error', 'Erro ao enviar o pedido. Contacte o suporte. '.$e->getMessage());
    }
}


}
