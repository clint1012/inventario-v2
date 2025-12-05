<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="card shadow-sm my-4">
    <div class="card-body">
        <h4 class="mb-3">Inventario anual</h4>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session('success') ?></div>
        <?php endif; ?>

        <form id="formInventario" action="<?= base_url('inventario/registrar') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="anioInventario">Año</label>
                    <input type="number" class="form-control" id="anioInventario" name="anio"
                        value="<?= esc($anioActual) ?>" min="2000" max="2100" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="mesInventario">Mes</label>
                    <select class="form-control" id="mesInventario" name="mes" required>
                        <option value="">Seleccione...</option>
                        <option value="Enero">Enero</option>
                        <option value="Febrero">Febrero</option>
                        <option value="Marzo">Marzo</option>
                        <option value="Abril">Abril</option>
                        <option value="Mayo">Mayo</option>
                        <option value="Junio">Junio</option>
                        <option value="Julio">Julio</option>
                        <option value="Agosto">Agosto</option>
                        <option value="Septiembre">Septiembre</option>
                        <option value="Octubre">Octubre</option>
                        <option value="Noviembre">Noviembre</option>
                        <option value="Diciembre">Diciembre</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="usuarioBuscador">Usuario</label>
                    <input type="text" class="form-control" id="usuarioBuscador" placeholder="Digita 3 letras..."
                        autocomplete="off" required>
                    <input type="hidden" name="usuario_id" id="usuarioIdHidden">
                </div>
                <div class="form-group col-md-4">
                    <label for="regimenUsuario">Régimen Laboral</label>
                    <input type="text" class="form-control" id="regimenUsuario" readonly
                        placeholder="Selecciona un usuario">
                </div>
            </div>

            <div class="form-row" id="jefeContainer" style="display: none;">
                <div class="form-group col-md-12">
                    <label for="jefeBuscador">Jefe Responsable <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="jefeBuscador"
                        placeholder="Digita 3 letras para buscar..." autocomplete="off">
                    <input type="hidden" name="jefe_id" id="jefeIdHidden">
                </div>
            </div>

            <div id="equiposAsignados" class="mt-4"></div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary" disabled id="btnGuardarInventario">
                    Guardar inventario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal asignar bien -->
<div class="modal fade" id="modalAsignarBien" tabindex="-1" aria-labelledby="modalAsignarBienLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formAsignarBien" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsignarBienLabel">Asignar bien</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <input type="hidden" name="usuario_id" id="asignarUsuarioId">
                <input type="hidden" name="bien_id" id="asignarBienId">
                <div class="form-group">
                    <label for="sbnBienAsignar">Código SBN</label>
                    <input type="text" class="form-control" id="sbnBienAsignar" name="sbn" required>
                </div>
                <div class="form-group">
                    <label for="descripcionBienAsignar">Descripción</label>
                    <input type="text" class="form-control" id="descripcionBienAsignar" name="descripcion" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar asignación</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal ubicación -->
<div class="modal fade" id="modalUbicacionBien" tabindex="-1" aria-hidden="true" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formUbicacionBien" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar ubicación del bien</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <input type="hidden" name="bien_id" id="ubicacionBienId">
                <div class="form-group">
                    <label for="localBienSelect">Local</label>
                    <select class="form-control" id="localBienSelect" name="id_locales" required></select>
                </div>
                <div class="form-group">
                    <label for="departamentoBienSelect">Departamento</label>
                    <select class="form-control" id="departamentoBienSelect" name="id_departamento" required></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
    const actualizarUbicacionUrl = "<?= base_url('bienes/actualizar-ubicacion'); ?>";
    const localesUrl = "<?= base_url('bienes/get-locales'); ?>";
    const departamentosUrl = "<?= base_url('bienes/get-departamentos'); ?>";
    const buscarUsuariosUrl = "<?= base_url('inventario/buscar-usuarios'); ?>";
    const buscarJefesUrl = "<?= base_url('inventario/buscar-jefes'); ?>";
    const equiposUsuarioUrl = "<?= base_url('inventario/equipos'); ?>";
    const liberarBienUrl = "<?= base_url('inventario/liberar-bien'); ?>";
    const asignarBienUrl = "<?= base_url('inventario/asignar-bien'); ?>";
    const buscarBienPorSbnUrl = "<?= base_url('bienes/buscar-por-sbn'); ?>";
    const autocompletarDescripcionUrl = "<?= base_url('bienes/autocompletar-descripcion'); ?>";
    const csrfTokenName = "<?= csrf_token() ?>";

    let requiereJefe = false;

    const regimenesConJefe = [
        'practicante pre-profesional',
        'practicante profesional',
        'locador de servicios'
    ];

    $('#descripcionBienAsignar').autocomplete({
        minLength: 2,
        source: function (request, response) {
            $.getJSON(autocompletarDescripcionUrl, { term: request.term }, response);
        }
    });

    $('#sbnBienAsignar').on('blur', function () {
        const sbn = this.value.trim();
        if (sbn.length < 3) return;
        $.getJSON(`${buscarBienPorSbnUrl}?sbn=${encodeURIComponent(sbn)}`)
            .done(data => {
                if (data?.descripcion) {
                    $('#descripcionBienAsignar').val(data.descripcion);
                }
            });
    });

    $('#formAsignarBien').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post(asignarBienUrl, formData)
            .done(() => {
                $('#modalAsignarBien').modal('hide');
                cargarEquipos($('#usuarioIdHidden').val());
            })
            .fail(() => alert('No se pudo asignar el bien.'));
    });

    $('#usuarioBuscador').autocomplete({
        minLength: 3,
        source: buscarUsuariosUrl,
        autoFocus: true,
        select: function (event, ui) {
            $('#usuarioBuscador').val(ui.item.label);
            $('#usuarioIdHidden').val(ui.item.id);

            const regimen = ui.item.regimen || '';
            $('#regimenUsuario').val(regimen || 'Sin régimen');

            // Verificar si requiere jefe
            requiereJefe = regimenesConJefe.some(r => regimen.toLowerCase().includes(r));

            if (requiereJefe) {
                $('#jefeContainer').show();
                $('#jefeBuscador').attr('required', true);
            } else {
                $('#jefeContainer').hide();
                $('#jefeBuscador').attr('required', false).val('');
                $('#jefeIdHidden').val('');
            }

            cargarEquipos(ui.item.id);
            return false;
        }
    }).on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const menu = $(this).autocomplete('widget');
            if (menu.is(':visible')) {
                const firstItem = menu.find('.ui-menu-item:first');
                if (firstItem.length) {
                    $(this).autocomplete('instance')._trigger('select', 'autocompleteselect', {
                        item: firstItem.data('ui-autocomplete-item')
                    });
                }
            }
        }
    });

    $('#jefeBuscador').autocomplete({
        minLength: 3,
        source: buscarJefesUrl,
        autoFocus: true,
        select: function (event, ui) {
            $('#jefeBuscador').val(ui.item.label);
            $('#jefeIdHidden').val(ui.item.id);
            return false;
        }
    }).on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const menu = $(this).autocomplete('widget');
            if (menu.is(':visible')) {
                const firstItem = menu.find('.ui-menu-item:first');
                if (firstItem.length) {
                    $(this).autocomplete('instance')._trigger('select', 'autocompleteselect', {
                        item: firstItem.data('ui-autocomplete-item')
                    });
                }
            }
        }
    });

    $('#formInventario').on('submit', function (e) {
        if (requiereJefe && !$('#jefeIdHidden').val()) {
            e.preventDefault();
            alert('Debe seleccionar un jefe responsable.');
            return false;
        }
    });

    $('#formInventario').on('keydown', function (e) {
        if (e.key === 'Enter' && (e.target.id === 'usuarioBuscador' || e.target.id === 'buscarSBN' || e.target.id === 'jefeBuscador')) {
            e.preventDefault();
            return false;
        }
    });

    function cargarEquipos(usuarioId) {
        $('#equiposAsignados').html('<div class="text-muted">Cargando equipos...</div>');
        $('#btnGuardarInventario').prop('disabled', true);

        $.getJSON(`${equiposUsuarioUrl}/${usuarioId}`)
            .done(renderEquipos)
            .fail(() => {
                $('#equiposAsignados').html('<div class="alert alert-danger">No se pudieron cargar los equipos.</div>');
            });
    }

    function renderEquipos(equipos) {
        const usuarioSeleccionado = $('#usuarioBuscador').val() || 'Usuario';
        const header = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-2">Bienes asignados a ${usuarioSeleccionado}</h5>
                    <input type="text" class="form-control form-control-sm" id="buscarSBN" 
                        placeholder="Buscar por código SBN..." style="max-width: 300px;">
                </div>
                <button type="button" class="btn btn-success btn-sm" onclick="abrirModalAsignar()">
                    Asignar bien
                </button>
            </div>
        `;

        if (!equipos.length) {
            $('#equiposAsignados').html(header + '<div class="alert alert-warning">El usuario no tiene equipos asignados.</div>');
            $('#btnGuardarInventario').prop('disabled', false);
            return;
        }

        const items = equipos.map(eq => `
             <label class="list-group-item equipo-item" data-sbn="${(eq.cod_patrimonial ?? '').toLowerCase()}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong>${eq.descripcion ?? 'Equipo'}</strong>
                        <small class="d-block text-muted">${eq.cod_patrimonial ?? 'Sin SBN'} · ${eq.marca ?? ''}</small>
                    </div>
                    <div class="col-md-1">
                        <small class="text-muted d-block mb-1">¿Verificado?</small>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="check_si_${eq.id}" 
                                name="equipos[${eq.id}][verificado]" value="1">
                            <label class="custom-control-label" for="check_si_${eq.id}">Sí</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="check_no_${eq.id}" 
                                name="equipos[${eq.id}][verificado]" value="0" checked>
                            <label class="custom-control-label" for="check_no_${eq.id}">No</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted d-block">Local</small>
                        <span>${eq.local ?? '—'}</span>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted d-block">Departamento</small>
                        <span>${eq.departamento ?? '—'}</span>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm"
                            name="equipos[${eq.id}][comentario]" placeholder="Observación">
                    </div>
                    <div class="col-md-2">
                        <div class="btn-group btn-group-sm btn-block" role="group">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="liberarBien(${eq.id})">
                                Liberar
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="abrirModalAsignar(${eq.id})">
                                Asignar
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="abrirModalUbicacion(${eq.id})">
                                Ubicación
                            </button>
                        </div>
                    </div>
                </div>
            </label>
        `).join('');

        $('#equiposAsignados').html(header + `<div class="list-group" id="listaEquipos">${items}</div>`);
        $('#btnGuardarInventario').prop('disabled', false);
    }

    // Filtro de búsqueda por SBN con delegación de eventos
    $(document).on('keyup', '#buscarSBN', function () {
        const searchTerm = $(this).val().toLowerCase();
        $('.equipo-item').each(function () {
            const sbn = String($(this).data('sbn') || '').toLowerCase();
            if (sbn.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Prevenir submit del formulario al presionar Enter en el buscador
    $(document).on('keydown', '#buscarSBN', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });

    function liberarBien(bienId) {
        const usuarioId = $('#usuarioIdHidden').val();
        if (!usuarioId || !confirm('¿Desea liberar este bien del usuario actual?')) return;

        $.post(liberarBienUrl, {
            [csrfTokenName]: $('input[name="<?= csrf_token() ?>"]').val(),
            bien_id: bienId,
            usuario_id: usuarioId
        })
            .done(() => cargarEquipos(usuarioId))
            .fail(() => alert('No se pudo liberar el bien.'));
    }

    function abrirModalAsignar(bienId = null) {
        const usuarioId = $('#usuarioIdHidden').val();
        if (!usuarioId) {
            alert('Selecciona un usuario antes de asignar bienes.');
            return;
        }
        $('#formAsignarBien')[0].reset();
        $('#asignarUsuarioId').val(usuarioId);
        $('#asignarBienId').val(bienId ?? '');
        $('#modalAsignarBien').modal('show');
    }
    let localesCache = [];
    let departamentosCache = [];

    function cargarCatalogosUbicacion() {
        const localesPromise = localesCache.length ? $.Deferred().resolve(localesCache) : $.getJSON(localesUrl).done(data => localesCache = data);
        const departamentosPromise = departamentosCache.length ? $.Deferred().resolve(departamentosCache) : $.getJSON(departamentosUrl).done(data => departamentosCache = data);
        return $.when(localesPromise, departamentosPromise).then((locData, depData) => {
            const locs = localesCache.length ? localesCache : locData;
            const deps = departamentosCache.length ? departamentosCache : depData;
            renderOptions('#localBienSelect', locs, 'id', 'nombre');
            renderOptions('#departamentoBienSelect', deps, 'id', 'nombre');
        });
    }

    function renderOptions(selector, data, valueKey, labelKey) {
        const options = data.map(item => `<option value="${item[valueKey]}">${item[labelKey]}</option>`).join('');
        $(selector).html(`<option value="">Seleccione...</option>${options}`);
    }

    function abrirModalUbicacion(bienId) {
        if (!bienId) return;
        $('#ubicacionBienId').val(bienId);
        cargarCatalogosUbicacion().then(() => {
            $('#modalUbicacionBien').modal('show');
        });
    }

    $('#formUbicacionBien').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post(actualizarUbicacionUrl, formData)
            .done(() => {
                $('#modalUbicacionBien').modal('hide');
                cargarEquipos($('#usuarioIdHidden').val());
            })
            .fail(() => alert('No se pudo actualizar la ubicación.'));
    });
</script>
<?= $this->endSection(); ?>