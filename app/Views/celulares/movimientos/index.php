<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>📋 Movimientos de Equipos Celulares</h2>
        <div>
            <a href="<?= base_url('celulares') ?>" class="btn btn-secondary">← Celulares</a>
            <a href="<?= base_url('celulares/movimientos/nuevo') ?>" class="btn btn-primary">+ Nuevo Movimiento</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table id="tablaMovimientos" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Equipos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $mov): ?>
                        <tr>
                            <td>
                                <?= strtoupper(trim(
                                    ($mov['nombre'] ?? '') . ' ' . 
                                    ($mov['ape_paterno'] ?? '') . ' ' . 
                                    ($mov['ape_materno'] ?? '')
                                )) ?: 'N/A' ?>
                            </td>
                            <td><?= date('d-m-Y H:i', strtotime($mov['fecha_movimiento'])) ?></td>
                            <td>
                                <?php if ($mov['tipo_movimiento'] === 'entrega'): ?>
                                    <span class="badge badge-success">Entrega</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Devolución</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= esc($mov['celulares_detalle'] ?? 'N/A') ?></small>
                                <br><span class="badge badge-secondary"><?= $mov['cantidad_celulares'] ?> equipo(s)</span>
                            </td>
                            <td>
                                <a href="<?= base_url('celulares/movimientos/pdf/' . $mov['lote']) ?>" 
                                   class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                                
                                <?php if (!$mov['anulado']): ?>
                                    <button class="btn btn-sm btn-danger btnAnular" 
                                            data-lote="<?= $mov['lote'] ?>">
                                        Anular
                                    </button>
                                <?php else: ?>
                                    <span class="badge badge-danger">Anulado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL DE ANULACIÓN -->
<div class="modal fade" id="modalAnular" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar anulación</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas anular este movimiento?</p>
                <div class="form-group">
                    <label>Motivo de anulación:</label>
                    <textarea id="motivoAnulacion" class="form-control" rows="3" 
                              placeholder="Ej: Error en el registro"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarAnular">Anular</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaMovimientos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[1, 'desc']]
    });

    let loteAnular = '';

    // Abrir modal de anulación
    $('.btnAnular').click(function() {
        loteAnular = $(this).data('lote');
        $('#modalAnular').modal('show');
    });

    // Confirmar anulación
    $('#confirmarAnular').click(function() {
        const motivo = $('#motivoAnulacion').val().trim();
        
        if (!motivo) {
            alert('Debe ingresar un motivo de anulación');
            return;
        }

        $.ajax({
            url: '<?= base_url('celulares/movimientos/anular') ?>',
            type: 'POST',
            data: {
                lote: loteAnular,
                motivo: motivo,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al procesar la anulación');
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
