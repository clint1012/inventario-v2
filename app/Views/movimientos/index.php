<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h2>📑 Actas de Instalación por Usuario</h2>
    <a href="<?= base_url('movimientos/new') ?>" class="btn btn-primary mb-3">+ Nuevo Movimiento</a>

    <table id="tablaMovimientos" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Departamento</th>
                <th>Local</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $mov): ?>
                <tr data-lote="<?= $mov['lote'] ?>">
                    <td><?= $mov['nombre'] . ' ' . $mov['ape_paterno'] . ' ' . $mov['ape_materno'] ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($mov['fecha_movimiento'])) ?></td>
                    <td><?= $mov['departamento'] ?></td>
                    <td><?= $mov['local'] ?></td>
                    <td><?= ucfirst($mov['tipo_movimiento']) ?></td>
                    <td>
                        <a href="<?= base_url('movimientos/descargarCargoLote/' . $mov['lote']) ?>"
                            class="btn btn-sm btn-primary">PDF</a>
                        <button class="btn btn-sm btn-danger btnAnular" data-id="<?= $mov['id'] ?>"
                            data-lote="<?= $mov['lote'] ?>">
                            Anular
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para confirmar anulación -->
<div class="modal fade" id="modalAnular" tabindex="-1" role="dialog" aria-labelledby="modalAnularLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalAnularLabel">Confirmar anulación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas anular este movimiento? Los bienes quedarán liberados.</p>
                <div class="form-group">
                    <label>Motivo de anulación:</label>
                    <textarea id="motivoAnulacion" class="form-control" rows="3"
                        placeholder="Ej: Error de asignación"></textarea>
                </div>
                <input type="hidden" id="idMovimiento">
                <input type="hidden" id="loteMovimiento">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarAnulacion">Anular</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {

        // Inicializar DataTable
        const table = $('table').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            }
        });

        // Abrir modal de confirmación
        $(document).on('click', '.btnAnular', function () {
            const id = $(this).data('id');
            const lote = $(this).data('lote');
            $('#idMovimiento').val(id);
            $('#loteMovimiento').val(lote);
            $('#motivoAnulacion').val('');
            $('#modalAnular').modal('show');
        });

        // Confirmar anulación
        $('#confirmarAnulacion').on('click', function () {
            const loteMovimiento = $('#loteMovimiento').val();
            const motivo = $('#motivoAnulacion').val().trim();

            if (motivo === '') {
                Swal.fire('Atención', 'Por favor ingrese un motivo de anulación.', 'warning');
                return;
            }

            $.ajax({
                url: "<?= base_url('movimientos/anular/') ?>" + loteMovimiento,
                type: 'POST',
                data: { motivo_anulacion: motivo },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // eliminar la fila del DataTable
                        const row = $('tr[data-lote="' + loteMovimiento + '"]');
                        table.row(row).remove().draw(false);

                        $('#modalAnular').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Anulado',
                            text: 'El lote fue anulado correctamente.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Ocurrió un error.', 'error');
                    }
                },
                error: function (xhr) {
                    console.error('❌ Backend error:', xhr.responseText);
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>


<?= $this->endSection(); ?>