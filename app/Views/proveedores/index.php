<?= $this->extend('plantilla') ?>
<?= $this->section('contenido') ?>

<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="container mt-4">
    <h2>📦 Gestión de Proveedores</h2>
    <button id="btnNuevo" class="btn btn-primary mb-3">+ Nuevo Proveedor</button>

    <table id="tablaProveedores" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>RUC</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Giro</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal para crear/editar -->
<div class="modal fade" id="modalProveedor" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formProveedor" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Proveedor</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="id" name="id">

          <div class="form-group">
            <label>Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
          </div>

          <div class="form-group">
            <label>RUC</label>
            <input type="text" id="ruc" name="ruc" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" id="telefono" name="telefono" class="form-control">
          </div>

          <div class="form-group">
            <label>Correo</label>
            <input type="email" id="correo" name="correo" class="form-control">
          </div>

          <div class="form-group">
            <label>Dirección</label>
            <input type="text" id="direccion" name="direccion" class="form-control">
          </div>

          <div class="form-group">
            <label>Giro</label>
            <input type="text" id="giro" name="giro" class="form-control">
          </div>

          <div class="form-group">
            <label>RNP (PDF)</label>
            <input type="file" name="rnp" class="form-control-file">
          </div>

          <div class="form-group">
            <label>Ficha RUC (PDF)</label>
            <input type="file" name="ficha_ruc" class="form-control-file">
          </div>

          <div class="form-group">
            <label>Estado</label>
            <select id="estado" name="estado" class="form-control">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
    const base_url = "<?= base_url('/') ?>";
</script>
<script src="<?= base_url('js/proveedores.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
