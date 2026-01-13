/**
 * Gestión de movimientos (asignación, préstamo, retiro) con Select2
 */

$(document).ready(function () {
    // Solo inicializar si existen los elementos
    if (!$('#usuario').length && !$('#buscador_asignar').length) return;

    // ==============================
    // Autocompletar usuario
    // ==============================
    $('#usuario').on('input', function () {
        $('#usuarioId').val('');
    }).on('keyup', function () {
        let usuario = $(this).val().trim();
        if (usuario.length >= 3) {
            var url = window.location.pathname.includes('movimientos')
                ? APP.url('movimientos/getUsuariosSugeridos')
                : APP.url('bienes/getUsuariosSugeridos');
            $.ajax({
                url: url,
                method: "GET",
                data: { usuario },
                dataType: "json",
                success: function (response) {
                    $('#usuarioSuggestions').empty().hide();
                    if (response.length > 0) {
                        response.forEach(function (persona) {
                            $('#usuarioSuggestions').append(
                                `<li class="list-group-item suggestion-item" data-id="${persona.id}">
                                    ${persona.nombre_completo}
                                </li>`
                            );
                        });
                        $('#usuarioSuggestions').show();
                    }
                }
            });
        } else {
            $('#usuarioSuggestions').hide();
        }
    });

    $(document).on('click', '.suggestion-item', function () {
        var nombreUsuario = $(this).text();
        var idUsuario = $(this).data('id');
        $('#usuario').val(nombreUsuario);
        $('#usuarioId').val(idUsuario);
        $('#usuarioSuggestions').hide();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#usuarioSuggestions, #usuario').length) {
            $('#usuarioSuggestions').hide();
        }
    });

    $('form').on('submit', function (event) {
        if ($('#usuario').val().trim() === '' || $('#usuarioId').val() === '') {
            alert('Debe seleccionar un usuario válido.');
            event.preventDefault();
        }
    });

    // ==============================
    // Inicializar Select2
    // ==============================
    function initSelect2(selector, tipo, listaId, inputName) {
        if (!$(selector).length) return;

        $(selector).select2({
            placeholder: 'Buscar bien por código o descripción',
            ajax: {
                url: APP.url('movimientos/buscarBienes'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        tipo: tipo,
                        persona: $('#usuarioId').val()
                    };
                },
                processResults: function (data) {
                    return data;
                },
                templateResult: function (bien) {
                    if (!bien.id) return bien.text;
                    let estado = bien.estado ? `📌 ${bien.estado}` : '';
                    return $(`<span>${bien.text}<br><small>${estado}</small></span>`);
                }
            }
        });

        $(selector).on('select2:select', function (e) {
            let data = e.params.data;
            if ($('#bien_' + listaId + '_' + data.id).length) return;
            
            $('#' + listaId).append(`
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    id="bien_${listaId}_${data.id}">
                    <div>
                        <i class="fas fa-laptop text-primary me-2"></i>
                        <strong>${data.text}</strong>
                    </div>
                    <div>
                        <input type="hidden" name="${inputName}[]" value="${data.id}">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-bien remove-bien"
                            data-id="${data.id}" data-lista="${listaId}">
                            <i class="fas fa-times"></i> Quitar
                        </button>
                    </div>
                </li>
            `);
            $(selector).val(null).trigger('change');
        });
    }

    initSelect2('#buscador_asignar', 'asignacion', 'lista_asignar', 'bienes_asignar');
    initSelect2('#buscador_prestar', 'prestamo', 'lista_prestar', 'bienes_prestar');
    initSelect2('#buscador_retirar', 'retiro', 'lista_retirar', 'bienes_retirar');

    $(document).on('click', '.remove-bien', function () {
        let id = $(this).data('id');
        let lista = $(this).data('lista');
        $('#bien_' + lista + '_' + id).remove();
    });

    $('#tipo_movimiento').change(function () {
        let tipo = $(this).val();
        $('#contenedor_asignar').toggle(tipo === 'asignacion' || tipo === 'cambio');
        $('#contenedor_prestar').toggle(tipo === 'prestamo' || tipo === 'cambio');
        $('#contenedor_retirar').toggle(tipo === 'retiro' || tipo === 'cambio');

        if (tipo === 'prestamo') {
            $('#contenedor_fecha_prestamo').show();
        } else {
            $('#contenedor_fecha_prestamo').hide();
            $('#fecha_limite').val('');
        }
    }).trigger('change');
});
