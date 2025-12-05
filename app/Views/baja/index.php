<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<style>
    .uppercase { text-transform: uppercase; }
</style>



<h3 class="my-3" id="titulo">Solicitud de baja de bienes</h3>

<!-- Mensajes Flash -->
<?php if (session()->has('error')) : ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<?php if (session()->has('success')) : ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<a href="<?= base_url('baja/reportePDF') ?>" class="btn btn-danger  mb-3" target="_blank">Generar PDF</a>

<a href="<?= base_url('baja/exportarExcel') ?>" class="btn btn-success mb-3">Exportar a Excel</a>






<table class="table table-hover table-bordered my-3 mb-4 mt-5" id="bienesTable" aria-describedby="titulo">
    <thead class="table-dark">
        <tr>
            <th scope="col">Codigo patrimonial</th>
            <th scope="col">Descripcion</th>
            <th scope="col">Marca</th>
            <th scope="col">Modelo</th>
            <th scope="col">Departamento</th>
            <th scope="col">Estado</th>
            <th scope="col">Fecha de compra</th>
            <th scope="col">Estado de garantia</th>
            <th scope="col">Proveedor</th>
            <th scope="col">Fecha de baja</th>
            <th scope="col">Motivo</th>
            <th scope="col">Opciones</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($bienes as $bienes) : ?>
            <div class="uppercase">
            <tr>
                <td><?= $bienes['cod_patrimonial'] ?></td>
                <td><?= $bienes['descripcion'] ?></td>
                <td><?= $bienes['marca'] ?></td>
                <td><?= $bienes['modelo'] ?></td>
                <td><?= $bienes['nombre_departamento'] ?></td>
                <td><?= $bienes['estado'] ?></td>
                <td><?= $bienes['fecha_adquisicion'] ?></td>
                <td><?= $bienes['estado_garantia'] ?></td>
                <td><?= $bienes['proveedor_id'] ?></td>
                <td><?= $bienes['updated_at'] ?></td>
                <td style="max-width:300px; white-space:pre-wrap; word-wrap:break-word; overflow-wrap:break-word;">
                    <?= esc(ltrim($bienes['motivo_baja'] ?? '')) ?>
                </td>
                <td>
                    <form action="<?= base_url('baja/recuperar/' . $bienes['id']) ?>" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-success btn-sm">Recuperar</button>
                    </form>
                </td>
                
            </tr>
            </div>
        <?php endforeach; ?>
    </tbody>

</table>



<?= $this->endSection(); ?>