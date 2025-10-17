<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3" id="titulo">Inventario</h3>


<a href="<?= base_url('inventario2025/new') ?>" class="btn btn-success mb-3">Agregar</a>
<a href="<?= base_url('inventario2025/reporte_asignacion') ?>" class="btn btn-danger mb-3" target="_blank">
    <i class="fas fa-file-pdf"></i> Generar Reporte de Asignación
</a>


<table class="table table-hover table-bordered my-3" id="bienesTable" aria-describedby="titulo">
    <thead class="table-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nombre Completo</th>
            <th scope="col">Departamento</th>
            <th scope="col">Sede</th>
            <th scope="col">Máquinas Asignadas</th>
            <th scope="col">Fecha de Creación</th>
            <th scope="col">Última Actualización</th>
            <th scope="col">Opciones</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($inventario2025 as $item) : ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= $item['nombre_completo'] ?></td>
                <td><?= $item['departamento'] ?></td>
                <td><?= $item['sede'] ?></td>
                <td><?= rtrim($item['maquinas_asignadas'], ', ') ?: 'Sin asignación' ?></td>
                <td><?= esc($item['created_at']) ?></td>
                <td><?= esc($item['updated_at']) ?></td>
                <td>
                    <a href="<?= base_url('inventario2025/' . $item['id'] . '/edit') ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="<?= base_url('inventario2025/' . $item['id']) ?>" class="btn btn-info btn-sm">Ver</a>
                    <!-- Formulario para eliminar -->
                    <form action="<?= base_url('inventario2025/delete/' . $item['id']) ?>" method="post" style="display:inline;">
                        <!-- Campo oculto para simular DELETE -->
                        <input type="hidden" name="_method" value="DELETE">
                        <!-- Botón de eliminación -->
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este registro?');">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<?= $this->endSection(); ?>