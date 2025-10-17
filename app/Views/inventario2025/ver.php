<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h2 class="my-3">Ver Asignación</h2>

<?php if (session()->getFlashdata('error') !== null) { ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>
<?php } ?>

<form class="row g-3">
    <div class="col-md-6">
        <label for="id_personas" class="form-label">Usuario Asignado:</label>
        <input type="text" class="form-control" id="id_personas" name="id_personas" value="<?= isset($inventario['nombre_completo']) ? $inventario['nombre_completo'] : ''; ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="id_departamentos" class="form-label">Departamento</label>
        <input type="text" class="form-control" id="id_departamentos" name="id_departamentos" value="<?= isset($inventario['departamento']) ? $inventario['departamento'] : ''; ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="id_locales" class="form-label">Sede Asignada:</label>
        <input type="text" class="form-control" id="id_locales" name="id_locales" value="<?= isset($inventario['sede']) ? $inventario['sede'] : ''; ?>" readonly>
    </div>

    <div class="col-12 mt-4 mb-3">
        <h4>Bienes Asignados</h4>
    </div>

    <?php
    $equipos = [
        "pc_escritorio" => "PC Escritorio",
        "teclado" => "Teclado",
        "monitor" => "Monitor",
        "impresora" => "Impresora",
        "scanner" => "Scanner",
        "otro" => "Otro"
    ];

    foreach ($equipos as $campo => $label): ?>
        <div class="col-md-4">
            <label for="<?= $campo ?>" class="form-label">Código - <?= $label ?></label>
            <input type="text" class="form-control" id="<?= $campo ?>_cod" name="<?= $campo ?>_cod" value="<?= isset($inventario[$campo . '_cod']) ? $inventario[$campo . '_cod'] : ''; ?>" readonly>
            <label for="<?= $campo ?>_desc" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="<?= $campo ?>_desc" name="<?= $campo ?>_desc" value="<?= isset($inventario[$campo . '_desc']) ? $inventario[$campo . '_desc'] : ''; ?>" readonly>
        </div>
    <?php endforeach; ?>

    <div class="col-12 mt-4">
        <a href="<?= base_url('inventario2025') ?>" class="btn btn-secondary">Regresar</a>
    </div>
</form>

<?= $this->endSection(); ?>



