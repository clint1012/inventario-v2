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

                    <!-- USUARIO -->
                    <td>
                        <?php
                        if ($mov['tipo_movimiento'] === 'retiro') {
                            // Mostrar el usuario ANTERIOR (a quien se retiró)
                            echo strtoupper(
                                trim(
                                    $mov['nombre_anterior'] . ' ' .
                                    $mov['apep_anterior'] . ' ' .
                                    $mov['apem_anterior']
                                )
                            );
                        } else {
                            // Mostrar el usuario DESTINO (a quien se asignó)
                            echo strtoupper(
                                trim(
                                    $mov['nombre_destino'] . ' ' .
                                    $mov['apep_destino'] . ' ' .
                                    $mov['apem_destino']
                                )
                            );
                        }
                        ?>
                    </td>

                    <!-- FECHA -->
                    <td><?= date('d-m-Y H:i', strtotime($mov['fecha_movimiento'])) ?></td>

                    <!-- DEPARTAMENTO -->
                    <td>
                        <?= $mov['departamento_anterior'] ?>
                    </td>

                    <!-- LOCAL -->
                    <td>
                        <?= $mov['local_anterior'] ?>
                    </td>

                    <!-- TIPO DE MOVIMIENTO -->
                    <td><?= ucfirst(strtolower($mov['tipo_movimiento'])) ?></td>

                    <!-- ACCIONES -->
                    <td>
                        <a href="<?= base_url('movimientos/descargarCargoLote/' . $mov['lote']) ?>"
                            class="btn btn-sm btn-primary">PDF</a>

                        <button class="btn btn-sm btn-danger btnAnular" data-id="<?= $mov['id'] ?? '' ?>"
                            data-lote="<?= $mov['lote'] ?>">
                            Anular
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- MODAL DE ANULACIÓN -->
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

        const table = $('#tablaMovimientos').DataTable({
            responsive: true,
            language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
        });

        $(document).on('click', '.btnAnular', function () {
            $('#idMovimiento').val($(this).data('id'));
            $('#loteMovimiento').val($(this).data('lote'));
            $('#motivoAnulacion').val('');
            $('#modalAnular').modal('show');
        });

        $('#confirmarAnulacion').on('click', function () {
            const lote = $('#loteMovimiento').val();
            const motivo = $('#motivoAnulacion').val().trim();

            if (motivo === '') {
                Swal.fire('Atención', 'Por favor ingrese un motivo de anulación.', 'warning');
                return;
            }

            $.ajax({
                url: "<?= base_url('movimientos/anular/') ?>" + lote,
                type: 'POST',
                data: { motivo_anulacion: motivo },
                dataType: 'json',

                success: function (response) {
                    if (response.status === 'success') {

                        const row = $('tr[data-lote="' + lote + '"]');
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

                error: function () {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection(); ?>