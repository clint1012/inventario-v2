<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="d-flex justify-content-between align-items-center my-3 flex-wrap gap-2">
    <h3 id="titulo" class="mb-0">Mantenimiento</h3>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEnviarMantenimiento">
        <i class="fa fa-plus me-1"></i> Agregar equipo a mantenimiento
    </button>
</div>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<table id="mantenimientoTable" class="table table-hover table-bordered my-3 mb-4 mt-5" aria-describedby="titulo">
    <thead class="table-dark">
        <tr>
            <th>Código Patrimonial</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Local</th>
            <th>Departamento</th>
            <th>Motivo</th>
            <th>Usuario</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bienes as $bien): ?>
            <tr>
                <td><?= esc($bien['cod_patrimonial']) ?></td>
                <td><?= esc($bien['descripcion']) ?></td>
                <td><?= esc($bien['marca']) ?></td>
                <td><?= esc($bien['modelo']) ?></td>
                <td><?= esc($bien['nombre_local']) ?></td>
                <td><?= esc($bien['nombre_departamento']) ?></td>
                <td><?= esc($bien['motivo_mantenimiento']) ?></td>
                <td><?= esc($bien['usuario_mantenimiento']) ?></td>
                <td><?= esc($bien['tipo_mantenimiento']) ?></td>
                <td><?= esc($bien['estado']) ?></td>
                <td>
                    <form action="<?= base_url('mantenimiento/recuperar/' . $bien['id']) ?>" method="POST"
                        style="display:inline;">
                        <button type="submit" class="btn btn-success btn-sm">Recuperar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="modal fade" id="modalEnviarMantenimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar equipo a mantenimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAgregarMantenimiento" action="<?= base_url('bienes/getMantenimiento') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="codPatrimonial" class="form-label">Código patrimonial</label>
                        <input type="text" class="form-control" id="codPatrimonial" name="cod_patrimonial" required>
                        <input type="hidden" name="bien_id" id="bienIdHidden">
                    </div>
                    <div class="mb-3">
                        <label for="descripcionBien" class="form-label">Descripción / Marca</label>
                        <input type="text" class="form-control" id="descripcionBien" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="motivoMantenimiento" class="form-label">Motivo</label>
                        <textarea class="form-control" id="motivoMantenimiento" name="motivo_mantenimiento" rows="3"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="usuarioMantenimiento" class="form-label">Usuario solicitante</label>
                        <input type="text" class="form-control" id="usuarioMantenimiento" name="usuario_mantenimiento"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="tipoMantenimiento" class="form-label">Tipo de mantenimiento</label>
                        <select class="form-control" id="tipoMantenimiento" name="tipo_mantenimiento" required>
                            <option value="">Seleccione una opción</option>
                            <option value="preventivo">Preventivo</option>
                            <option value="correctivo">Correctivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar a mantenimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        $('#mantenimientoTable').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            order: [[0, "asc"]],
            responsive: true,
            fixedHeader: true
        });

        const modalEl = document.getElementById('modalEnviarMantenimiento');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                const form = document.getElementById('formAgregarMantenimiento');
                if (form) {
                    form.reset();
                }
            });
        }
    });

    const detalleBienUrl = "<?= base_url('bienes/detalle-por-codigo'); ?>";

    $('#codPatrimonial').on('blur change', function () {
        const codigo = $(this).val().trim();
        $('#descripcionBien').val('');
        $('#bienIdHidden').val('');

        if (!codigo) return;

        $.getJSON(detalleBienUrl, { codigo })
            .done(({ id, descripcion, marca }) => {
                $('#descripcionBien').val(`${descripcion} - ${marca}`);
                $('#bienIdHidden').val(id);
            })
            .fail(() => {
                $('#descripcionBien').val('No encontrado');
            });
    });
</script>
<?= $this->endSection(); ?>