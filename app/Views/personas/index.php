<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container-fluid mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-users mr-2"></i>Gestión de Personas</h4>
            <a href="<?= base_url('personas/new') ?>" class="btn btn-light btn-sm">
                <i class="fas fa-plus-circle"></i> Nueva Persona
            </a>
            <div class="input-group w-50">
                <input type="text" id="filtroGlobal" class="form-control"
                    placeholder="Filtrar por nombre, DNI, régimen o sede...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="personalTable" class="table table-hover table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>Nombre completo</th>
                            <th>Régimen Laboral</th>
                            <th>Inicio Laboral</th>
                            <th>Fin Laboral</th>
                            <th>DNI</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Modalidad</th>
                            <th>Sede</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($personas as $p): ?>
                            <tr>
                                <td class="text-center"><?= esc($p['id']) ?></td>
                                <td><?= esc($p['nombre_completo']) ?></td>
                                <td><?= esc($p['nombre_regimen']) ?></td>
                                <td class="text-center"><?= esc($p['fecha_inicio']) ?></td>
                                <td class="text-center"><?= esc($p['fecha_fin']) ?></td>
                                <td class="text-center font-weight-bold"><?= esc($p['dni']) ?></td>
                                <td><?= esc($p['correo']) ?></td>
                                <td><?= esc($p['telefono']) ?></td>
                                <td><?= esc($p['direccion_domiciliaria']) ?></td>
                                <td class="text-capitalize text-center">
                                    <span class="badge badge-info px-2 py-1"><?= esc($p['modalidad']) ?></span>
                                </td>
                                <td><?= esc($p['nombre_local']) ?></td>
                                <td class="col-estado">
                                    <?php if (($p['estado'] ?? 'activo') === 'inactivo'): ?>
                                        <span class="badge badge-secondary">INACTIVO</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">ACTIVO</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center col-opciones">
                                    <a href="<?= base_url('personas/' . $p['id']) ?>" class="btn btn-secondary btn-sm mb-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('personas/' . $p['id'] . '/edit') ?>"
                                        class="btn btn-primary btn-sm mb-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                
                                
                                    <!-- botones -->
                                    <?php if (($p['estado'] ?? 'activo') === 'activo'): ?>
                                        <button class="btn btn-sm btn-warning btnDesactivar"
                                            data-id="<?= esc($p['id']) ?>">Desactivar</button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary btnRecuperar"
                                            data-id="<?= esc($p['id']) ?>">Recuperar</button>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Desactivar -->
<div class="modal fade" id="modalDesactivar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Motivo de cese</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formDesactivar">
                    <input type="hidden" id="desactivarId" name="id" value="">
                    <div class="form-group">
                        <label for="motivoCese">Motivo</label>
                        <textarea id="motivoCese" name="motivo_cese" class="form-control" rows="4" required
                            style="resize:vertical;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmarDesactivar" class="btn btn-danger">Confirmar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>


<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // Inicializar UNA vez con el ID real de la tabla
        var table = $('#personalTable').DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
            order: [[0, "asc"]],
            responsive: true,
            fixedHeader: true,
            pageLength: 10
        });

        // Filtro global (mantener)
        $('#filtroGlobal').on('keyup', function () {
            var valor = $(this).val().toLowerCase();
            $.fn.dataTable.ext.search = [];
            $.fn.dataTable.ext.search.push(function (settings, data) {
                var combinado = (data[1] + ' ' + data[2] + ' ' + data[5] + ' ' + data[10]).toLowerCase();
                return combinado.includes(valor);
            });
            table.draw();
        });

        // Abrir modal y setear id
        $(document).on('click', '.btnDesactivar', function () {
            const id = $(this).data('id');
            $('#desactivarId').val(id);
            $('#motivoCese').val('');
            $('#modalDesactivar').modal('show');
        });

        // Confirmar desactivación (AJAX) — actualizar fila: Estado (índice 11) y Opciones (índice 12)
        $('#confirmarDesactivar').on('click', function () {
            const id = $('#desactivarId').val();
            const motivo = $('#motivoCese').val().trim();
            if (!motivo) {
                alert('Ingrese un motivo de cese.');
                return;
            }

            const url = "<?= base_url('personas/desactivar') ?>/" + id;
            $.post(url, { motivo_cese: motivo }, function (resp) {
                if (resp && resp.status === 'success') {
                    const $row = $('tr[data-id="' + id + '"]');
                    if ($row.length) {
                        $row.find('td').eq(11).html('<span class="badge badge-secondary">INACTIVO</span>');
                        $row.find('td').eq(12).html('<button class="btn btn-sm btn-primary btnRecuperar" data-id="' + id + '">Recuperar</button>');
                        table.row($row).invalidate().draw(false);
                    }
                    $('#modalDesactivar').modal('hide');
                    alert(resp.message || 'Usuario desactivado.');
                } else {
                    alert(resp.message || 'Error al desactivar.');
                }
            }, 'json').fail(function () {
                alert('Error del servidor.');
            });
        });

        // Recuperar usuario (AJAX) — actualizar fila a ACTIVO
        $(document).on('click', '.btnRecuperar', function () {
            const $btn = $(this);
            const id = $btn.data('id');
            if (!confirm('¿Confirmas recuperar este usuario?')) return;

            const url = "<?= base_url('personas/recuperar') ?>/" + id;
            $.post(url, {}, function (resp) {
                if (resp && resp.status === 'success') {
                    const $row = $('tr[data-id="' + id + '"]');
                    if ($row.length) {
                        $row.find('td').eq(11).html('<span class="badge badge-success">ACTIVO</span>');
                        $row.find('td').eq(12).html('<button class="btn btn-sm btn-warning btnDesactivar" data-id="' + id + '">Desactivar</button>');
                        table.row($row).invalidate().draw(false);
                    }
                    alert(resp.message || 'Usuario recuperado.');
                } else {
                    alert(resp.message || 'Error al recuperar.');
                }
            }, 'json').fail(function () {
                alert('Error del servidor.');
            });
        });
    });
</script>
<?= $this->endSection(); ?>