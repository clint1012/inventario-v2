<?= $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h4>Editar Usuario</h4>

<form action="<?= base_url('/usuarios/update/'.$usuario['id']) ?>" method="post">

    <div class="form-group">
        <label>Usuario</label>
        <input name="usuario" class="form-control" value="<?= esc($usuario['usuario']) ?>" required>
    </div>

    <div class="form-group">
        <label>Nombre</label>
        <input name="nombre" class="form-control" value="<?= esc($usuario['nombre']) ?>" required>
    </div>

    <div class="form-group">
        <label>Correo</label>
        <input name="correo" class="form-control" value="<?= esc($usuario['correo']) ?>">
    </div>

    <div class="form-group">
        <label>Nueva contraseña (opcional)</label>
        <input name="password" type="password" class="form-control" placeholder="Dejar vacío para no cambiar">
    </div>

    <button class="btn btn-primary">Guardar cambios</button>
    <a href="<?= base_url('/usuarios') ?>" class="btn btn-secondary">Cancelar</a>

</form>

<?= $this->endSection(); ?>
