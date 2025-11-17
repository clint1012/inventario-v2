<?= $this->extend('plantilla') ?>
<?= $this->section('contenido') ?>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php endif; ?>

<style>
  /* --- Estilos Tabla --- */
  /* Evita márgenes excesivos en la tabla */
  #tablaProveedores {
    width: 100% !important;
  }

  #tablaProveedores tbody tr:hover {
    background: #f1f7ff;
    cursor: pointer;
  }

  /* --- Modal más bonito --- */

  .modal-content {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }


  /* Hacer que el wrapper de DataTables no empuje hacia la derecha */
  #tablaProveedores_wrapper {
    width: 98% !important;
    margin-left: 0 !important;
  }

  /* Ajustar el contenedor para que empiece más a la izquierda */
  .container.mt-4 {
    max-width: 100%;
    margin-left: 20px !important;
    /* mueve todo hacia la izquierda */
    margin-right: 20px;
    text-align: left !important;
    /* evita que el botón y título se centren */
  }

  /* Alineación más equilibrada entre buscador y selector */
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter {
    margin-bottom: 15px;
  }

  .dataTables_wrapper .dataTables_filter {
    float: right !important;
  }

  .dataTables_wrapper .dataTables_length {
    float: left !important;
  }

  /* Limitar ancho de la columna Dirección */
  #tablaProveedores td:nth-child(8),
  #tablaProveedores th:nth-child(8) {
    max-width: 180px;
    /* ajusta el tamaño */
    white-space: normal !important;
    word-wrap: break-word !important;
  }

  /* Limitar ancho de la columna Dirección */
  #tablaProveedores td:nth-child(7),
  #tablaProveedores th:nth-child(7) {
    max-width: 180px;
    /* ajusta el tamaño */
    white-space: normal !important;
    word-wrap: break-word !important;
  }

  /* Convertir toda la tabla en MAYÚSCULAS */
  #tablaProveedores td,
  #tablaProveedores th {
    text-transform: uppercase !important;
  }

  /* PERO el correo debe ser minúscula */
  #tablaProveedores td:nth-child(7) {
    text-transform: lowercase !important;
  }
</style>


<div class="container mt-4">
  <h2 class="mb-3">📦 Gestión de Proveedores</h2>
  <button id="btnNuevo" class="btn btn-primary mb-3">+ Nuevo Proveedor</button>

  <table id="tablaProveedores" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre Empresa</th>
        <th>Representante Legal</th>
        <th>RUC</th>
        <th>Teléfono Movil</th>
        <th>Teléfono fijo</th>
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
  <div class="modal-dialog modal-lg"> <!-- más ancho -->
    <div class="modal-content">
      <form id="formProveedor" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Proveedor</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="id" name="id">

          <div class="row">

            <!-- Columna izquierda -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Nombre Empresa</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Representante Legal</label>
                <input type="text" id="rep_legal" name="rep_legal" class="form-control" required>
              </div>

              <div class="form-group">
                <label>RUC</label>
                <input type="text" id="ruc" name="ruc" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Teléfono móvil</label>
                <input type="text" id="telefono" name="telefono" class="form-control">
              </div>

              <div class="form-group">
                <label>Teléfono fijo</label>
                <input type="text" id="tel_fijo" name="tel_fijo" class="form-control">
              </div>
            </div>

            <!-- Columna derecha -->
            <div class="col-md-6">
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