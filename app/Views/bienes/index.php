<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3" id="titulo">Bienes</h3>
<!-- Mensajes Flash -->
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>



<div class="d-flex justify-content-between mb-4">
    <a href="<?= base_url('bienes/new') ?>" class="btn btn-success">
        Agregar
    </a>

    <!-- <button id="btnGenerarPDF" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Generar PDF
    </button> -->
</div>


<table class="table table-hover table-bordered my-3 mb-4 mt-5" id="bienesTable" aria-describedby="titulo">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Código patrimonial</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Serie</th>
            <th>Local</th>
            <th>Departamento</th>
            <th>Estado</th>
            <th>Fecha de compra</th>
            <th>Estado de garantía</th>
            <th>Usuario</th>
            <th>Opciones</th>
        </tr>
        <tr>
            <th></th>
            <th><input type="text" class="form-control form-control-sm" placeholder="Buscar código"></th>
            <th><input type="text" class="form-control form-control-sm" placeholder="Buscar descripción"></th>
            <th>
                <select class="form-control form-control-sm">
                    <option value="">Todos</option>
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm">
                    <option value="">Todos</option>
                </select>
            </th>
            <th><input type="text" class="form-control form-control-sm" placeholder="Buscar serie"></th>
            <th><select class="form-control form-control-sm">
                    <option value="">Todos</option>
                </select></th>
            <th><select class="form-control form-control-sm">
                    <option value="">Todos</option>
                </select></th>
            <th>
                <select class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="activo">Activo</option>
                    <option value="asignado">Asignado</option>
                    <option value="mantenimiento">Mantenimiento</option>
                    <option value="retirado">Retirado</option>
                    <option value="regular">Regular</option>
                    <option value="bueno">Bueno</option>
                    <option value="malo">Malo</option>
                    <option value="nuevo">Nuevo</option>
                </select>
            </th>
            <th><input type="date" class="form-control form-control-sm"></th>
            <th>
                <select class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="en garantía">En garantía</option>
                    <option value="garantía caducada">Garantía caducada</option>
                </select>
            </th>
            <th><input type="text" class="form-control form-control-sm" placeholder="Buscar usuario"></th>
            <th>
                <button id="clearFilters" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-eraser"></i> Limpiar filtros
                </button>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($bienes as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td><?= $b['cod_patrimonial'] ?></td>
                <td><?= $b['descripcion'] ?></td>
                <td><?= $b['marca'] ?></td>
                <td><?= $b['modelo'] ?></td>
                <td><?= $b['serie'] ?></td>
                <td><?= $b['nombre_local'] ?? '' ?></td>
                <td><?= $b['nombre_departamento'] ?></td>
                <td><?= $b['estado'] ?></td>
                <td><?= $b['fecha_adquisicion'] ?></td>
                <td><?= $b['estado_garantia'] ?></td>
                <td><?= $b['nombre_persona'] ?></td>
                <td>
                    <a href="<?= base_url('bienes/' . $b['id'] . '/edit') ?>"
                        class="btn btn-warning btn-sm me-2 mb-1">Editar</a>
                    <a href="<?= base_url('bienes/' . $b['id']) ?>" class="btn btn-warning btn-sm me-2">Ver</a>
                    <a href="#" onclick="abrirModalBaja(<?= $b['id'] ?>)" class="btn btn-danger btn-sm me-2"> Dar de
                        baja</a>
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
            <form id="formBaja" action="<?= base_url('bienes/desactivar') ?>" method="post"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="bien_id" name="bien_id">
                    <div class="form-group">
                        <label for="motivo_baja">Motivo de la Baja</label>
                        <textarea class="form-control" id="motivo_baja" name="motivo_baja" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="usuario_baja">Usuario que da la Baja</label>
                        <input type="text" class="form-control" id="usuario_baja" name="usuario_baja"
                            value="<?= session('usuario') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="foto_frente">Foto Frente</label>
                        <input type="file" class="form-control" id="foto_frente" name="foto_frente" accept="image/*"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="foto_lateral">Foto Lateral</label>
                        <input type="file" class="form-control" id="foto_lateral" name="foto_lateral" accept="image/*"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"> Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Baja</button>
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
    } function abrirModalBaja(id) {
        document.getElementById('bien_id').value = id; // Pasar el ID al modal 
        $('#modalBaja').modal('show'); // Mostrar el modal 
    } 
</script>


<?= $this->endSection(); ?>