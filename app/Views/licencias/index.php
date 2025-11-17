<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h3 class="my-3" id="titulo">🎫 Licencias Informáticas</h3>

    <!-- Mensajes de sesión -->
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger"><?= session('error') ?></div>
    <?php endif; ?>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success"><?= session('success') ?></div>
    <?php endif; ?>

    <!-- Botón para nueva licencia -->
    <div class="mb-3">
        <button class="btn btn-primary" id="btnNuevaLicencia">
            <i class="fas fa-plus-circle"></i> Nueva Licencia
        </button>
    </div>

    <!-- Tabla principal -->
    <table id="licenciasTable" class="table table-hover table-bordered" aria-describedby="titulo">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Software</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Versión</th>
                <th>Fabricante</th>
                <th>Cant. Total</th>
                <th>Disponibles</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-center"></tbody>
    </table>
</div>

<!-- Modal para crear/editar licencia -->
<div class="modal fade" id="licenciaModal" tabindex="-1" role="dialog" aria-labelledby="licenciaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="licenciaForm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="licenciaModalLabel">Nueva Licencia</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nombre del Software</label>
                            <input type="text" class="form-control" id="nombre_software" name="nombre_software"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tipo de Licencia</label>
                            <select class="form-control" id="tipo_licencia" name="tipo_licencia" required>
                                <option value="perpetua">Perpetua</option>
                                <option value="suscripción">Suscripción</option>
                                <option value="OEM">OEM</option>
                                <option value="trial">Trial</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Categoría</label>
                            <select class="form-control" id="categoria" name="categoria" required>
                                <option value="ofimática">Ofimática</option>
                                <option value="antivirus">Antivirus</option>
                                <option value="diseño">Diseño</option>
                                <option value="sistema operativo">Sistema Operativo</option>
                                <option value="otros">Otros</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Versión</label>
                            <input type="text" class="form-control" id="version" name="version">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Fabricante</label>
                            <input type="text" class="form-control" id="fabricante" name="fabricante">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Proveedor</label>
                            <input type="text" class="form-control" id="proveedor" name="proveedor">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Fecha Adquisición</label>
                            <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Fecha Expiración</label>
                            <input type="date" class="form-control" id="fecha_expiracion" name="fecha_expiracion">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Cantidad Total</label>
                            <input type="number" class="form-control" id="cantidad_total" name="cantidad_total"
                                required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Cantidad Disponible</label>
                            <input type="number" class="form-control" id="cantidad_disponible"
                                name="cantidad_disponible" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Estado</label>
                            <select class="form-control" id="estado" name="estado">
                                <option value="activa">Activa</option>
                                <option value="vencida">Vencida</option>
                                <option value="en uso">En uso</option>
                                <option value="agotada">Agotada</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        let tabla = $('#licenciasTable').DataTable({
            ajax: {
                url: "<?= base_url('licencias'); ?>",
                dataSrc: 'data',
                dataSrc: function (json) {
                    return json.licencias ?? json;
                }
            },
            columns: [
                { data: 'id' },
                { data: 'nombre_software' },
                { data: 'tipo_licencia' },
                { data: 'categoria' },
                { data: 'version' },
                { data: 'fabricante' },
                { data: 'cantidad_total' },
                { data: 'cantidad_disponible' },
                { data: 'estado' },
                {
                    data: null,
                    render: function (data) {
                        return `
                        <button class="btn btn-sm btn-info btnEditar" data-id="${data.id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btnEliminar" data-id="${data.id}"><i class="fas fa-trash"></i></button>
                    `;
                    }
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            responsive: true
        });

        // Nuevo registro
        $('#btnNuevaLicencia').click(function () {
            $('#licenciaForm')[0].reset();
            $('#id').val('');
            $('#licenciaModalLabel').text('Nueva Licencia');
            $('#licenciaModal').modal('show');
        });

        // Guardar o editar
        $('#licenciaForm').submit(function (e) {
            e.preventDefault();
            const id = $('#id').val();
            const method = id ? 'PUT' : 'POST';
            const url = id ? `<?= base_url('licencias'); ?>/${id}` : `<?= base_url('licencias'); ?>`;

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function () {
                    $('#licenciaModal').modal('hide');
                    tabla.ajax.reload(null, false);
                }
            });
        });

        // Editar licencia
        $('#licenciasTable').on('click', '.btnEditar', function () {
            const id = $(this).data('id');
            $.get(`<?= base_url('licencias'); ?>/${id}`, function (data) {
                for (const key in data) {
                    $(`#${key}`).val(data[key]);
                }
                $('#licenciaModalLabel').text('Editar Licencia');
                $('#licenciaModal').modal('show');
            });
        });

        // Eliminar licencia
        $('#licenciasTable').on('click', '.btnEliminar', function () {
            const id = $(this).data('id');
            if (confirm('¿Seguro que deseas eliminar esta licencia?')) {
                $.ajax({
                    url: `<?= base_url('licencias'); ?>/${id}`,
                    type: 'DELETE',
                    success: function () {
                        tabla.ajax.reload(null, false);
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection(); ?>