<footer class="footer">
    <div class="container-fluid">
        <div class="row text-muted">
            <div class="col-6 text-start">
                <p class="mb-0">
                    <a class="text-muted" href="" target="_blank"><strong>NUTERRA LDA.</strong></a>&copy;
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
@vite('resources/js/app.js')  
<script>
    window.flashSuccess = @json(session('success'));
    window.flashError = @json(session('error'));
</script>
