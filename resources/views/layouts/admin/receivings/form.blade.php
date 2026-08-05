@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Listagem dos Produtos</h1>
    <span class="text-muted">Pedido #{{ $receiving->id }} > {{ $receiving->supplier->name }} </span>
    <!-- Botões de ação -->
    <div class="row mb-3">
        <div class="col-12 col-lg-12 text-end">
            <div class="mt-3 mb-3 action-buttons-header-mobile">

                {{-- Entrada com scanner (ativo apenas se a receção estiver em curso) --}}
                <a 
                    href="{{ route('receivings.items.singleScanner', $receiving->id) }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $receiving->status_id === 7 ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                    {{ $receiving->status_id === 7 ? '' : 'disabled' }}>
                    <i class="bi bi-upc-scan"></i> Entrada com Scanner
                </a>

                {{-- Criar e adicionar Produto Extra (ativo apenas se a receção estiver em curso) --}}
                <a 
                    href="{{ route('receivings.items.createProduct', $receiving->id) }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $receiving->status_id === 7 ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                    {{ $receiving->status_id === 7 ? '' : 'disabled' }}>
                    <i class="bi bi-plus-circle"></i> Criar e Adicionar Produto Extra
                </a> 

                 {{-- Adicionar Produto Extra (ativo apenas se a receção estiver em curso) --}}
                <a 
                    href="{{ route('receivings.items.single', $receiving->id) }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $receiving->status_id === 7 ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                    {{ $receiving->status_id === 7 ? '' : 'disabled' }}>
                    <i class="bi bi-plus-circle"></i> Adicionar Produto Extra
                </a> 

                <!-- {{-- Voltar ao Painel de Pendentes --}}
                <a 
                    href="{{ route('receivings.pending') }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary">
                    <i class="bi bi-box"></i> Entradas em Curso
                </a> -->
                {{-- voltar --}}
                <a 
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                    href="{{ route('receivings.pending') }}">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>

            </div>
        </div>
    </div>


    {{-- Breadcrumbs --}}
    @include('layouts.admin.partials.breadcrumbs', [
      'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
    ])

    @if (in_array($receiving->erp_submission_status, ['submitting', 'uncertain'], true))
        <div class="alert alert-warning mt-3">
            <strong>Receção bloqueada.</strong>
            Existe uma submissão Sage em curso ou com resultado incerto. Não repita nem altere a receção;
            confirme primeiro o documento no Sage e contacte o suporte se necessário.
        </div>
    @endif

  
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Produtos Recebidos</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-hover align-middle table-bordered">
                        <thead>
                            <tr>
                                <th title="SKU"><i class="bi bi-hash table-icons"></i></th>
                                <th title="Código Barras"><i class="bi bi-upc-scan table-icons"></i></th>
                                <th title="Nome"><i class="bi bi-card-text table-icons"></i></th>
                                <th title="Encomendado"><i class="bi bi-send-plus table-icons"></i></th>
                                <th title="Recebido"><i class="bi bi-clipboard-check table-icons"></i></th>
                                <th class="text-center"><i class="bi bi-gear-fill table-icons" style="width:1.5rem;" title="Ações"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->product_sku }}</td>
                                    <td>{{ $item->product_barcode }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td style="width:100px;">{{ $item->ordered_qty }}</td>
                                    <td>
                                        {{-- Mostrar lotes existentes --}}
                                        @php
                                            $totalReceived = $item->batches->sum('quantity');
                                            if($totalReceived!=$item->ordered_qty){
                                                $qty_error=true;
                                            }
                                            else{
                                                $qty_error=false;
                                            }
                                        @endphp

                                        @if($item->batches->count())
                                            <div>
                                                <span class="badge bg-dark mt-1 mb-1" >Total: {{ $totalReceived }}</span>
                                                @if($qty_error)
                                                    <span class="badge bg-danger mt-1 mb-1" title="Quantidade diferente da encomendada"><i class="bi bi-exclamation-triangle"></i></span>
                                                @else
                                                    <span class="badge bg-success mt-1 mb-1" title="Quantidade correcta"><i class="bi bi-check-circle"></i></span>
                                                @endif
                                            </div>

                                            @foreach ($item->batches as $batch)
                                            <span class="badge bg-light text-dark mt-1 mb-1 d-flex justify-content-between align-items-center px-2 py-2">
                                                <span class="text-start" style="width: 90%;">
                                                    {{ $batch->batch_number }} |
                                                    {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }} |
                                                    {{ $batch->quantity }}
                                                </span>

                                                <span class="ms-1" style="width: 10%;font-size:0.9rem;">
                                                    <a href="#" 
                                                        onclick="confirmDelete('batch-{{ $batch->id }}')" 
                                                        class="text-danger text-decoration-none"
                                                        title="Eliminar Lote" 
                                                        data-bs-toggle="tooltip">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>

                                                    <form id="delete-form-batch-{{ $batch->id }}" 
                                                        action="{{ route('receivings.batches.destroy', $batch->id) }}" 
                                                        method="POST" 
                                                        style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </span>
                                            </span>

                                        @endforeach

                                        @else
                                            <span class="text-muted small">Nenhum lote inserido.</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        
                                        <button 
                                            class="btn btn-sm btn-general btn-outline-secondary m-1"
                                            onclick="addBatch(this)"
                                            data-item-id="{{ $item->id }}"
                                            data-product-name="{{ $item->product_name }}"
                                            data-url="{{ route('receivings.items.batches.store', ['item' => $item->id]) }}"
                                            title="Adicionar Lote e Quantidade" 
                                            data-toggle="tooltip" data-placement="bottom"
                                            >
                                            
                                            <i class="bi bi-capsule"></i>
                                        </button>

                                        @if ($item->order_item_id == 0)
                                            <a class="btn btn-sm btn-general btn-outline-danger m-1" 
                                                href="#" 
                                                onclick="confirmDelete({{ $item->id }})" 
                                                title="Eliminar Lote" 
                                                data-toggle="tooltip" data-placement="bottom">                                            
                                                <i class="bi bi-trash table-icon-remove"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->id }}" 
                                                action="{{ route('receivings.items.destroy', $item->id) }}" 
                                                method="POST" 
                                                style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <a class="btn btn-sm btn-outline-secondary btn-general text-muted disabled m-1" 
                                                href="#" 
                                                tabindex="-1" 
                                                aria-disabled="true"
                                                title="Item original. Não pode remover." 
                                                data-toggle="tooltip" data-placement="bottom">
                                                <i class="bi bi-trash table-icon-remove"></i>
                                            </a>                                     
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
               

                {{-- Formulário de receção --}}
                <form
                    id="receiving-finalization-form"
                    method="POST"
                    action="{{ route('receivings.store', ['order' => $order->id, 'supplier' => $supplier->id]) }}"
                    data-no-loader
                >
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                    <input type="hidden" name="preview_token" id="preview_token" value="">

                    <div class="row">
                        <div class="col-12 col-lg-4">
                            {{-- Destinatários do email de receção finalizada --}}
                            @if(!empty($mailTo) || !empty($mailCc))
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Destinatários do email</h5>
                                    </div>
                                    <div class="card-body">

                                        {{-- PARA: --}}
                                        @if(!empty($mailTo))
                                            <h6 class="mb-2">Para:</h6>
                                            @foreach($mailTo as $email)
                                                <div class="form-check mb-1">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="mail_to[]"
                                                        value="{{ $email }}"
                                                        id="mail_to_{{ $loop->index }}"
                                                        checked
                                                    >
                                                    <label class="form-check-label" for="mail_to_{{ $loop->index }}">
                                                        {{ $email }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif

                                        {{-- CC: --}}
                                        @if(!empty($mailCc))
                                            <hr class="my-3">
                                            <h6 class="mb-2">CC:</h6>
                                            @foreach($mailCc as $email)
                                                <div class="form-check mb-1">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="mail_cc[]"
                                                        value="{{ $email }}"
                                                        id="mail_cc_{{ $loop->index }}"
                                                        checked
                                                    >
                                                    <label class="form-check-label" for="mail_cc_{{ $loop->index }}">
                                                        {{ $email }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-12 col-lg-8">
                            {{-- Notas --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Notas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <!-- <label for="notes" class="form-label">Notas</label> -->
                                        <textarea name="notes" id="notes" class="form-control" rows="8">{{ $receiving->notes ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>    
                     </div>
                
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            {{-- Checkbox de incluir impostos --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Impostos do documento</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="modo_insercao"
                                                id="modo_anterior"
                                                value="anterior"
                                                @checked(old('modo_insercao', 'anterior') === 'anterior')
                                                required
                                            >
                                            <label class="form-check-label" for="modo_anterior">
                                                Usar definição de fatura anterior
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="modo_insercao"
                                                id="modo_com_impostos"
                                                value="com_impostos"
                                                @checked(old('modo_insercao') === 'com_impostos')
                                            >
                                            <label class="form-check-label" for="modo_com_impostos">
                                                Inserir com impostos
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="modo_insercao"
                                                id="modo_sem_impostos"
                                                value="sem_impostos"
                                                @checked(old('modo_insercao') === 'sem_impostos')
                                            >
                                            <label class="form-check-label" for="modo_sem_impostos">
                                                Inserir sem impostos
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            {{--cambio --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Moeda da receção</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="moeda_id" class="form-label">Moeda</label>
                                        <select name="moedaId" id="moeda_id" class="form-select" required>
                                            <option value="EUR" @selected(old('moedaId', $receiving->fx_currency ?? 'EUR') === 'EUR')>EUR</option>
                                            <option value="USD" @selected(old('moedaId', $receiving->fx_currency ?? 'EUR') === 'USD')>USD</option>
                                        </select>

                                        <label for="taxa_cambio" class="mt-3 form-label">Taxa de câmbio</label>
                                        <input
                                            id="taxa_cambio"
                                            type="number"
                                            step="0.000000000001"
                                            min="0.000000000001"
                                            value="{{ old('taxaCambio', $receiving->fx_rate_to_eur ?? '1.000000000000') }}"
                                            name="taxaCambio"
                                            class="form-control"
                                            required
                                        >
                                        <div id="taxa_cambio_help" class="form-text">
                                            Para USD: 1 USD = taxa indicada em EUR.
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>    
                    </div>    

                    {{-- Ações --}}
                    <div class="d-flex gap-2">
                        {{-- Guardar (POST para store) --}}
                        <button type="submit"
                                formaction="{{ route('receivings.store', [$order->id, $supplier->id]) }}"
                                class="btn btn-secondary">
                            <i class="bi bi-floppy"></i> Guardar
                        </button>

                        {{-- Pré-visualizar antes de criar o documento Sage --}}
                        <button
                            type="button"
                            id="btn-preview-receiving"
                            class="btn btn-primary"
                            data-preview-url="{{ route('receivings.preview', [$order->id, $supplier->id]) }}"
                            data-finalize-url="{{ route('receivings.finalize', [$order->id, $supplier->id]) }}"
                        >
                            <i class="bi bi-eye"></i> Pré-visualizar finalização
                        </button>
                    </div>
                </form>

                <div
                    class="modal fade"
                    id="receiving-preview-modal"
                    tabindex="-1"
                    aria-labelledby="receiving-preview-title"
                    aria-hidden="true"
                >
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="receiving-preview-title">
                                        Pré-visualização do documento Sage
                                    </h5>
                                    <div id="preview-header-meta" class="small text-muted mt-1"></div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info py-2">
                                    O Sage calculará descontos, impostos e totais. Confirme abaixo os preços em EUR que serão enviados.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>SKU / Produto</th>
                                                <th class="text-end">Qtd.</th>
                                                <th class="text-end">Último preço Sage</th>
                                                <th class="text-end">Taxa anterior</th>
                                                <th class="text-end">Nova taxa</th>
                                                <th class="text-end">Preço a enviar</th>
                                                <th class="text-end">Desconto</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="preview-products-body"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancelar e corrigir
                                </button>
                                <button type="button" class="btn btn-primary" id="btn-confirm-receiving">
                                    <i class="bi bi-check2-circle"></i> Confirmar e inserir no Sage
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
  </div>
</main>
<script>
    const csrfToken = '{{ csrf_token() }}';

    (() => {
        const form = document.getElementById('receiving-finalization-form');
        const currencyInput = document.getElementById('moeda_id');
        const rateInput = document.getElementById('taxa_cambio');
        const rateHelp = document.getElementById('taxa_cambio_help');
        const previewButton = document.getElementById('btn-preview-receiving');
        const confirmButton = document.getElementById('btn-confirm-receiving');
        const previewToken = document.getElementById('preview_token');
        const previewBody = document.getElementById('preview-products-body');
        const previewMeta = document.getElementById('preview-header-meta');
        const modalElement = document.getElementById('receiving-preview-modal');

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const showError = (message) => {
            if (typeof window.showInfoSwal === 'function') {
                window.showInfoSwal('Não foi possível preparar a receção', escapeHtml(message));
                return;
            }

            window.alert(message);
        };

        const formatPrice = (value) => {
            if (value === null || value === undefined || value === '') return '—';

            return Number(value).toLocaleString('pt-PT', {
                minimumFractionDigits: 5,
                maximumFractionDigits: 6,
            }) + ' EUR';
        };

        const invalidatePreview = () => {
            previewToken.value = '';
        };

        const syncCurrency = (clearUsdRate = false) => {
            if (currencyInput.value === 'EUR') {
                rateInput.value = '1.000000000000';
                rateInput.readOnly = true;
                rateHelp.textContent = 'Receções em EUR não têm conversão cambial.';
            } else {
                rateInput.readOnly = false;

                if (clearUsdRate && Number(rateInput.value) === 1) {
                    rateInput.value = '';
                }

                rateHelp.textContent = 'Introduza a taxa manual: 1 USD = taxa indicada em EUR.';
            }

            invalidatePreview();
        };

        const appendCell = (row, text, className = '') => {
            const cell = document.createElement('td');
            cell.textContent = text;
            cell.className = className;
            row.appendChild(cell);
            return cell;
        };

        const renderPreview = (preview) => {
            previewBody.replaceChildren();
            previewMeta.textContent = [
                `Fornecedor: ${preview.supplier.name}`,
                `Receção #${preview.receiving_id}`,
                `Moeda: ${preview.currency}`,
                preview.currency === 'USD' ? `1 USD = ${preview.rate} EUR` : 'Sem conversão cambial',
                `${preview.line_count} linha(s) por lote`,
            ].join(' | ');

            preview.items.forEach((item) => {
                const row = document.createElement('tr');
                const productCell = appendCell(row, '');

                const sku = document.createElement('strong');
                sku.textContent = item.sku;
                productCell.appendChild(sku);
                productCell.appendChild(document.createElement('br'));
                productCell.appendChild(document.createTextNode(item.name));

                appendCell(row, String(item.quantity), 'text-end');
                appendCell(row, formatPrice(item.last_price_eur), 'text-end text-nowrap');
                appendCell(
                    row,
                    item.previous_rate ? `${item.previous_rate} (${item.previous_currency})` : '—',
                    'text-end text-nowrap'
                );
                appendCell(
                    row,
                    preview.currency === 'USD' ? preview.rate : '1.000000000000',
                    'text-end text-nowrap'
                );
                appendCell(row, formatPrice(item.final_price_eur), 'text-end text-nowrap fw-semibold');
                appendCell(
                    row,
                    item.discount_percent === null ? '—' : `${item.discount_percent}%`,
                    'text-end text-nowrap'
                );

                const statusCell = appendCell(row, '');
                const badge = document.createElement('span');
                badge.className = `badge bg-${item.status_class}`;
                badge.textContent = item.status_label;
                statusCell.appendChild(badge);

                const formula = document.createElement('div');
                formula.className = 'small text-muted mt-1';
                formula.textContent = item.formula;
                statusCell.appendChild(formula);

                previewBody.appendChild(row);
            });
        };

        currencyInput.addEventListener('change', () => syncCurrency(true));
        rateInput.addEventListener('input', invalidatePreview);
        form.querySelectorAll('input[name="modo_insercao"]').forEach((input) => {
            input.addEventListener('change', invalidatePreview);
        });

        previewButton.addEventListener('click', async () => {
            if (! form.reportValidity()) return;

            const originalHtml = previewButton.innerHTML;
            previewButton.disabled = true;
            previewButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> A calcular...';

            try {
                const response = await fetch(previewButton.dataset.previewUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                });

                const data = await response.json();

                if (! response.ok || ! data.success) {
                    throw new Error(data.message || 'Não foi possível calcular os preços.');
                }

                previewToken.value = data.token;
                renderPreview(data.preview);
                window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            } catch (error) {
                showError(error.message || 'Ocorreu um erro inesperado.');
            } finally {
                previewButton.disabled = false;
                previewButton.innerHTML = originalHtml;
            }
        });

        confirmButton.addEventListener('click', () => {
            if (! previewToken.value) {
                showError('A pré-visualização expirou. Gere uma nova.');
                return;
            }

            confirmButton.disabled = true;
            window.bootstrap.Modal.getInstance(modalElement)?.hide();
            form.action = previewButton.dataset.finalizeUrl;

            if (typeof window.showLoadingSwal === 'function') {
                window.showLoadingSwal('A inserir no Sage...', 'Não feche esta página.');
            }

            form.submit();
        });

        syncCurrency(false);
    })();
</script>


@endsection
