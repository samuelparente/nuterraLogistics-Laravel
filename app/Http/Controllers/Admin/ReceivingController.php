<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ErpController;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;
use App\Models\Admin\Order;
use App\Models\Admin\Receiving;
use App\Models\Admin\ReceivingItem;
use App\Models\Admin\ReceivingBatch;
use App\Http\Controllers\Admin\WooController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\AppSetting;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReceivingFinalizedMail;
use App\Exports\ReceivingExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\Receiving\ReceivingFxCalculator;
use App\Services\Receiving\ReceivingPreviewService;


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

        // Destinatários a partir das definições globais
        $settings = AppSetting::first();
        $mailTo   = $settings?->toList() ?? [];
        $mailCc   = $settings?->ccList() ?? [];

        return view('layouts.admin.receivings.form', compact(
            'order',
            'supplier',
            'receiving',
            'items',
            'mailTo',
            'mailCc'
        ));
    }

    public function store(Request $request, Order $order, Supplier $supplier)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'moedaId' => ['required', 'in:EUR,USD'],
            'taxaCambio' => [
                'required_if:moedaId,USD',
                'nullable',
                'numeric',
                'gt:0',
                'regex:/^\d+(?:\.\d{1,12})?$/',
            ],
        ]);

        $receiving = Receiving::where('order_id', $order->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        if ($this->isErpLocked($receiving)) {
            return back()->withErrors([
                'receiving' => 'Esta receção já foi enviada ou encontra-se em validação no Sage.',
            ]);
        }

        $rate = app(ReceivingFxCalculator::class)->normalizeRate(
            $validated['taxaCambio'] ?? null,
            $validated['moedaId']
        );

        DB::transaction(function () use ($receiving, $validated, $rate) {
            foreach ($receiving->items as $item) {
                $totalQty = $item->batches()->sum('quantity');
                $item->update([
                    'received_qty' => $totalQty,
                ]);
            }

            $receiving->update([
                'notes' => $validated['notes'] ?? null,
                'fx_currency' => $validated['moedaId'],
                'fx_rate_to_eur' => $rate,
                'fx_preview' => null,
                'fx_preview_token' => null,
                'fx_previewed_at' => null,
            ]);
        });

        return redirect()
            ->route('receivings.form', ['order' => $receiving->order_id, 'supplier' => $receiving->supplier_id])
            ->with('success', 'Progresso guardado.');
    }

    public function preview(
        Request $request,
        Order $order,
        Supplier $supplier,
        ReceivingPreviewService $previewService,
    ) {
        $validated = $request->validate([
            'modo_insercao' => ['required', 'in:anterior,com_impostos,sem_impostos'],
            'moedaId' => ['required', 'in:EUR,USD'],
            'taxaCambio' => [
                'required_if:moedaId,USD',
                'nullable',
                'numeric',
                'gt:0',
                'regex:/^\d+(\.\d{1,12})?$/',
            ],
        ]);

        $receiving = Receiving::where('order_id', $order->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        if (! $receiving->supplier?->erp_id) {
            return response()->json([
                'message' => 'Fornecedor sem ERP ID. Associe o fornecedor ao Sage antes de finalizar.',
            ], 422);
        }

        if (in_array($receiving->erp_submission_status, ['submitting', 'uncertain'], true)) {
            return response()->json([
                'message' => 'Esta receção tem uma submissão Sage em validação e não pode ser recalculada.',
            ], 409);
        }

        try {
            $snapshot = $previewService->build(
                $receiving,
                $validated['moedaId'],
                $validated['taxaCambio'] ?? null,
                $validated['modo_insercao'],
                (int) config('erp.default_warehouse_id', 1),
            );

            $token = (string) Str::uuid();

            $receiving->update([
                'fx_currency' => $snapshot['currency'],
                'fx_rate_to_eur' => $snapshot['rate'],
                'fx_preview' => $snapshot,
                'fx_preview_token' => $token,
                'fx_previewed_at' => now(),
                'erp_submission_status' => 'pending',
                'erp_submission_key' => $receiving->erp_submission_key ?: (string) Str::uuid(),
            ]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'preview' => [
                    'supplier' => $snapshot['supplier'],
                    'receiving_id' => $snapshot['receiving_id'],
                    'order_id' => $snapshot['order_id'],
                    'currency' => $snapshot['currency'],
                    'rate' => $snapshot['rate'],
                    'product_count' => $snapshot['product_count'],
                    'line_count' => $snapshot['line_count'],
                    'transaction_tax_included' => $snapshot['transaction_tax_included'],
                    'items' => $snapshot['items'],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Não foi possível preparar a pré-visualização.',
            ], 422);
        }
    }

    public function finalize(
        Request $request,
        Order $order,
        Supplier $supplier,
        ReceivingPreviewService $previewService,
        ReceivingFxCalculator $fxCalculator,
    ) {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'mail_to' => ['nullable', 'array'],
            'mail_to.*' => ['required', 'email'],
            'mail_cc' => ['nullable', 'array'],
            'mail_cc.*' => ['required', 'email'],
            'modo_insercao' => ['required', 'in:anterior,com_impostos,sem_impostos'],
            'moedaId' => ['required', 'in:EUR,USD'],
            'taxaCambio' => [
                'required_if:moedaId,USD',
                'nullable',
                'numeric',
                'gt:0',
                'regex:/^\d+(\.\d{1,12})?$/',
            ],
            'preview_token' => ['required', 'uuid'],
        ]);

        $to = collect($validated['mail_to'] ?? [])->filter()->values()->all();
        $cc = collect($validated['mail_cc'] ?? [])->filter()->values()->all();

        if ($to === [] && $cc === []) {
            return back()->withInput()->with(
                'error',
                'Seleccione pelo menos um destinatário (Para ou CC).'
            );
        }

        if ($to === [] && $cc !== []) {
            $to[] = array_shift($cc);
        }

        $receiving = Receiving::where('order_id', $order->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        $sageAccepted = false;

        try {
            $submission = DB::transaction(function () use (
                $receiving,
                $validated,
                $previewService,
                $fxCalculator,
            ) {
                $locked = Receiving::whereKey($receiving->id)->lockForUpdate()->firstOrFail();

                if ($locked->received_at !== null || $locked->erp_submission_status === 'submitted') {
                    throw new \RuntimeException(
                        'Esta receção já foi inserida no Sage'
                        . ($locked->erp_document_reference ? " ({$locked->erp_document_reference})" : '')
                        . '.'
                    );
                }

                if (in_array($locked->erp_submission_status, ['submitting', 'uncertain'], true)) {
                    throw new \RuntimeException(
                        'Já existe uma submissão Sage em curso ou por confirmar para esta receção.'
                    );
                }

                if (! hash_equals((string) $locked->fx_preview_token, $validated['preview_token'])) {
                    throw new \RuntimeException('A pré-visualização expirou. Gere uma nova antes de finalizar.');
                }

                $snapshot = $locked->fx_preview;

                if (! is_array($snapshot) || empty($snapshot['lines'])) {
                    throw new \RuntimeException('A pré-visualização guardada é inválida. Gere uma nova.');
                }

                $submittedRate = $fxCalculator->normalizeRate(
                    $validated['taxaCambio'] ?? null,
                    $validated['moedaId'],
                );

                if (
                    ($snapshot['currency'] ?? null) !== $validated['moedaId']
                    || ($snapshot['rate'] ?? null) !== $submittedRate
                    || ($snapshot['tax_mode'] ?? null) !== $validated['modo_insercao']
                ) {
                    throw new \RuntimeException(
                        'A moeda, taxa ou modo de impostos mudou. Gere uma nova pré-visualização.'
                    );
                }

                $previewService->assertSnapshotStillMatches($locked, $snapshot);

                $submissionKey = $locked->erp_submission_key ?: (string) Str::uuid();

                $locked->update([
                    'erp_submission_status' => 'submitting',
                    'erp_submission_key' => $submissionKey,
                ]);

                return [
                    'snapshot' => $snapshot,
                    'submission_key' => $submissionKey,
                ];
            });

            $receiving->load('supplier');
            $supplierErpId = $receiving->supplier?->erp_id;

            if (! $supplierErpId) {
                throw new \RuntimeException('Fornecedor sem ERP ID. Associe o fornecedor ao Sage.');
            }

            $transDocument = (string) config('erp.docs.entry', 'FGR');
            $transSerial = (string) config('erp.docs.entry_series', 'PV');
            $warehouseId = (int) config('erp.default_warehouse_id', 1);
            $salesmanId = (int) config('erp.default_salesman_id', 1);

            $erp = app(ErpController::class);
            $supplierDefaults = $erp->getSupplierDocumentDefaults((int) $supplierErpId);
            $paymentId = (int) ($supplierDefaults['paymentID'] ?? config('erp.default_payment_id') ?? 0);
            $tenderId = (int) ($supplierDefaults['tenderID'] ?? config('erp.default_tender_id') ?? 0);

            if ($transSerial === '' || $warehouseId <= 0 || $salesmanId <= 0) {
                throw new \RuntimeException('Configuração Sage incompleta: confirme série, vendedor e armazém.');
            }

            if ($paymentId <= 0 || $tenderId <= 0) {
                throw new \RuntimeException(
                    "Não foi possível obter o pagamento ou meio de pagamento do fornecedor Sage {$supplierErpId}."
                );
            }

            $snapshot = $submission['snapshot'];
            $orderReceived = [
                'clientID' => (int) $supplierErpId,
                'salesmanID' => $salesmanId,
                'paymentID' => $paymentId,
                'tenderID' => $tenderId,
                'transSerial' => $transSerial,
                'wharehouseID' => $warehouseId,
                'transDocument' => $transDocument,
                'transactionTaxIncluded' => (bool) $snapshot['transaction_tax_included'],
                'contractReferenceNumber' => 'NUTERRA-RECEIVING-' . $receiving->id
                    . '-' . $submission['submission_key'],
                'comments' => $validated['notes']
                    ?? $receiving->notes
                    ?? 'Entrada finalizada via plataforma NUTERRA | Logistics',
                'lines' => $snapshot['lines'],
            ];

            $result = $erp->erpInsertDocument($orderReceived);

            if (! ($result['success'] ?? false)) {
                $httpStatus = (int) ($result['status'] ?? 0);
                $status = $httpStatus === 0 || $httpStatus >= 500
                    ? 'uncertain'
                    : 'failed';

                $receiving->update([
                    'erp_submission_status' => $status,
                    'erp_response' => $result,
                ]);

                $message = $status === 'uncertain'
                    ? 'A resposta do Sage é inconclusiva. Confirme no ERP antes de repetir a operação.'
                    : 'Sage: ' . ($result['error'] ?? 'Falha ao inserir o documento.');

                throw new \RuntimeException($message);
            }

            $sageAccepted = true;

            // Regista primeiro a confirmação externa. Se o processo terminar
            // antes da consolidação local, a operação fica bloqueada para não
            // criar um segundo documento no Sage.
            $receiving->update([
                'erp_submission_status' => 'uncertain',
                'erp_document_reference' => $this->erpDocumentReference($result),
                'erp_response' => $result,
                'erp_submitted_at' => now(),
            ]);

            DB::transaction(function () use ($receiving, $validated, $snapshot, $result) {
                $locked = Receiving::whereKey($receiving->id)->lockForUpdate()->firstOrFail();

                foreach ($snapshot['item_snapshots'] as $itemId => $itemSnapshot) {
                    ReceivingItem::where('receiving_id', $locked->id)
                        ->whereKey((int) $itemId)
                        ->update([
                            'received_qty' => (int) $itemSnapshot['received_qty'],
                            'fx_currency' => $itemSnapshot['currency'],
                            'fx_rate_to_eur' => $itemSnapshot['rate'],
                            'fx_source_unit_price' => $itemSnapshot['source_unit_price'],
                            'fx_unit_price_eur' => $itemSnapshot['unit_price_eur'],
                        ]);
                }

                $locked->update([
                    'notes' => $validated['notes'] ?? null,
                    'fx_currency' => $snapshot['currency'],
                    'fx_rate_to_eur' => $snapshot['rate'],
                    'received_at' => now(),
                    'received_by' => auth()->id(),
                    'status_id' => 5,
                    'erp_submission_status' => 'submitted',
                    'erp_document_reference' => $this->erpDocumentReference($result),
                    'erp_response' => $result,
                    'erp_submitted_at' => now(),
                ]);

                $allFinalized = Receiving::where('order_id', $locked->order_id)
                    ->whereNull('deleted_at')
                    ->whereNull('received_at')
                    ->doesntExist();

                if ($allFinalized) {
                    Order::whereKey($locked->order_id)->update(['status_id' => 5]);
                }
            });

            $receiving->refresh();
            $this->sendReceivingCompletion($receiving, $to, $cc);

            return redirect()
                ->route('receivings.pending')
                ->with('success', 'Entrada finalizada e inserida no Sage com sucesso.');
        } catch (\Throwable $e) {
            if ($sageAccepted) {
                Receiving::whereKey($receiving->id)
                    ->whereIn('erp_submission_status', ['submitting', 'uncertain'])
                    ->update(['erp_submission_status' => 'uncertain']);

                $message = 'O Sage confirmou o documento, mas a receção não ficou consolidada localmente. '
                    . 'Não repita a operação; confirme o documento no Sage e contacte o suporte.';
            } else {
                Receiving::whereKey($receiving->id)
                    ->where('erp_submission_status', 'submitting')
                    ->update(['erp_submission_status' => 'failed']);

                $message = $e->getMessage() ?: 'Não foi possível finalizar a receção.';
            }

            return back()
                ->withInput()
                ->with('error', $message);
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
        if ($this->isErpLocked($receiving)) {
            return back()->with('error', 'A receção está bloqueada para validação ou já foi finalizada no Sage.');
        }

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
        if ($this->isErpLocked($receiving)) {
            return back()->with('error', 'A receção está bloqueada para validação ou já foi finalizada no Sage.');
        }

        $request->validate([
            'item_sku'      => 'required|string',
            'quantity'      => 'required|integer|min:0',
            'product_name'  => 'required|string',
            'batch_number'  => 'required|string|max:255',
            'expiry_date'   => 'required|date|after:today',
            'is_new'        => 'nullable|boolean',
            'fx_currency'   => 'required_if:is_new,1|nullable|in:EUR,USD',
            'fx_rate_to_eur' => [
                'required_if:is_new,1',
                'nullable',
                'numeric',
                'gt:0',
                'regex:/^\d+(?:\.\d{1,12})?$/',
            ],
            'fx_source_unit_price' => 'required_if:is_new,1|nullable|numeric|min:0',
            'fx_unit_price_eur' => 'required_if:is_new,1|nullable|numeric|min:0',
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

                    // Se este scan criou o artigo no Sage, guarda também a base
                    // cambial mesmo quando o item já existia na encomenda.
                    if ($request->boolean('is_new')) {
                        $item->update([
                            'is_new' => 1,
                            'fx_currency' => $request->input('fx_currency', 'EUR'),
                            'fx_rate_to_eur' => $request->input('fx_rate_to_eur'),
                            'fx_source_unit_price' => $request->input('fx_source_unit_price'),
                            'fx_unit_price_eur' => $request->input('fx_unit_price_eur'),
                        ]);
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
                        'fx_currency'     => $request->input('fx_currency', 'EUR'),
                        'fx_rate_to_eur'  => $request->input('fx_rate_to_eur'),
                        'fx_source_unit_price' => $request->input('fx_source_unit_price'),
                        'fx_unit_price_eur' => $request->input('fx_unit_price_eur'),
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
        if ($this->isErpLocked($item->receiving)) {
            return back()->with('error', 'A receção está bloqueada para validação ou já foi finalizada no Sage.');
        }

        // Só permite apagar se for um item extra (adicionado manualmente)
        if ($item->order_item_id !== NULL) {
            return redirect()->back()->with('error', 'Não é possível remover itens que pertencem ao pedido original.');
        }

        $item->delete();

        return redirect()->back()->with('success', 'Item removido com sucesso.');
    }

    public function destroyBatch(ReceivingBatch $batch)
    {
        if ($this->isErpLocked($batch->receivingItem->receiving)) {
            return back()->with('error', 'A receção está bloqueada para validação ou já foi finalizada no Sage.');
        }

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
        if ($this->isErpLocked($receiving)) {
            return back()->with('error', 'A receção está bloqueada para validação ou já foi finalizada no Sage.');
        }

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
        if ($this->isErpLocked($item->receiving)) {
            return response()->json([
                'success' => false,
                'message' => 'A receção está bloqueada para validação ou já foi finalizada no Sage.',
            ], 409);
        }

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
        if ($this->isErpLocked($receiving)) {
            return back()->with('error', 'A receção está bloqueada para validação ou já foi finalizada no Sage.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        return view('layouts.admin.receivings.create',compact('receiving', 'suppliers', 'brands'));

    }

    public function createAddProduct(Request $request, Receiving $receiving, ErpController $erpController)
    {
        if ($this->isErpLocked($receiving)) {
            return back()->with('error', 'A receção está bloqueada para validação ou já foi finalizada no Sage.');
        }

        $validated = $request->validate([
            'item_sku' => ['required', 'string', 'max:255'],
            'bar_code' => ['required', 'string', 'max:255'],
            'product_description' => ['required', 'string', 'max:49'],
            'product_full_description' => ['required', 'string', 'max:255'],
            'pc' => ['required', 'numeric', 'min:0'],
            'moedaId' => ['required', 'in:EUR,USD'],
            'taxaCambio' => [
                'required_if:moedaId,USD',
                'nullable',
                'numeric',
                'gt:0',
                'regex:/^\d+(\.\d{1,12})?$/',
            ],
            'TaxableGroupID' => ['required', 'in:1,2,3,4'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'batch_number' => ['required', 'string', 'max:255'],
            'expiry_date' => ['required', 'date', 'after:today'],
        ]);

        $currency = strtoupper($validated['moedaId']);
        $rate = app(ReceivingFxCalculator::class)->normalizeRate(
            $validated['taxaCambio'] ?? null,
            $currency,
        );
       
        try {
        
            // Pede ao ErpController para inserir o produto novo no ERP
            // o id do fornecedor e marca que vem do form tem de ser convertido no erp_id dessas tabelas
            // antes de enviar
            //dd($request);

            // Montar payload
            $product =[];
            
            // buscar erp_id correspondentes (ignora soft-deleted)
            $supplierErpId = Supplier::whereKey($validated['supplier_id'])->value('erp_id');
            $brandErpId    = Brand::whereKey($validated['brand_id'])->value('erp_id');

            //dd($request);
            $product['ItemID'] = $validated['item_sku'];
            $product['Description'] = $validated['product_full_description'];
            $product['ShortDescription'] = $validated['product_description'];
            $product['ItemType'] = 0;
            $product['BarCode'] = $validated['bar_code'];
            $product['BarCodeType'] = 0;
            $product['UnitOfSaleID'] = "UNI";
            $product['TaxableGroupID'] = $validated['TaxableGroupID'];
            $product['pc'] = $validated['pc'];
            $product['moedaId'] = $currency;
            $product['taxaCambio'] = $rate;
            $product['SupplierID'] = $supplierErpId;
            $product['FamilyID'] = $brandErpId;

            try {
                $result = $erpController->erpCreateProduct($product);

                // 👇 Se falhar (incluindo validação 28 chars), não continua
                if (!$result['success']) {
                    // Se for erro de validação (status 422)
                    if ($result['status'] === 422) {
                        return redirect()
                            ->back()
                        ->with('error',  $result['error'])
                            ->withInput();
                    }

                    // Outros erros do ERP
                    return redirect()
                        ->back()
                        ->with('error', 'Erro ao criar produto no ERP: ' . $result['error'])
                        ->withInput();
                }
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
            }


            //O pedido para o inserir na receção com lote

            $newRequest = new Request([
                'item_sku'      => $validated['item_sku'],
                'quantity'      => $validated['quantity'],
                'product_name'  => $validated['product_full_description'],
                'batch_number'  => $validated['batch_number'],
                'expiry_date'   => $validated['expiry_date'],
                'supplier_id'   => $validated['supplier_id'],
                'brand_id'      => $validated['brand_id'],
                'bar_code'      => $validated['bar_code'],
                'is_new'        => 1,
                'fx_currency'   => $currency,
                'fx_rate_to_eur' => $rate,
                'fx_source_unit_price' => $validated['pc'],
                'fx_unit_price_eur' => $result['payload']['pc'],
                ]);

            $this->addSingleScanner($newRequest, $receiving);

            $savedNewItem = $receiving->items()
                ->where('product_sku', $validated['item_sku'])
                ->first();

            if (! $savedNewItem || $savedNewItem->fx_unit_price_eur === null) {
                return redirect()->back()->with(
                    'error',
                    'O produto foi criado no Sage, mas não ficou associado à receção. '
                    . 'Contacte o suporte antes de repetir.'
                );
            }

            $receiving->update([
                'fx_currency' => $currency,
                'fx_rate_to_eur' => $rate,
                'fx_preview' => null,
                'fx_preview_token' => null,
                'fx_previewed_at' => null,
            ]);


             try {
                app(WooController::class)->createFromArray([
                    'name'    => $validated['product_full_description'],
                    'sku'     => $validated['item_sku'],
                    'barcode' => $validated['bar_code'] ?? null,
                    'tax_group_id'  => $validated['TaxableGroupID'],
                ]);
                $wooOk = true;
            } catch (\Throwable $e) {

                $wooOk = false; // não bloquear fluxo
            }

            return redirect()->back()->with(
                    'success',
                    $wooOk
                    ? 'Produto criado no Sage, criado no WooCommerce e inserido na receção.'
                    : 'Produto criado no Sage e inserido na receção. (WooCommerce indisponível no momento.)' . $e
                );        
        } 
        catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }

    }

    private function erpDocumentReference(array $result): ?string
    {
        $reference = data_get($result, 'data.document')
            ?? data_get($result, 'data.documentNumber')
            ?? data_get($result, 'data.DocumentNumber')
            ?? data_get($result, 'data.reference')
            ?? data_get($result, 'data.id');

        return $reference !== null ? (string) $reference : null;
    }

    private function isErpLocked(Receiving $receiving): bool
    {
        return $receiving->received_at !== null
            || in_array(
                $receiving->erp_submission_status,
                ['submitting', 'uncertain', 'submitted'],
                true,
            );
    }

    private function sendReceivingCompletion(Receiving $receiving, array $to, array $cc): void
    {
        try {
            $receiving->load([
                'supplier',
                'receiver',
                'items.brand',
                'items.batches',
            ]);

            $brands = $receiving->items
                ->pluck('brand.name')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $divergences = $receiving->items
                ->filter(fn ($item) => $item->order_item_id !== null
                    && (int) $item->ordered_qty !== (int) $item->received_qty)
                ->map(function ($item) {
                    $ordered = (int) ($item->ordered_qty ?? 0);
                    $received = (int) ($item->received_qty ?? 0);

                    return [
                        'sku' => (string) $item->product_sku,
                        'name' => (string) $item->product_name,
                        'ordered_qty' => $ordered,
                        'received_qty' => $received,
                        'diff' => $received - $ordered,
                    ];
                })
                ->values()
                ->all();

            $newItems = $receiving->items
                ->where('is_new', true)
                ->map(fn ($item) => [
                    'sku' => (string) $item->product_sku,
                    'name' => (string) $item->product_name,
                    'received_qty' => (int) ($item->received_qty ?? 0),
                ])
                ->values()
                ->all();

            $summary = [
                'supplier_name' => $receiving->supplier?->name ?? 'Fornecedor',
                'brands' => $brands,
                'meta' => [
                    'order_id' => $receiving->order_id,
                    'receiving_id' => $receiving->id,
                    'received_at' => $receiving->received_at?->format('Y-m-d H:i'),
                    'received_by_name' => $receiving->receiver?->name
                        ?? auth()->user()?->name
                        ?? 'Utilizador',
                    'notes' => $receiving->notes,
                    'currency' => $receiving->fx_currency,
                    'fx_rate_to_eur' => $receiving->fx_rate_to_eur,
                    'erp_document_reference' => $receiving->erp_document_reference,
                ],
            ];

            $folder = 'receivings/' . Carbon::now()->format('Y_m');
            $filePath = null;

            try {
                if (! Storage::disk('public')->exists($folder)) {
                    Storage::disk('public')->makeDirectory($folder);
                }

                $supplierSlug = Str::slug($receiving->supplier?->name ?? 'fornecedor', '_');
                $date = Carbon::now()->format('Y-m-d');
                $filename = "rececao_{$supplierSlug}_{$date}_{$receiving->id}.xlsx";
                $filePath = "{$folder}/{$filename}";

                Excel::store(new ReceivingExport($receiving), $filePath, 'public');
            } catch (\Throwable $exportException) {
                \Log::error('Falha ao gerar Excel da receção concluída', [
                    'receiving_id' => $receiving->id,
                    'error' => $exportException->getMessage(),
                ]);
            }

            $settings = AppSetting::first();

            if ($settings) {
                config([
                    'mail.mailers.smtp.host' => $settings->smtp_host,
                    'mail.mailers.smtp.port' => $settings->smtp_port,
                    'mail.mailers.smtp.username' => $settings->smtp_user,
                    'mail.mailers.smtp.password' => $settings->smtp_password,
                    'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                    'mail.from.address' => $settings->smtp_from_address,
                    'mail.from.name' => $settings->smtp_from_name,
                ]);
            }

            Mail::to($to)
                ->cc($cc)
                ->send(new ReceivingFinalizedMail(
                    $summary,
                    $divergences,
                    $newItems,
                    $filePath,
                ));
        } catch (\Throwable $exception) {
            \Log::error('Falha no pós-processamento da receção concluída', [
                'receiving_id' => $receiving->id,
                'error' => $exception->getMessage(),
            ]);
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
