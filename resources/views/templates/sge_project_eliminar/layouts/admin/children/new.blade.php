@extends('layouts.master_admin')
@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>CRIANÇAS</strong> | Nova</h1>
        </div>
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Inserir Novo Registo</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('children.child.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                <div class="col-12 col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Avatar</h5>
                                        </div>
                                        <div class="card-body">
                                                <div class="avatar-container" id="avatarPreviewImg">
                                                    <img src="{{ asset('assets/images/general/avatar_default.png') }}" 
                                                    class="avatar_profile">                            
                                                </div>
                                                <div class="mb-3">
                                                    <label for="avatarInput" class="form-label">Avatar</label>
                                                    <input type="file" id="avatarInput" onchange="openCropperModal()" accept="image/*"> 
                                                </div>
                                                <input type="hidden" id="cropped_avatar" name="cropped_avatar">
                                        </div>
                                    </div>
                                </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Data de Nascimento</label>
                                <input type="date" name="dob" id="dob" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="address_1" class="form-label">Morada 1</label>
                                <input type="text" name="address_1" id="address_1" class="form-control" placeholder="Morada 1" required>
                            </div>
                            <div class="mb-3">
                                <label for="address_2" class="form-label">Morada 2</label>
                                <input type="text" name="address_2" id="address_2" class="form-control" placeholder="Morada 2">
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Localidade</label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Localidade" required>
                            </div>
                            <div class="mb-3">
                                <label for="district" class="form-label">Distrito</label>
                                <input type="text" name="district" id="district" class="form-control" placeholder="Distrito" required>
                            </div>
                            <div class="mb-3">
                                <label for="postal_code" class="form-label">Código Postal</label>
                                <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="Código Postal" required>
                            </div>
                            <div class="mb-3">
                                <label for="country_id" class="form-label">País</label>
                                <select name="country_id" id="country_id" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }} ({{ $country->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="gender_id" class="form-label">Género</label>
                                <select name="gender_id" id="gender_id" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}" {{ old('gender_id', $child->gender_id ?? '') == $gender->id ? 'selected' : '' }}>
                                            {{ $gender->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="allergies" class="form-label">Alergias</label>
                                <textarea name="allergies" id="allergies" class="form-control" rows="3">{{ old('allergies', $child->allergies ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="medications" class="form-label">Medicação</label>
                                <textarea name="medications" id="medications" class="form-control" rows="3">{{ old('medications', $child->medications ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="observations" class="form-label">Observações</label>
                                <textarea name="observations" id="observations" class="form-control" rows="3">{{ old('observations', $child->observations ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="guardian" class="form-label">Encarregado de Educação</label>
                                <input type="text" name="guardian" id="guardian" class="form-control" placeholder="Escreva para procurar..." required>
                                <input type="hidden" name="guardian_id" id="guardian_id">
                            </div>
                            <label for="class_group_id" class="form-label">Turma</label>
                                <select name="class_group_id" id="class_group_id" class="form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($class_groups as $class_group)
                                        <option value="{{ $class_group->id }}">
                                            {{ $class_group->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                    <option value="0">Inativo</option>
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
                    data: {
                        term: request.term
                    },
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
    
    const avatarPreviewImg = document.querySelector('#avatarPreviewImg img'); 
    avatarPreviewImg.src = canvas.toDataURL();

    avatarPreviewImg.style.display = 'block'; 
    const croppedAvatarInput = document.getElementById('cropped_avatar');
    croppedAvatarInput.value = canvas.toDataURL(); 
    

    $('#cropperModal').modal('hide');
    cropper.destroy();
    cropper = null;
    });
</script>
    
@endsection
