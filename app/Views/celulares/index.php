<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>📱 Gestión de Equipos Celulares</h2>
        <div>
            <a href="<?= base_url('celulares/nuevo') ?>" class="btn btn-success">+ Registrar Celular</a>
            <a href="<?= base_url('celulares/movimientos') ?>" class="btn btn-primary">📋 Movimientos</a>
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
            <table id="tablaCelulares" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>IMEI</th>
                        <th>N/S</th>
                        <th>Modelo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($celulares as $cel): ?>
                        <tr>
                            <td><?= esc($cel['imei']) ?></td>
                            <td><?= esc($cel['numero_serie'] ?? 'N/A') ?></td>
                            <td><?= esc($cel['modelo']) ?></td>
                            <td><?= esc($cel['descripcion'] ?? '') ?></td>
                            <td>
                                <?php if ($cel['estado'] === 'disponible'): ?>
                                    <span class="badge badge-success">Disponible</span>
                                <?php elseif ($cel['estado'] === 'asignado'): ?>
                                    <span class="badge badge-info">Asignado</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Baja</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('celulares/editar/' . $cel['id']) ?>" 
                                   class="btn btn-sm btn-warning">Editar</a>
                                <?php if ($cel['estado'] !== 'baja'): ?>
                                    <a href="<?= base_url('celulares/baja/' . $cel['id']) ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Dar de baja este celular?')">Baja</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaCelulares').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[4, 'asc'], [2, 'asc']]
    });
});
</script>

<?= $this->endSection(); ?>
