<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3" id="titulo">Personas</h3>


    <a href="<?= base_url('personas/new') ?>" class="btn btn-success">Agregar</a>   


<table class="table table-hover table-bordered my-3" id="bienesTable" aria-describedby="titulo">
    <thead class="table-dark">
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Nombre</th>
            <th scope="col">Apellido Paterno</th>
            <th scope="col">Apellido Materno</th>
            <th scope="col">Regimen Laboral</th>
            <th scope="col">DNI</th>
            <th scope="col">Opciones</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($personas as $personas) : ?>
            <tr>
                <td><?= $personas['id'] ?></td>
                <td><?= $personas['nombre'] ?></td>
                <td><?= $personas['ape_paterno'] ?></td>
                <td><?= $personas['ape_materno'] ?></td>
                <td><?= $personas['id_regimen_laboral'] ?></td>
                <td><?= $personas['dni'] ?></td>
                <td>
                    <a href="<?= base_url('personas/' . $personas['id'] . '/edit') ?>" class="btn btn-warning btn-sm me-2">Editar</a>
                    <a href="<?= base_url('personas/' . $personas['id']) ?>" class="btn btn-warning btn-sm me-2">Ver</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<?= $this->endSection(); ?>
