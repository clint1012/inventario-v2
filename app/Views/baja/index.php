<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

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
                <option value="activo">Activo</option>
                <option value="asignado">Asignado</option>
                <option value="retirado">Retirado</option>
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
            <th scope="col">Opciones</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($bienes as $bienes) : ?>
            <tr>
                <td><?= $bienes['cod_patrimonial'] ?></td>
                <td><?= $bienes['descripcion'] ?></td>
                <td><?= $bienes['marca'] ?></td>
                <td><?= $bienes['modelo'] ?></td>
                <td><?= $bienes['nombre_departamento'] ?></td>
                <td><?= $bienes['estado'] ?></td>
                <td><?= $bienes['fecha_adquisicion'] ?></td>
                <td><?= $bienes['estado_garantia'] ?></td>
                <td><?= $bienes['proveedor'] ?></td>
                <td><?= $bienes['updated_at'] ?></td>
                <td>
                    <form action="<?= base_url('baja/recuperar/' . $bienes['id']) ?>" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-success btn-sm">Recuperar</button>
                    </form>
                </td>
                
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<?= $this->endSection(); ?>