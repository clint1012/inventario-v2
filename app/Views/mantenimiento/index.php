<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3" id="titulo">Mantenimiento</h3>

<?php if (session()->has('error')) : ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<?php if (session()->has('success')) : ?>
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
                    <form action="<?= base_url('mantenimiento/recuperar/' . $bien['id']) ?>" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-success btn-sm">Recuperar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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
});
</script>
<?= $this->endSection(); ?>
