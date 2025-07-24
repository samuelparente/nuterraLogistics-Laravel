@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>UTILIZADORES</strong> | Editar</h1>
        </div>

        <div class="row">
            <!-- Formulário para editar o user -->
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Editar Registo</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.user.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Password</label>
                                <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                            </div>

                            <div class="mb-3">
                                <label for="status_id" class="form-label">Estado</label>
                                <select name="status_id" id="status_id" class="form-select">
                                    <option value="1" {{ $user->status_id == 1 ? 'selected' : '' }}>Ativo</option>
                                    <option value="2" {{ $user->status_id == 2 ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="type_id" class="form-label">Tipo de Utilizador</label>
                                <select name="type_id" id="type_id" class="form-select">
                                    @foreach($userTypes as $type)
                                        <option value="{{ $type->id }}" {{ $user->type_id == $type->id ? 'selected' : '' }}>
                                            {{ $type->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="institution_id" class="form-label">Instituição</label>
                                <select name="institution_id" id="institution_id" class="form-select">
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}" {{ $user->institution_id == $institution->id ? 'selected' : '' }}>
                                            {{ $institution->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Avatar --}}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Avatar</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.user.update.avatar', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="avatar-container text-center mb-3">
                                <img src="{{ $user->avatar 
                                ? route('user.avatar', ['id' => $user->id]) 
                                : asset('assets/images/general/avatar_default.png') }}" 
                                class="avatar_profile rounded-circle" width="150">                            
                            </div>

                            <div class="mb-3">
                                <label for="avatarInput" class="form-label">Alterar Avatar</label>
                                <input type="file" id="avatarInput" onchange="openCropperModal()" accept="image/*" class="form-control"> 
                            </div>
                        
                            <input type="hidden" id="cropped_avatar" name="cropped_avatar">
                            
                            <button type="submit" class="btn btn-primary">Atualizar Avatar</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal para o Cropper -->
            <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cropperModalLabel">Cortar Avatar</h5>
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
</main>

<script>
    let cropper;
    
    function openCropperModal() {
        const input = document.getElementById('avatarInput');
        const file = input.files[0];
    
        if (file) {
            const reader = new FileReader();
    
            reader.onload = function(e) {
                const image = document.getElementById('cropperImage');
                image.src = e.target.result;
    
                image.onload = function() {
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                    });
                };
    
                $('#cropperModal').modal('show');
            };
    
            reader.readAsDataURL(file);
        }
    }
    
    document.getElementById('cropButton').addEventListener('click', function() {
        const canvas = cropper.getCroppedCanvas({
            width: 200,
            height: 200,
        });
    
        const avatarPreviewImg = document.querySelector('.avatar-container img');
        avatarPreviewImg.src = canvas.toDataURL();
    
        const croppedAvatarInput = document.getElementById('cropped_avatar');
        croppedAvatarInput.value = canvas.toDataURL();
    
        $('#cropperModal').modal('hide');
        cropper.destroy();
        cropper = null;
    });
</script>

@endsection
