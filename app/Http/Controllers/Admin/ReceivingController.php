<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ErpController;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Receiving;
use App\Models\Admin\ReceivingItem;
use App\Models\Admin\ReceivingBatch;
use App\Http\Controllers\Admin\WooController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\AppSetting;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReceivingFinalizedMail;

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
            ->whereHas('items', function ($query) {
                $query->whereHas('supplier')
                    ->whereRaw('NOT EXISTS (
                        SELECT 1 FROM receivings
                        WHERE receivings.order_id = order_items.order_id
                        AND receivings.supplier_id = order_items.supplier_id
                        AND receivings.deleted_at IS NULL
                    )');
            })
            ->orderByDesc('created_at')
            ->paginate(paginationPerPage());

        // suppliers_without_receiving: fornecedores que AINDA NÃO têm qualquer receção (aberta ou concluída)
        foreach ($orders as $order) {
            $suppliers = $order->items
                ->filter(fn($item) => $item->supplier)
                ->pluck('supplier')
                ->unique('id');

            $order->suppliers_without_receiving = $suppliers->filter(function ($supplier) use ($order) {
                return !Receiving::where('order_id', $order->id)
                    ->where('supplier_id', $supplier->id)
                    ->whereNull('deleted_at')
                    ->exists();
            });
        }

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

    public function startReceivingBySupplier(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $order = Order::with('items')->findOrFail($request->order_id);

        // Filtrar itens apenas deste fornecedor
        $items = $order->items->where('supplier_id', $request->supplier_id);

        if ($items->isEmpty()) {
            return back()->with('warning', 'Nenhum item deste fornecedor no pedido.');
        }

        try {
            // Criar ou obter receção existente
            $receiving = Receiving::firstOrCreate(
                [
                    'order_id'    => $order->id,
                    'supplier_id' => $request->supplier_id,
                ],
                [
                    'started_at' => now(),
                    'status_id'  => 7, // exemplo: 'em curso'
                ]
            );

            // Garantir que os itens estão associados à receção
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
                        'received_qty'    => 0, // ou valor padrão
                        'notes'           => null,
                    ]
                );
            }

            return redirect()->route('receivings.index')
                ->with('success', 'Entrada iniciada para o fornecedor selecionado.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
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
            ->with('success', 'Progresso guardado.');
    }

    public function finalize(Request $request, Order $order, Supplier $supplier)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $receiving = Receiving::where('order_id', $order->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        try {
            DB::transaction(function () use ($receiving, $validated) {
                foreach ($receiving->items as $item) {
                    $totalQty = $item->batches()->sum('quantity');

                    // Validação: lote obrigatório com quantidade
                    if ($totalQty < 0) {
                        throw new \Exception("Sem lote e quantidade no item '{$item->product_name}'. Entrada não finalizada.");
                    }

                    $item->update([
                        'received_qty' => $totalQty,
                    ]);
                }

                $receiving->update([
                    'notes' => $validated['notes'] ?? null,
                    'received_at' => now(),
                    'received_by' => auth()->id(),
                    'status_id' => 5, // finalizado/arquivado receining individual
                ]);

                // Verifica se todos os receivings da mesma order estão finalizados e marca essa order como finalizada
                $allFinalized = Receiving::where('order_id', $receiving->order_id)
                    ->whereNull('deleted_at') // ignora os eliminados
                    ->whereNull('received_at') // ainda não finalizados
                    ->doesntExist();

                if ($allFinalized) {
                    Order::where('id', $receiving->order_id)
                        ->update(['status_id' => 5]); // arquivado
                }


            });

            // Aqui fica a chamada de inserir o documento no ERP
            try {
                // Recarregar com relações necessárias
                $receiving->load([
                    'supplier',                // precisa do erp_id
                    'items.batches',           // 1 linha por lote
                    'items.orderItem',         // tentar obter preço
                ]);

                // Validar erp_id do fornecedor
                $supplierErpId = $receiving->supplier?->erp_id;
                if (!$supplierErpId) {
                    throw new \Exception('Fornecedor sem ERP ID (erp_id). Associe o erp_id ao fornecedor antes de finalizar.');
                }

                // Configs/fallbacks
                $transDocument = config('erp.docs.entry', 'FGR');               // sigla do doc de ENTRADA
                $warehouseId   = config('erp.default_warehouse_id', 1);
                $paymentId     = config('erp.default_payment_id');              // opcional
                $tenderId      = config('erp.default_tender_id');               // opcional

                // Montar linhas a partir dos lotes
                $lines = [];
                $erp = new ErpController();

                foreach ($receiving->items as $item) {
                    // SKU ERP = assumimos product_sku
                    $itemId = $item->product_sku;

                    // Tentar preço a partir do OrderItem (se existir esse campo). 
                    $priceFromOrder = null;
                    if ($item->relationLoaded('orderItem') && $item->orderItem) {
                        // tenta várias colunas comuns
                        $priceFromOrder = $item->orderItem->unit_price
                            ?? $item->orderItem->price
                            ?? $item->orderItem->cost_price
                            ?? null;
                    }

                    foreach ($item->batches as $batch) {
                        $qty = (float) $batch->quantity;

                        //Se a quantidade for 0 (ou negativa), ignora o lote
                        if ($qty <= 0) {
                            continue;
                        }
                        
                        // Determinar preço (fallback ao ERP se necessário)
                        $price = $priceFromOrder;
                        if ($price === null) {
                            $p = $erp->getProductBySkuOrBarcode($itemId);
                            $price = (float) ($p['CostPrice'] ?? 0);
                        }

                        // Normalizar data (YYYY-MM-DD)
                        $validade = \Carbon\Carbon::parse($batch->expiry_date)->format('Y-m-d');

                        $lines[] = [
                            'itemID'       => $itemId,
                            'quantity'     => $qty,
                            'price'        => (float) $price,
                            'unitOfSaleID' => 'UNI',
                            'propriedade1' => (string) $batch->batch_number,
                            'validade1'    => $validade,
                            'colorID'      => 0,
                            'sizeID'       => 0,
                        ];
                    }
                }

                if (empty($lines)) {
                    throw new \Exception('Nenhuma linha para enviar ao ERP (sem lotes válidos).');
                }

                // Payload final
                $orderReceived = [
                    'clientID'               => (int) $supplierErpId,         // <- Supplier ERP ID
                    'wharehouseID'           => (int) $warehouseId,
                    'transDocument'          => (string) $transDocument,
                    'transactionTaxIncluded' => false,
                    'comments'               => $receiving->notes ?? 'Entrada finalizada via plataforma NUTERRA | Logistics',
                    'lines'                  => $lines,
                ];

                if ($paymentId) $orderReceived['paymentID'] = (int) $paymentId;
                if ($tenderId)  $orderReceived['tenderID']  = (int) $tenderId;

                // Enviar
                $erpController = new ErpController();
                $result = $erpController->erpInsertDocument($orderReceived);

                if (!($result['success'] ?? false)) {
                    throw new \Exception('ERP: ' . ($result['error'] ?? 'Falha a inserir documento.'));
                }

                // Envia mail de resumo
                $receiving->load(['supplier', 'items.brand']);

                $brands = $receiving->items
                    ->pluck('brand.name')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                // Divergências: itens do pedido cujo recebido difere do encomendado
                $divergences = $receiving->items
                    ->filter(function ($i) {
                        return $i->order_item_id !== null && (int)$i->ordered_qty !== (int)$i->received_qty;
                    })
                    ->map(function ($i) {
                        $ordered  = (int)($i->ordered_qty ?? 0);
                        $received = (int)($i->received_qty ?? 0);
                        return [
                            'sku'         => (string)$i->product_sku,
                            'name'        => (string)$i->product_name,
                            'ordered_qty' => $ordered,
                            'received_qty'=> $received,
                            'diff'        => $received - $ordered,
                        ];
                    })
                    ->values()
                    ->toArray();

                // Novos itens marcados na receção
                $newItems = $receiving->items
                    ->where('is_new', 1)
                    ->map(function ($i) {
                        return [
                            'sku'          => (string)$i->product_sku,
                            'name'         => (string)$i->product_name,
                            'received_qty' => (int)($i->received_qty ?? 0),
                        ];
                    })
                    ->values()
                    ->toArray();

                // Meta/summary
                $summary = [
                    'supplier_name' => $receiving->supplier?->name ?? 'Fornecedor',
                    'brands'        => $brands,
                    'meta'          => [
                        'order_id'        => $receiving->order_id,
                        'receiving_id'    => $receiving->id,
                        'received_at'     => optional($receiving->received_at)->format('Y-m-d H:i'),
                        'received_by_name'=> optional($receiving->receivedBy ?? null, function ($u) { return $u->name ?? null; }) ?? (auth()->user()->name ?? 'Utilizador'),
                        'notes'           => $receiving->notes,
                    ],
                ];

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

                // Destinatários do email
                $to = collect(json_decode($settings->notification_to ?? '[]', true))
                    ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                    ->values()
                    ->toArray();

                $cc = collect(json_decode($settings->notification_cc ?? '[]', true))
                    ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                    ->values()
                    ->toArray();

                // Enviar email
                try {
                    Mail::to($to)->cc($cc)->send(new ReceivingFinalizedMail($summary, $divergences, $newItems));
                } catch (\Throwable $mailEx) {
                    // Não bloquear o fluxo se o email falhar; registra para debug
                    \Log::error('Falha no envio de email de receção concluída', [
                        'receiving_id' => $receiving->id,
                        'error' => $mailEx->getMessage(),
                    ]);
                }

            } catch (\Throwable $e) {
                return back()->with('error', 'Falha ao criar documento no ERP: ' . $e->getMessage());
            }


            return redirect()
                ->route('receivings.pending')
                ->with('success', 'Entrada finalizada e arquivada com sucesso.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage() ?: 'Ocorreu um erro inesperado. Contace o suporte.');
        }
    }


    // GET: formulário de pesquisa para adicionar produto extra à receção
    public function searchSingle(Request $request, Receiving $receiving, ErpController $erpController)
    {
        $product = null;

        if ($request->filled('search')) {
            $product = $erpController->getProductBySkuOrBarcode($request->search);

            if (!$product) {
                return redirect()
                    ->route('receivings.items.singleScanner', $receiving->id)
                    ->with('errorCreate', 'Este produto não existe. Criar novo?')
                    ->with('createProductUrl', route('receivings.items.createProduct', ['receiving' => $receiving->id]));
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
                    ->with('errorCreate', 'Este produto não existe. Criar novo?')
                    ->with('createProductUrl', route('receivings.items.createProduct', ['receiving' => $receiving->id]));
            }

        }

        return view('layouts.admin.receivings.singleScanner', compact('product', 'receiving'));
    }

    public function addSingle(Request $request, Receiving $receiving)
    {
        $request->validate([
            'item_sku'      => 'required|string',
            'quantity'      => 'required|integer|min:0',
            'product_name'  => 'required|string',
            'batch_number'  => 'required|string|max:255',
            'expiry_date'   => 'required|date|after:today',
            'is_new'        => 'nullable|boolean',
        ]);

        $exists = ReceivingItem::where('receiving_id', $receiving->id)
            ->where('product_sku', $request->item_sku)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Este produto já foi adicionado à receção.');
        }

        try {
            DB::transaction(function () use ($request, $receiving) {
                $item = ReceivingItem::create([
                    'receiving_id'    => $receiving->id,
                    'product_sku'     => $request->item_sku,
                    'product_name'    => $request->product_name,
                    'product_barcode' => $request->bar_code ?? null,
                    'supplier_id'     => $request->supplier_id ?? null,
                    'brand_id'        => $request->brand_id ?? null,
                    'ordered_qty'     => 0,
                    'received_qty'    => $request->quantity,
                    'is_new'          => (int) $request->input('is_new', 0),
                ]);

                $item->batches()->create([
                    'quantity'     => $request->quantity,
                    'batch_number' => $request->batch_number,
                    'expiry_date'  => $request->expiry_date,
                ]);
            });

            return redirect()
                ->route('receivings.form', [
                    'order'    => $receiving->order_id,
                    'supplier' => $receiving->supplier_id,
                ])
                ->with('success', 'Produto e lote adicionados com sucesso.');
        } catch (\Throwable $e) {
            // loga o erro para debug
            \Log::error('Erro ao adicionar item na receção: '.$e->getMessage(), ['trace' => $e]);

            return redirect()
                ->route('receivings.form', [
                    'order'    => $receiving->order_id,
                    'supplier' => $receiving->supplier_id,
                ])
                ->with('error', 'Ocorreu um erro inesperado ao adicionar o produto. Contacte o suporte.');
        }
    }

    public function addSingleScanner(Request $request, Receiving $receiving)
    {
        $request->validate([
            'item_sku'      => 'required|string',
            'quantity'      => 'required|integer|min:0',
            'product_name'  => 'required|string',
            'batch_number'  => 'required|string|max:255',
            'expiry_date'   => 'required|date|after:today',
            'is_new'        => 'nullable|boolean', 
        ]);

        try {
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

                    // se este scan marcou como novo, mantém a flag a 1
                    if ($request->boolean('is_new')) {
                        $item->is_new = 1;
                        $item->save();
                    }
                } else {
                    // Cria novo item e primeiro lote
                    $item = ReceivingItem::create([
                        'receiving_id'    => $receiving->id,
                        'product_sku'     => $request->item_sku,
                        'product_name'    => $request->product_name,
                        'product_barcode' => $request->bar_code ?? null,
                        'supplier_id'     => $request->supplier_id ?? null,
                        'brand_id'        => $request->brand_id ?? null,
                        'ordered_qty'     => 0,
                        'received_qty'    => $request->quantity,
                        'is_new'          => (int) $request->input('is_new', 0), 
                    ]);

                    $item->batches()->create([
                        'quantity'     => $request->quantity,
                        'batch_number' => $request->batch_number,
                        'expiry_date'  => $request->expiry_date,
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Item inserido com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.' . $e);
        }
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
                // Elimina os itens associados
                $receiving->items()->delete();

                // Soft delete da própria receção
                $receiving->delete();
            });

            return redirect()->back()->with('success', 'Entrada eliminada.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    public function storeBatch(Request $request, ReceivingItem $item)
    {
        $validated = $request->validate([
            'quantity'      => 'required|integer|min:0',
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
            return response()->json(['success' => false, 'message' => 'Erro ao guardar o lote.' . $e]);
        }
    }

    public function createProduct(Request $request, Receiving $receiving, ErpController $erpController)
    {

        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        return view('layouts.admin.receivings.create',compact('receiving', 'suppliers', 'brands'));

    }

    public function createAddProduct(Request $request, Receiving $receiving, ErpController $erpController)
    {

        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
       
        try {
        
            // Pede ao ErpController para inserir o produto novo no ERP
            // o id do fornecedor e marca que vem do form tem de ser convertido no erp_id dessas tabelas
            // antes de enviar
            //dd($request);

            // Montar payload
            $product =[];
            
            // buscar erp_id correspondentes (ignora soft-deleted)
            $supplierErpId = Supplier::whereKey($request['supplier_id'])->value('erp_id');
            $brandErpId    = Brand::whereKey($request['brand_id'])->value('erp_id');

            //dd($request);
            $product['ItemID'] = $request['item_sku'];
            $product['Description'] = $request['product_full_description'];
            $product['ShortDescription'] = $request['product_description'];
            $product['ItemType'] = 0;
            $product['BarCode'] = $request['bar_code'];
            $product['BarCodeType'] = 0;
            $product['UnitOfSaleID'] = "UNI";
            $product['TaxableGroupID'] = $request['TaxableGroupID'];
            $product['pc'] = $request['pc'];
            $product['SupplierID'] = $supplierErpId;
            $product['FamilyID'] = $brandErpId;

            try{
                 $result = $erpController->erpCreateProduct($product);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.' . $e);
            }

            //O pedido para o inserir na receção com lote

            $newRequest = new Request([
                'item_sku'      => $request['item_sku'],
                'quantity'      => $request['quantity'],
                'product_name'  => $request['product_full_description'],
                'batch_number'  => $request['batch_number'],
                'expiry_date'   => $request['expiry_date'],
                'supplier_id'   => $request['supplier_id'],
                'brand_id'      => $request['brand_id'],    
                'bar_code'      => $request['bar_code'],    
                'is_new'        => 1,
                ]);

            $this->addSingleScanner($newRequest, $receiving);


             try {
                app(WooController::class)->createFromArray([
                    'name'    => $request['product_full_description'],
                    'sku'     => $request['item_sku'],
                    'barcode' => $request['bar_code'] ?? null,
                    'tax_group_id'  => $request['TaxableGroupID'],
                ]);
                $wooOk = true;
            } catch (\Throwable $e) {

                $wooOk = false; // não bloquear fluxo
            }

            return redirect()->back()->with(
                    'success',
                    $wooOk
                    ? 'Produto criado no Sage, criado no WooCommerce e inserido na receção.'
                    : 'Produto criado no Sage e inserido na receção. (WooCommerce indisponível no momento.)'
                );        
        } 
        catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }

    }

    public function history()
    {
        $receivings = Receiving::with(['supplier', 'items.brand'])
            ->whereNotNull('received_at')
            ->orderByDesc('received_at')
            ->paginate(paginationPerPage());

        return view('layouts.admin.receivings.history', compact('receivings'));
    }

    public function showBrandDivergences(Receiving $receiving, int $brandId)
    {
        // Carrega os itens desta marca com divergências
        $items = $receiving->items()
            ->where('brand_id', $brandId)
            ->get()
            ->filter(function ($item) {
                return is_null($item->order_item_id) || $item->received_qty != $item->ordered_qty;
            });

        $brand = $items->first()?->brand;
        $supplier = $receiving->supplier;

        return view('layouts.admin.receivings.divergences', [
            'receiving' => $receiving,
            'divergentItems' => $items,
            'brand' => $brand,
            'supplier' => $supplier,
        ]);
    }

    public function showDetails(Receiving $receiving)
    {
        $items = $receiving->items()->with('batches')->get();

        return view('layouts.admin.receivings.details', compact('receiving', 'items'));
    }



}
