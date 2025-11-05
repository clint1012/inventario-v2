<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h2>📑 Actas de Instalación por Usuario</h2>
    <a href="<?= base_url('movimientos/new') ?>" class="btn btn-primary mb-3">+ Nuevo Movimiento</a>

    <table class="table table-bordered table-striped">
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
                <tr>
                    <td><?= $mov['nombre'] . ' ' . $mov['ape_paterno'] . ' ' . $mov['ape_materno'] ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($mov['fecha_movimiento'])) ?></td>
                    <td><?= $mov['departamento'] ?></td>
                    <td><?= $mov['local'] ?></td>
                    <td><?= ucfirst($mov['tipo_movimiento']) ?></td>
                    <td>
                        <a href="<?= base_url('movimientos/descargarCargoLote/' . $mov['lote']) ?>"
                            class="btn btn-sm btn-primary">PDF</a>
                        <a href="<?= base_url('movimientos/anular/' . $mov['lote']) ?>"
                            class="btn btn-sm btn-danger">Anular</a>
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

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {

        // Abrir modal de confirmación
        $(document).on('click', '.btnAnular', function () {
            const id = $(this).data('id');
            const lote = $(this).data('lote');
            $('#idMovimiento').val(id);
            $('#loteMovimiento').val(lote);
            $('#motivoAnulacion').val('');
            $('#modalAnular').modal('show');
        });

        $('#confirmarAnulacion').on('click', function () {
            const idMovimiento = $('#idMovimiento').val();
            const loteMovimiento = $('#loteMovimiento').val();
            const motivo = $('#motivoAnulacion').val().trim();

            if (motivo === '') {
                alert('Por favor, ingrese un motivo de anulación.');
                return;
            }

            $.ajax({
                url: '<?= base_url('movimientos/anular') ?>/' + idMovimiento,
                type: 'POST',
                data: {
                    motivo_anulacion: motivo,
                    lote: loteMovimiento // 👈 se envía el lote también
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // ✅ Oculta todas las filas con el mismo lote
                        $('tr[data-lote="' + loteMovimiento + '"]').fadeOut(500, function () {
                            $(this).remove();
                        });
                        $('#modalAnular').modal('hide');
                    } else {
                        alert(response.message || 'Ocurrió un error al anular el movimiento.');
                    }
                },
                error: function () {
                    alert('Error en la solicitud. Verifica la consola o el backend.');
                }
            });
        });

        // ==============================
        //  Evitar duplicados por lote
        // ==============================
        $('#bienesTable').on('click', '.eliminarLote', function () {
            var lote = $(this).data('lote'); // el atributo data-lote que identifica el grupo

            // Confirmación
            Swal.fire({
                title: '¿Eliminar todo el lote?',
                text: 'Esto eliminará todas las filas con el mismo número de lote.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Eliminar todas las filas que tengan el mismo lote
                    table.rows(function (idx, data, node) {
                        return data[1] === lote; // Ajusta el índice de columna [1] según tu tabla
                    }).remove().draw();

                    Swal.fire('Eliminado', 'Todas las filas del lote fueron eliminadas.', 'success');
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>


<?= $this->endSection(); ?>