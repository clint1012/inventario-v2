<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3">Ver bien</h3>

<?php if (session()->getFlashdata('error') !== null) { ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>

<?php } ?>

<form action="<?= base_url('bienes/' . $bien['id']); ?>" class="row g-3" method="post" autocomplete="off">
    <input type="hidden" name="_method" value="get">
    <input type="hidden" name="bien_id" value="<?= $bien['id'] ?>">

    <div class="col-md-4">
        <label for="cod_patrimonial" class="form-label">Cod_patrimonial</label>
        <input type="text" class="form-control" id="cod_patrimonial" name="cod_patrimonial" value="<?= $bien['cod_patrimonial'] ?>" required autofocus readonly>
    </div>

    <div class="col-md-8">
        <label for="descripcion" class="form-label">Descripcion</label>
        <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?= $bien['descripcion'] ?>" required readonly>
    </div>

    <div class="col-md-6">
        <label for="marca" class="form-label">Marca</label>
        <input type="text" class="form-control" id="marca" name="marca" value="<?= $bien['marca'] ?>" required readonly>
    </div>

    <div class="col-md-6">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" class="form-control" id="modelo" name="modelo" value="<?= $bien['modelo'] ?>" required readonly>
    </div>

    <div class="col-md-6">
        <label for="tipo_disco" class="form-label">Tipo de disco</label>
        <input type="text" class="form-control" id="tipo_disco" name="tipo_disco" value="<?= $bien['tipo_disco'] ?>" required readonly>
    </div>

    <div class="col-md-6">
        <label for="espacio_disco" class="form-label">Espacio de disco</label>
        <input type="text" class="form-control" id="espacio_disco" name="espacio_disco" value="<?= $bien['espacio_disco'] ?>" required readonly>
    </div>

    <div class="col-md-6">
        <label for="serie" class="form-label">Serie</label>
        <input type="text" class="form-control" id="serie" name="serie" value="<?= $bien['serie'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="procesador" class="form-label">Procesador</label>
        <input type="text" class="form-control" id="procesador" name="procesador" value="<?= $bien['procesador'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="memoria" class="form-label">Memoria</label>
        <input type="text" class="form-control" id="memoria" name="memoria" value="<?= $bien['memoria'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="sistema_operativo" class="form-label">Sistema_operativo</label>
        <input type="text" class="form-control" id="sistema_operativo" name="sistema_operativo" value="<?= $bien['sistema_operativo'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="ver_office" class="form-label">ver_office</label>
        <input type="text" class="form-control" id="ver_office" name="ver_office" value="<?= $bien['ver_office'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="Ip" class="form-label">Ip</label>
        <input type="text" class="form-control" id="Ip" name="Ip" value="<?= $bien['Ip'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <input type="text" class="form-control" id="estado" name="estado" value="<?= $bien['estado'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="fecha_adquisicion" class="form-label">Fecha_adquisicion</label>
        <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion" value="<?= $bien['fecha_adquisicion'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="años_garantia" class="form-label">Años de garantia</label>
        <input type="text" class="form-control" id="años_garantia" name="años_garantia" value="<?= $bien['años_garantia'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="estado_garantia" class="form-label">Estado de garantia</label>
        <input type="text" class="form-control" id="estado_garantia" name="estado_garantia" value="<?= $bien['estado_garantia'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="proveedor" class="form-label">Proveedor</label>
        <input type="text" class="form-control" id="proveedor" name="proveedor" value="<?= $bien['proveedor'] ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="departamento" class="form-label">Departamento</label>
        <select class="form-select form-control" id="departamento" name="departamento" disabled>
            <?php foreach ($departamentos as $departamento) : ?>
                <option value="<?= $departamento['id']; ?>" <?php echo ($departamento['id'] == $bien['id_departamento']) ? 'selected' : ''; ?>>
                    <?= $departamento['nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="usuario" class="form-label">Persona asignada:</label>
        <input type="text" class="form-control" id="usuario" name="usuario"
            value="<?= isset($bien['persona_nombre']) ? $bien['persona_nombre'] : '' ?>"
            placeholder="Escriba un nombre" readonly> <!-- Campo solo lectura -->
        <input type="hidden" id="usuarioId" name="id_personas"
            value="<?= isset($bien['id_personas']) ? $bien['id_personas'] : '' ?>"> <!-- ID oculto -->
    </div>

    


    <div class="col-12">
        <a href="<?= base_url('bienes') ?>" class="btn btn-secondary">Regresar</a>

    </div>

</form>

<?= $this->endSection(); ?>