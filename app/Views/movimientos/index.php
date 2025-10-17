<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h2>📑 Actas de Instalación por Usuario</h2>
    <a href="<?= base_url('movimientos/new') ?>" class="btn btn-primary mb-3">+ Nuevo Movimiento</a>

    <table id="tablaUsuarios" class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Usuario</th>
            <th>Departamento</th>
            <th>Local</th>
            <th>Tipo de Movimiento</th>
            <th>Fecha Movimiento</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($usuarios)): ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['nombre'].' '.$u['ape_paterno'].' '.$u['ape_materno'] ?></td>
                    <td><?= $u['departamento'] ?? '-' ?></td>
                    <td><?= $u['local'] ?? '-' ?></td>
                    <td><?= ucfirst($u['tipo_movimiento']) ?></td>
                    <td><?= date('d/m/Y H:i:s', strtotime($u['fecha_movimiento'])) ?></td>
                    <td>
                        <a href="<?= base_url('movimientos/descargarActa/'.$u['id_personas'].'/'.$u['lote']) ?>" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary">
                           📄 Descargar Acta
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">
                    ⚠ No hay movimientos registrados
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>

<?= $this->endSection(); ?>
