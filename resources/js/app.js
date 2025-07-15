import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

window.Alpine = Alpine;

Alpine.start();

// Flash messages (Laravel sessions via Blade)
window.addEventListener('DOMContentLoaded', () => {

    const corPrincipal = getComputedStyle(document.documentElement).getPropertyValue('--cor-principal').trim();

    // Flash success
    if (window.flashSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Sucesso',
            text: window.flashSuccess,
            confirmButtonColor: corPrincipal,
        });
    }

    // Flash error
    if (window.flashError) {
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: window.flashError,
            confirmButtonColor: corPrincipal,
        });
    }

    // Confirm delete
    window.confirmDelete = function (id) {
        Swal.fire({
            title: 'Eliminar Registo?',
            text: 'Esta ação não pode ser anulada!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: corPrincipal,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'A eliminar...',
                    text: 'Por favor aguarde',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                document.getElementById('delete-form-' + id).submit();
            }
        });
    };

    // Confirm delete para múltiplos IDs (ex: storeId + clientId)
    window.confirmDeleteWithIds = function (id1, id2) {
        Swal.fire({
            title: 'Eliminar Registo?',
            text: 'Esta ação não pode ser anulada!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: corPrincipal,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'A eliminar...',
                    text: 'Por favor aguarde',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                document.getElementById('delete-form-' + id1 + '-' + id2).submit();
            }
        });
    };


    // Confirm sensitive changes
    window.confirmSensitiveUpdate = function (storeId) {
        let changed = false;
        const sensitiveFields = ['db_host', 'db_port', 'db_username', 'db_password', 'database', 'db_connection'];

        sensitiveFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input) return;

            const original = input.getAttribute('data-original') ?? '';
            const current = input.value ?? '';

            const normalizedOriginal = original.trim();
            const normalizedCurrent = current.trim();

            if (normalizedOriginal !== normalizedCurrent) {
                changed = true;
            }
        });

        if (changed) {
            const corPrincipal = getComputedStyle(document.documentElement).getPropertyValue('--cor-principal').trim();

            Swal.fire({
                title: 'Alterar dados sensíveis?',
                text: 'Vai alterar dados sensíveis da base de dados. Esta ação pode ter impacto no funcionamento da loja.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: corPrincipal,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Alterar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'A atualizar...',
                        text: 'Por favor aguarde',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById('update-form-' + storeId).submit();
                }
            });
        } else {
            document.getElementById('update-form-' + storeId).submit();
        }
    };


});

// Cropper para avatars
let cropper = null;

// Abrir o modal e inicializar o cropper
window.openCropperModal = function () {
    const input = document.getElementById('avatarInput');
    const file = input?.files?.[0];

    if (!file) return;

    const reader = new FileReader();

    reader.onload = function (e) {
        const image = document.getElementById('cropperImage');
        if (!image) return;

        image.src = e.target.result;

        image.onload = function () {
            if (cropper) cropper.destroy();

            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
            });

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('cropperModal'));
            modal.show();
        };
    };

    reader.readAsDataURL(file);
};

// Quando a página carregar, liga o botão de cortar
window.addEventListener('DOMContentLoaded', () => {
    const cropButton = document.getElementById('cropButton');
    if (!cropButton) return;

    cropButton.addEventListener('click', function () {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({ width: 200, height: 200 });
        if (!canvas) return;

        const dataUrl = canvas.toDataURL();

        // Atualizar preview do avatar
        const avatarPreviewImg = document.querySelector('#avatarPreviewImg img');
        if (avatarPreviewImg) {
            avatarPreviewImg.src = dataUrl;
        }

        // Guardar base64 no input hidden
        const inputHidden = document.getElementById('cropped_avatar');
        if (inputHidden) {
            inputHidden.value = dataUrl;
        }

        // Fechar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('cropperModal'));
        modal?.hide();

        cropper.destroy();
        cropper = null;
    });
});

// Atualizar nome do ficheiro selecionado e abrir modal
window.handleAvatarSelect = function (input) {
    const fileName = input.files?.[0]?.name || 'Nenhum ficheiro selecionado';
    const fileLabel = document.getElementById('avatarFileName');
    if (fileLabel) fileLabel.textContent = fileName;

    openCropperModal();
};

// Cropper para as marcas
let cropperLogo;
let selectedLogoFile;
window.handleLogoSelect = function(input) {
    const file = input.files[0];
    if (!file) return;

    document.getElementById('logoFileName').innerText = file.name;

    const reader = new FileReader();
    reader.onload = function (e) {
        const image = document.getElementById('cropperImage');
        image.src = e.target.result;

        // Abre o modal
        const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
        cropperModal.show();

        // Inicializa cropper
        if (window.cropperLogo) {
            window.cropperLogo.destroy();
        }
        window.cropperLogo = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            movable: true,
            zoomable: true,
            rotatable: false,
            scalable: false,
        });
    };
    reader.readAsDataURL(file);
}

// Botão cortar
document.getElementById('cropButton')?.addEventListener('click', function () {
    if (!window.cropperLogo) return;
    const canvas = window.cropperLogo.getCroppedCanvas({ width: 300, height: 300 });
    const croppedDataUrl = canvas.toDataURL('image/png');
    document.getElementById('logoImage').src = croppedDataUrl;
    document.getElementById('cropped_logo').value = croppedDataUrl;
    bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
    window.cropperLogo.destroy();
    window.cropperLogo = null;
});

// Reset preview ao cancelar seleção
document.getElementById('logoInput')?.addEventListener('change', function (e) {
    if (!e.target.files.length) {
        document.getElementById('logoFileName').innerText = 'Nenhum ficheiro selecionado';
        document.getElementById('cropped_logo').value = '';
        document.getElementById('logoImage').src = document.getElementById('logoImage').dataset.logoDefault;
    }
});

window.openMenuItemModal = function(item = null, items = [], saveUrl = null, csrfToken = null, countryPtId = null) {
    let parentId = item?.parent_id || '';
    let name = item?.translations?.[countryPtId]?.name || '';
    let order = item?.order || 0;
    let isVisible = item?.is_visible ?? 1;
    let itemId = item?.id || '';

    // Parent options
    let parentOptions = `<option value="">Nenhum (nível raiz)</option>`;
    items.forEach(existing => {
        if (item && existing.id === item.id) return; // não listar ele mesmo
        let label = existing.translations?.[countryPtId]?.name || '(sem nome)';
        let selected = parentId == existing.id ? 'selected' : '';
        parentOptions += `<option value="${existing.id}" ${selected}>${label}</option>`;
    });

    Swal.fire({
        title: itemId ? 'Editar Item' : 'Novo Item',
        html: `
            <form id="menuItemForm" method="POST" action="${saveUrl}">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="id" value="${itemId}">
                <input type="hidden" name="type" value="url">
                <input type="hidden" name="target" value="_self">

                <div class="mb-3 text-start">
                    <label class="form-label">Parent Item</label>
                    <select class="form-select" name="parent_id">
                        ${parentOptions}
                    </select>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Nome (PT)</label>
                    <input type="hidden" name="translations[${countryPtId}][country_id]" value="${countryPtId}">
                    <input type="text" name="translations[${countryPtId}][name]" class="form-control" value="${name}">
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Ordem</label>
                    <input type="number" name="order" class="form-control" value="${order}">
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Visível</label>
                    <select name="is_visible" class="form-select">
                        <option value="1" ${isVisible == 1 ? 'selected' : ''}>Sim</option>
                        <option value="0" ${isVisible == 0 ? 'selected' : ''}>Não</option>
                    </select>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        preConfirm: () => {
            document.getElementById('menuItemForm').submit();
            return false;
        }
    });
};

let cropperImage, cropperIcon;

// ICON CROP
function handleIconCrop(e) {
    const file = e.target.files[0];
    if (!file) return;

    const mimeType = file.type;
    const reader = new FileReader();

    reader.onload = function (evt) {
        Swal.fire({
            title: 'Recortar Ícone',
            html: `
                <div style="max-width: 100%; max-height: 300px;">
                    <img id="cropperIconModal" src="${evt.target.result}" style="width: 100%; max-height: 250px;">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Recortar',
            cancelButtonText: 'Cancelar',
            willOpen: () => {
                const image = document.getElementById('cropperIconModal');
                cropperIcon = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    responsive: true,
                });
            },
            preConfirm: () => {
                const canvas = cropperIcon.getCroppedCanvas({ width: 48, height: 48 });
                const dataUrl = canvas.toDataURL(mimeType);
                document.getElementById('currentIcon').src = dataUrl;
                document.getElementById('cropped_icon').value = dataUrl;
                cropperIcon.destroy();
                cropperIcon = null;
            },
            willClose: () => {
                if (cropperIcon) {
                    cropperIcon.destroy();
                    cropperIcon = null;
                }
            }
        });
    };

    reader.readAsDataURL(file);
}

// IMAGE CROP
function handleImageCrop(e) {
    const file = e.target.files[0];
    if (!file) return;

    const mimeType = file.type;
    const reader = new FileReader();

    reader.onload = function (evt) {
        Swal.fire({
            title: 'Recortar Imagem',
            html: `
                <div style="max-width: 100%; max-height: 500px;">
                    <img id="cropperImageModal" src="${evt.target.result}" style="width: 100%; max-height: 400px;">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Recortar',
            cancelButtonText: 'Cancelar',
            willOpen: () => {
                const image = document.getElementById('cropperImageModal');
                cropperImage = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    responsive: true,
                });
            },
            preConfirm: () => {
                const canvas = cropperImage.getCroppedCanvas({ width: 400, height: 400 });
                const dataUrl = canvas.toDataURL(mimeType);
                document.getElementById('currentImage').src = dataUrl;
                document.getElementById('cropped_image').value = dataUrl;
                cropperImage.destroy();
                cropperImage = null;
            },
            willClose: () => {
                if (cropperImage) {
                    cropperImage.destroy();
                    cropperImage = null;
                }
            }
        });
    };

    reader.readAsDataURL(file);
}

// REGISTRA OS LISTENERS
document.addEventListener('DOMContentLoaded', () => {
    const iconInput = document.getElementById('iconInput');
    const imageInput = document.getElementById('imageInput');

    if (iconInput) {
        iconInput.addEventListener('change', handleIconCrop);
    }

    if (imageInput) {
        imageInput.addEventListener('change', handleImageCrop);
    }
});
