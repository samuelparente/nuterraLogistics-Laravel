@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>TURMAS</strong> | Nova</h1>
        </div>

        <div class="row">
            <!-- Formulário para inserir nova turma -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Inserir Novo Registo</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('classes.class.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                           
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="classroom_id" class="form-label">Associar a Sala</label>
                                <select name="classroom_id" id="classroom_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="observations" class="form-label">Observações</label>
                                <textarea name="observations" id="observations" class="form-control"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="institution_id" class="form-label">Instituição</label>
                                <select name="institution_id" id="institution_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="active" class="form-label">Estado</label>
                                <select name="status_id" id="status_id" class="form-select">
                                    <option value="1">Ativo</option>
                                    <option value="2">Inativo</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Inserir Registo</button>
                        </form>
                    </div>
                </div>
            </div>
             <!-- Modal para o Cropper -->
            <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cropperModalLabel">Cortar Capa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img id="cropperImage" style="width: 100%; max-height: 400px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="cropButton">Cortar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main><script>
        let cropper;
        
        function openCropperModal() {
        const input = document.getElementById('coverInput');
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
        
            reader.onload = function(e) {
                const image = document.getElementById('cropperImage');
                image.src = e.target.result;
        
                // Quando a imagem é carregada, inicializa o Cropper
                image.onload = function() {
                    if (cropper) {
                        cropper.destroy(); // Destroi qualquer cropper existente
                    }
                     cropper = new Cropper(image, {
                        aspectRatio: 4 / 3, // Define proporção fixa de 4:3 (800x600 ou 1280x800)
                        viewMode: 1,
                        autoCropArea: 1,
                        movable: true,
                        zoomable: true,
                        scalable: false,
                        cropBoxResizable: true,
                    });

                };
        
                $('#cropperModal').modal('show'); // Mostra o modal
            };
        
            reader.readAsDataURL(file);
        }
        }
        
        document.getElementById('cropButton').addEventListener('click', function() {
        const canvas = cropper.getCroppedCanvas({
            width: 1280, // Largura do corte
            height: 800, // Altura do corte
        });
        
        // Atualizar o preview da capa
        const coverPreviewImg = document.querySelector('#coverPreviewImg img'); // Seleciona a imagem dentro da div
        coverPreviewImg.src = canvas.toDataURL();
    
        coverPreviewImg.style.display = 'block'; // Mostra a imagem após o corte
        
        // Definir o campo oculto com a imagem cortada
        const croppedCoverInput = document.getElementById('cropped_cover');
        croppedCoverInput.value = canvas.toDataURL(); // Armazenar a imagem em base64
        
        // Fechar o modal e destruir o cropper
        $('#cropperModal').modal('hide');
        cropper.destroy();
        cropper = null;
        });
        
        </script>
    
@endsection
