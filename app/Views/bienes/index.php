<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3" id="titulo">Bienes</h3>

<!-- Mensajes Flash -->
<?php if (session()->has('error')) : ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<?php if (session()->has('success')) : ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between mb-4">
    <a href="<?= base_url('bienes/new') ?>" class="btn btn-success">Agregar</a>
    <!-- Botón para la subida masiva -->
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalSubidaMasiva">Subida masiva</button>
    <!-- Genera Reporte -->
    <a href="<?= base_url('bienes/reporte_bienes') ?>" class="btn btn-danger" target="_blank">Generar PDF</a>
</div>

<button id="toggleFilters" class="btn btn-secondary mb-3">Mostrar Filtros</button>

<div id="filterContainer" class="card p-3 mb-3" style="display: none;">
    <div class="row mb-3">
        <!-- Filtro por Fecha Desde -->
        <div class="col-md-4">
            <label for="filterFechaDesde" class="form-label">Filtrar por Fecha Desde:</label>
            <input type="date" id="filterFechaDesde" class="form-control">
        </div>
        <!-- Filtro por Fecha Hasta -->
        <div class="col-md-4">
            <label for="filterFechaHasta" class="form-label">Filtrar por Fecha Hasta:</label>
            <input type="date" id="filterFechaHasta" class="form-control">
        </div>
        <!-- Filtro por Descripción -->
        <div class="col-md-4">
            <label for="filterDescripcion" class="form-label">Filtrar por Descripción:</label>
            <input type="text" id="filterDescripcion" class="form-control" placeholder="Buscar descripción">
        </div>

    </div>

    <div class="row mb-3">
        <!-- Filtro por Estado -->
        <div class="col-md-4">
            <label for="filterEstado" class="form-label">Filtrar por Estado:</label>
            <select id="filterEstado" class="form-control">
                <option value="">Todos</option>
                <option value="mantenimiento">Mantenimiento</option>
                <option value="regular">Regular</option>
                <option value="bueno">Bueno</option>
                <option value="nuevo">Nuevo</option>
                <option value="malo">Malo</option>
            </select>
        </div>
    </div>

    <button id="clearFilters" class="btn btn-warning">Limpiar Filtros</button>

</div>


<table class="table table-hover table-bordered my-3 mb-4 mt-5" id="bienesTable" aria-describedby="titulo">
    <thead class="table-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Codigo patrimonial</th>
            <th scope="col">Descripcion</th>
            <th scope="col">Marca</th>
            <th scope="col">Modelo</th>
            <th scope="col">Serie</th>
            <th scope="col">Departamento</th>
            <th scope="col">Estado</th>
            <th scope="col">Fecha de compra</th>
            <th scope="col">Estado de garantia</th>
            <th scope="col">Usuario</th>
            <th scope="col">Opciones</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($bienes as $bienes) : ?>
            <tr>
                <td><?= $bienes['id'] ?></td>
                <td><?= $bienes['cod_patrimonial'] ?></td>
                <td><?= $bienes['descripcion'] ?></td>
                <td><?= $bienes['marca'] ?></td>
                <td><?= $bienes['modelo'] ?></td>
                <td><?= $bienes['serie'] ?></td>
                <td><?= $bienes['nombre_departamento'] ?></td>
                <td><?= $bienes['estado'] ?></td>
                <td><?= $bienes['fecha_adquisicion'] ?></td>
                <td><?= $bienes['estado_garantia'] ?></td>
                <td><?= $bienes['nombre_persona'] ?></td>
                <td>
                    <a href="<?= base_url('bienes/' . $bienes['id'] . '/edit') ?>" class="btn btn-warning btn-sm me-2 mb-1">Editar</a>
                    <a href="<?= base_url('bienes/' . $bienes['id']) ?>" class="btn btn-warning btn-sm me-2">Ver</a>
                    <a href="#" onclick="abrirModalBaja(<?= $bienes['id'] ?>)" class="btn btn-danger btn-sm me-2">Baja</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal para dar de baja un bien -->
<div class="modal fade" id="modalBaja" tabindex="-1" role="dialog" aria-labelledby="modalBajaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBajaLabel">Dar de Baja un Bien</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formBaja" action="<?= base_url('bienes/desactivar') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="bien_id" name="bien_id">

                    <div class="form-group">
                        <label for="motivo_baja">Motivo de la Baja</label>
                        <textarea class="form-control" id="motivo_baja" name="motivo_baja" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="usuario_baja">Usuario que da la Baja</label>
                        <input type="text" class="form-control" id="usuario_baja" name="usuario_baja" value="<?= session('usuario') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="foto_frente">Foto Frente</label>
                        <input type="file" class="form-control" id="foto_frente" name="foto_frente" accept="image/*" required>
                    </div>

                    <div class="form-group">
                        <label for="foto_lateral">Foto Lateral</label>
                        <input type="file" class="form-control" id="foto_lateral" name="foto_lateral" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Baja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para subida masiva -->
<div class="modal fade" id="modalSubidaMasiva" tabindex="-1" role="dialog" aria-labelledby="modalSubidaMasivaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSubidaMasivaLabel">Subida Masiva de Bienes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('bienes/subida_masiva') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="archivo" class="form-label">Seleccione el archivo (CSV o Excel)</label>
                        <input type="file" class="form-control" id="archivo" name="archivo" accept=".csv, .xls, .xlsx" required>
                    </div>
                    <small class="text-muted">El archivo debe tener los campos: Código Patrimonial, Descripción, Marca, Modelo, Departamento, Estado, etc.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Subir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmarEliminacion(id) {
        if (confirm("¿Estás seguro de que deseas eliminar este bien?")) {
            window.location.href = "<?= base_url('bienes/desactivar/') ?>" + id;
        }
    }

    function abrirModalBaja(id) {
        document.getElementById('bien_id').value = id; // Pasar el ID al modal
        $('#modalBaja').modal('show'); // Mostrar el modal
    }
</script>

<?= $this->endSection(); ?>