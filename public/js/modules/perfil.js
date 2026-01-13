/**
 * Gestión de perfil de usuario (foto y datos)
 */

$(function () {
    // Solo inicializar si existe el modal
    if (!$('#modalPerfil').length) return;

    // Preview de foto
    $('#inputFoto').on('change', function () {
        var file = this.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'Archivo demasiado grande (máx 2MB)', 'error');
            $(this).val('');
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#previewFoto').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    });

    // Subir foto por AJAX
    $('#formFoto').on('submit', function (e) {
        e.preventDefault();
        var fd = new FormData(this);
        $.ajax({
            url: APP.url('perfil/foto'),
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (resp) {
                if (resp.ok) {
                    Swal.fire('Listo', resp.msg, 'success');
                    var imgTop = $('img.img-profile');
                    if (imgTop.length) {
                        imgTop.attr('src', resp.url + '?t=' + new Date().getTime());
                    }
                } else {
                    Swal.fire('Error', resp.msg || 'Error al subir', 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', 'Error al subir (ver consola)', 'error');
                console.error(xhr);
            }
        });
    });

    // Guardar datos perfil
    $('#formDatos').on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            url: APP.url('perfil/guardar'),
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function (resp) {
                if (resp.ok) {
                    Swal.fire('Listo', resp.msg, 'success').then(() => {
                        $('span.user-nombre').text($('input[name="nombre"]').val());
                    });
                } else {
                    Swal.fire('Error', resp.msg || 'No se pudo actualizar', 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', 'Error al guardar (ver consola)', 'error');
                console.error(xhr);
            }
        });
    });
});
