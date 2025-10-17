<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3">Editar personas</h3>

<?php if (session()->getFlashdata('error') !== null) { ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>

<?php } ?>

<form action="<?= base_url('personas/' . $persona['id']); ?>" class="row g-3" method="post" autocomplete="off">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="personas_id" value="<?= $persona['id']; ?>">

    <div class="col-md-4">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" class="form-control" id="dni" name="dni" value="<?= $persona['dni'] ?>" required autofocus>
    </div>

    <div class="col-md-8">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $persona['nombre'] ?>" required>
    </div>

    <div class="col-md-6">
        <label for="ape_paterno" class="form-label">Apellido Paterno</label>
        <input type="text" class="form-control" id="ape_paterno" name="ape_paterno" value="<?= $persona['ape_paterno'] ?>" required>
    </div>

    <div class="col-md-6">
        <label for="ape_materno" class="form-label">Apellido Materno</label>
        <input type="text" class="form-control" id="ape_materno" name="ape_materno" value="<?= $persona['ape_materno'] ?>">
    </div>

    <div class="col-md-6">
        <label for="regimen_laboral" class="form-label">Asignar regimen laboral</label>
        <select class="form-select form-control" id="regimen_laboral" name="regimen_laboral" >
            <option value="">Seleccionar</option>
            <?php foreach ($regimen_laboral as $regimen) : ?>
                <option value="<?= $regimen['id']; ?>" <?php echo ($regimen['id'] == $persona['id_regimen_laboral']) ? 'selected' : ''; ?>> <?= $regimen['regimen_laboral'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>


    <div class="col-12">
        <a href="<?= base_url('personas') ?>" class="btn btn-secondary">Regresar</a>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>

</form>

<?= $this->endSection(); ?>