<div
    id="expiry-alert-bar"
    style="
        display:none;
        position: sticky;
        top: 0;
        z-index: 1050;
        background: #dc2626;
        color: #ffffff;
        padding: 10px 16px;
        font-size: 0.8rem;
        font-weight: 400;
        text-align: center;
        justify-content: center;
        align-items: center;
        border:0px;
    "
>
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div id="expiry-alert-text">
            <!-- texto preenchido via JS -->
        </div>

        <button
            type="button"
            id="expiry-alert-close"
            style="
                background: transparent;
                border: none;
                color: #ffffff;
                font-size: 20px;
                line-height: 1;
                cursor: pointer;
            "
            aria-label="Fechar"
        >
            &times;
        </button>
    </div>
</div>

<script>
async function loadExpiryAlertBar() {
    try {
        const res = await fetch(@json(route('alerts.expiry.count')) + '?months=6', {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return;

        const data = await res.json();
        if (!data?.count || data.count <= 0) return;

        document.getElementById('expiry-alert-text').innerHTML = `
            Existem <strong>${data.count}</strong> lotes a expirar nos próximos
            <strong>${data.months}</strong> meses.
            <a href="{{ route('batchesexpirydates.index') }}"
               style="color:#fff; text-decoration:underline; margin-left:8px;">
                Ver lista
            </a>
        `;

        document.getElementById('expiry-alert-bar').style.display = 'flex';
    } catch (e) {
        console.error(e);
    }
}

document.addEventListener('DOMContentLoaded', loadExpiryAlertBar);

document.getElementById('expiry-alert-close')?.addEventListener('click', async () => {
    document.getElementById('expiry-alert-bar').style.display = 'none';

    try {
        await fetch(@json(route('alerts.expiry.hide')), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': @json(csrf_token()),
                'Accept': 'application/json'
            }
        });
    } catch (e) {}
});
</script>
