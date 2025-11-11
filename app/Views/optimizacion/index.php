<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h2>Optimización - Cambio de componentes</h2>
    <!-- Botón que abre el modal -->
    <button class="btn btn-secondary mb-3" data-toggle="modal" data-target="#modalRegistro">
        Registrar
    </button>

    <table id="optimizacion" class="table table-striped table-bordered" style="width: 100%">
        <thead class="table-dark">
            <tr>
                <th>Cod. patrimonial</th>
                <th>Descripción</th>
                <th>Optimización</th>
                <th>Motivo</th>
                <th>Fecha de modificación</th>
                <th>Local</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($optimizaciones)): ?>
                <?php foreach ($optimizaciones as $opt): ?>
                    <tr>
                        <td><?= esc($opt['cod_patrimonial']); ?></td>
                        <td><?= esc($opt['descripcion']); ?></td>
                        <td><?= esc($opt['optimizacion']); ?></td>
                        <td><?= esc($opt['motivo']); ?></td>
                        <td><?= esc($opt['fecha_modificacion']); ?></td>
                        <td><?= esc($opt['local']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning btnEditar" data-id="<?= $opt['id'] ?>">Editar</button>
                            <button class="btn btn-sm btn-danger eliminar-btn" data-id="<?= $opt['id']; ?>">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ==========================
     MODAL REGISTRO
========================== -->
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?= base_url('optimizacion') ?>" method="post">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="modalRegistroLabel">Registrar Optimización</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="bien_id">Código patrimonial</label>
                            <input type="text" class="form-control" name="cod_patrimonial" id="cod_patrimonial"
                                required>
                            <input type="hidden" name="bien_id" id="bien_id">
                            <input type="hidden" name="id_locales" id="id_locales">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="descripcion">Descripción</label>
                            <input type="text" class="form-control" name="descripcion" id="descripcion" readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="optimizacion">Tipo de optimización</label>
                            <select name="optimizacion" id="optimizacion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Cambio de memoria RAM">Cambio de memoria RAM</option>
                                <option value="Cambio de disco duro">Cambio de disco duro</option>
                                <option value="Actualización de procesador">Actualización de procesador</option>
                                <option value="Reemplazo de fuente">Reemplazo de fuente</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Tipo de mantenimiento:</label><br>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="mantenimiento_interno"
                                name="tipo_mantenimiento" value="interno">
                            <label class="form-check-label" for="mantenimiento_interno">Mantenimiento interno</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="mantenimiento_externo"
                                name="tipo_mantenimiento" value="externo">
                            <label class="form-check-label" for="mantenimiento_externo">Mantenimiento externo</label>
                        </div>
                    </div>

                    <!-- 🔹 Campos que aparecen dinámicamente -->
                    <div id="camposInterno" class="mt-2" style="display: none;">
                        <div class="form-group">
                            <label for="tecnico_interno">Técnico responsable</label>
                            <input type="text" class="form-control" name="tecnico_interno" id="tecnico_interno"
                                placeholder="Nombre del técnico">
                        </div>
                    </div>

                    <div id="camposExterno" class="mt-2" style="display: none;">
                        <div class="form-group">
                            <label for="empresa_externa">Nombre de la empresa</label>
                            <input type="text" class="form-control" name="empresa_externa" id="empresa_externa"
                                placeholder="Nombre de la empresa">
                        </div>
                        <div class="form-group mt-2">
                            <label for="tecnico_externo">Técnico a cargo</label>
                            <input type="text" class="form-control" name="tecnico_externo" id="tecnico_externo"
                                placeholder="Nombre del técnico a cargo">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="motivo">Motivo</label>
                        <textarea name="motivo" id="motivo" rows="3" class="form-control"
                            placeholder="Describa brevemente el motivo..." required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="local">Local</label>
                            <input type="text" class="form-control" name="local" id="local" readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" name="fecha" id="fecha_modificacion" required>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- ==========================
     MODAL EDITAR   
========================== -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formEditar">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Optimización</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id">

                    <div class="form-group">
                        <label for="edit_optimizacion">Optimización</label>
                        <input type="text" id="edit_optimizacion" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="edit_motivo">Motivo</label>
                        <input type="text" id="edit_motivo" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="edit_local">Local</label>
                        <input type="text" id="edit_local" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>


<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // ==========================
        // Inicialización DataTable
        // ==========================
        $('#optimizacion').DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
            order: [[0, "asc"]],
            responsive: true,
            fixedHeader: true
        });

        // ==========================
        //  Autocompletar descripción y local
        // ==========================
        $('#cod_patrimonial').on('keyup', function () {
            const cod = $(this).val().trim();

            if (cod.length >= 3) { // buscar desde 3 caracteres
                $.ajax({
                    url: '<?= base_url('optimizacion/buscarBien') ?>/' + cod,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.error) {
                            $('#descripcion').val('');
                            $('#local').val('');
                            $('#id_locales').val('');
                            $('#bien_id').val('');
                        } else {
                            $('#descripcion').val(data.descripcion);
                            $('#local').val(data.local);
                            $('#id_locales').val(data.id_locales);
                            $('#bien_id').val(data.id);
                        }
                    },
                    error: function () {
                        $('#descripcion').val('');
                        $('#local').val('');
                        $('#id_locales').val('');
                        $('#bien_id').val('');
                    }
                });
            } else {
                $('#descripcion').val('');
                $('#local').val('');
                $('#id_locales').val('');
                $('#bien_id').val('');
            }
        });

        // ========================
        //  Fecha automática al abrir el modal
        // ==========================
        $('#modalRegistro').on('show.bs.modal', function () {
            const hoy = new Date();
            const año = hoy.getFullYear();
            const mes = String(hoy.getMonth() + 1).padStart(2, '0');
            const dia = String(hoy.getDate()).padStart(2, '0');
            const fechaActual = `${año}-${mes}-${dia}`;
            $('#fecha_modificacion').val(fechaActual);
        });

        // ==========================
        //  Mostrar campos según tipo de mantenimiento
        // ==========================
        $('#mantenimiento_interno').on('change', function () {
            if (this.checked) {
                $('#camposInterno').show();
                $('#camposExterno').hide();
                $('#mantenimiento_externo').prop('checked', false);
            } else {
                $('#camposInterno').hide();
            }
        });

        $('#mantenimiento_externo').on('change', function () {
            if (this.checked) {
                $('#camposExterno').show();
                $('#camposInterno').hide();
                $('#mantenimiento_interno').prop('checked', false);
            } else {
                $('#camposExterno').hide();
            }
        });

        // -------------ELIMINAR------------
        $('.eliminar-btn').on('click', function () {
            const id = $(this).data('id');
            if (confirm('¿Seguro que deseas eliminar este registro?')) {
                $.ajax({
                    url: `<?= base_url('optimizacion') ?>/${id}`,
                    type: 'DELETE',
                    success: function (response) {
                        alert('Registro eliminado correctamente');
                        location.reload(); // recarga la tabla
                    },
                    error: function (xhr) {
                        alert('❌ Error al eliminar: ' + xhr.responseText);
                    }
                });
            }
        });


        // ------------------ BOTÓN EDITAR --------------------------
        $('.btnEditar').click(function () {
            const id = $(this).data('id');

            $.get('<?= base_url('optimizacion') ?>/' + id, function (data) {
                if (data.status === 'ok') {
                    $('#edit_id').val(data.optimizacion.id);
                    $('#edit_optimizacion').val(data.optimizacion.optimizacion);
                    $('#edit_motivo').val(data.optimizacion.motivo);
                    // Aquí ponemos el nombre del local
                    $('#edit_local').val(data.optimizacion.local);
                    $('#modalEditar').modal('show');
                } else {
                    alert(data.message || 'No se pudo cargar la información.');
                }
            }).fail(function () {
                alert('No se pudo cargar la información.');
            });
        });

        // ------------------ ENVÍA ACTUALIZACIÓN --------------------------
        $('#formEditar').submit(function (e) {
            e.preventDefault();
            const id = $('#edit_id').val();

            $.ajax({
                url: '<?= base_url('optimizacion') ?>/' + id,
                type: 'POST',
                data: {
                    optimizacion: $('#edit_optimizacion').val(),
                    motivo: $('#edit_motivo').val()
                },
                success: function (response) {
                    alert(response.message || 'Registro actualizado correctamente.');
                    $('#modalEditar').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('Error al actualizar los datos.');
                }
            });
        });




    });
</script>

<?= $this->endSection(); ?>