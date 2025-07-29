@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>ENCARREGADOS DE EDUCAÇÃO</strong> | Editar</h1>
        </div>

        <div class="row">
            <!-- Formulário para editar o guardian -->
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Editar Registo</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guardians.guardian.update', $guardian->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $guardian->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="address_1" class="form-label">Morada 1</label>
                                <input type="text" name="address_1" id="address_1" class="form-control" value="{{ old('name', $guardian->address_1) }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="address_2" class="form-label">Morada 2</label>
                                <input type="text" name="address_2" id="address_2" class="form-control" value="{{ old('name', $guardian->address_2) }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="city" class="form-label">Cidade</label>
                                <input type="text" name="city" id="city" class="form-control" value="{{ old('name', $guardian->city) }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="district" class="form-label">Distrito</label>
                                <input type="text" name="district" id="district" class="form-control" value="{{ old('name', $guardian->district) }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="postal_code" class="form-label">Código Postal</label>
                                <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('name', $guardian->postal_code) }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="country_id" class="form-label">País</label>
                                <select name="country_id" id="country_id" class="form-control">
                                    <option value="">Selecione...</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" {{ $guardian->country_id == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }} ({{ $country->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                
                            <div class="mb-3">
                                <label for="mobile_phone" class="form-label">Telemóvel</label>
                                <input type="text" name="mobile_phone" id="mobile_phone" class="form-control" value="{{ old('mobile_phone', $guardian->mobile_phone) }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $guardian->phone) }}">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $guardian->email) }}">
                            </div>
                            <div class="mb-3">
                                <label for="gender_id" class="form-label">Género</label>
                                <select name="gender_id" id="gender_id" class="form-control">
                                    <option value="">Selecione...</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}" {{ $guardian->gender_id == $gender->id ? 'selected' : '' }}>{{ $gender->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                
                            <div class="mb-3">
                                <label for="observations" class="form-label">Observações</label>
                                <textarea name="observations" id="observations" class="form-control">{{ $guardian->observations }}</textarea>
                            </div>
                
                            <div class="mb-3">
                                <label for="is_primary_guardian" class="form-label">Responsável Principal?</label>
                                <input type="hidden" name="is_primary_guardian" value="0">

                                <input type="checkbox" name="is_primary_guardian" id="is_primary_guardian" value="1" {{ $guardian->is_primary_guardian ? 'checked' : '' }}>
                            </div>

                            <div class="mb-3">
                                <label for="relationship_id" class="form-label">Relação</label>
                                <select name="relationship_id" id="relationship_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($relationships as $relationship)
                                        <option value="{{ $relationship->id }}" {{ $guardian->relationship_id == $relationship->id ? 'selected' : '' }}>
                                            {{ $relationship->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="institution_id" class="form-label">Instituição</label>
                                <select name="institution_id" id="institution_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}" {{ $guardian->institution_id == $institution->id ? 'selected' : '' }}>
                                            {{ $institution->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status_id" class="form-label">Estado</label>
                                <select name="status_id" id="status_id" class="form-select">
                                    <option value="1" {{ $guardian->status_id == 1 ? 'selected' : '' }}>Ativo</option>
                                    <option value="2" {{ $guardian->status_id == 2 ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- avatar --}}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Avatar</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guardians.guardian.update.avatar', $guardian->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="avatar-container" id="avatarPreviewImg">
                                <img src="{{ $guardian->avatar 
                                ? route('guardian.avatar', ['id' => $guardian->id]) 
                                : asset('assets/images/general/avatar_default.png') }}" 
                                class="avatar_profile">                            
                            </div>

                            <div class="mb-3">
                                <label for="avatarInput" class="form-label">Avatar</label>
                                <!-- Input para upload do avatar -->
                                <input type="file" id="avatarInput" onchange="openCropperModal()" accept="image/*"> 
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
    $(document).ready(function(){
        $('#guardian').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('guardians.search') }}",
                    dataType: "json",
                    data: { term: request.term },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                $('#guardian').val(ui.item.label);
                $('#guardian_id').val(ui.item.value);
                return false;
            }
        });
    });
</script>

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
    
            // Quando a imagem é carregada, inicializa o Cropper
            image.onload = function() {
                if (cropper) {
                    cropper.destroy(); // Destroi qualquer cropper existente
                }
                cropper = new Cropper(image, {
                    aspectRatio: 1, // Manter proporção quadrada
                    viewMode: 1,
                    autoCropArea: 1,
                });
            };
    
            $('#cropperModal').modal('show'); // Mostra o modal
        };
    
        reader.readAsDataURL(file);
    }
    }
    
    document.getElementById('cropButton').addEventListener('click', function() {
    const canvas = cropper.getCroppedCanvas({
        width: 200, // Largura do corte
        height: 200, // Altura do corte
    });
    
    // Atualizar o preview do avatar
    const avatarPreviewImg = document.querySelector('#avatarPreviewImg img'); // Seleciona a imagem dentro da div
    avatarPreviewImg.src = canvas.toDataURL();

    avatarPreviewImg.style.display = 'block'; // Mostra a imagem após o corte
    
    // Definir o campo oculto com a imagem cortada
    const croppedAvatarInput = document.getElementById('cropped_avatar');
    croppedAvatarInput.value = canvas.toDataURL(); // Armazenar a imagem em base64
    
    // Fechar o modal e destruir o cropper
    $('#cropperModal').modal('hide');
    cropper.destroy();
    cropper = null;
    });
    
    </script>
    
@endsection
