<footer class="footer">
    <div class="container-fluid">
        <div class="row text-muted">
            <div class="col-6 text-start">
                <p class="mb-0">
                    <a class="text-muted" href="https://peixeverde.pt" target="_blank"><strong>Peixe Verde</strong></a>&copy;
                </p>
            </div>
            <div class="col-6 text-end">
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a class="text-muted" href="" target="_blank">Suporte</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-muted" href="" target="_blank">Centro de Ajuda</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
@section('scripts')
    <!-- Inclui o SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Script para exibir o alerta de erro -->
    @if(session('error'))
    <script>
        var corPrincipal = getComputedStyle(document.documentElement).getPropertyValue('--cor-principal').trim();
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: "{{ session('error') }}",
            confirmButtonColor: corPrincipal, // Usa a cor definida em --cor-principal
        });
    </script>
    @endif
    <!-- Script para exibir o alerta de sucesso -->
    @if(session('success'))
    <script>
        var corPrincipal = getComputedStyle(document.documentElement).getPropertyValue('--cor-principal').trim();
        Swal.fire({
            icon: 'success',
            title: 'Sucesso',
            text: "{{ session('success') }}",
            confirmButtonColor: corPrincipal, // Usa a cor definida em --cor-principal
        });
    </script>
    @endif
