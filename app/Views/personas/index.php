<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container-fluid mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-users mr-2"></i>Gestión de Personas</h4>
            <a href="<?= base_url('personas/new') ?>" class="btn btn-light btn-sm">
                <i class="fas fa-plus-circle"></i> Nueva Persona
            </a>
            <div class="input-group w-50">
                <input type="text" id="filtroGlobal" class="form-control"
                    placeholder="Filtrar por nombre, DNI, régimen o sede...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="personalTable" class="table table-hover table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>Nombre completo</th>
                            <th>Régimen Laboral</th>
                            <th>Inicio Laboral</th>
                            <th>Fin Laboral</th>
                            <th>DNI</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Modalidad</th>
                            <th>Sede</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($personas as $p): ?>
                            <tr>
                                <td class="text-center"><?= esc($p['id']) ?></td>
                                <td><?= esc($p['nombre_completo']) ?></td>
                                <td><?= esc($p['nombre_regimen']) ?></td>
                                <td class="text-center"><?= esc($p['fecha_inicio']) ?></td>
                                <td class="text-center"><?= esc($p['fecha_fin']) ?></td>
                                <td class="text-center font-weight-bold"><?= esc($p['dni']) ?></td>
                                <td><?= esc($p['correo']) ?></td>
                                <td><?= esc($p['telefono']) ?></td>
                                <td><?= esc($p['direccion_domiciliaria']) ?></td>
                                <td class="text-capitalize text-center">
                                    <span class="badge badge-info px-2 py-1"><?= esc($p['modalidad']) ?></span>
                                </td>
                                <td><?= esc($p['nombre_local']) ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('personas/' . $p['id']) ?>" class="btn btn-secondary btn-sm mb-1">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="<?= base_url('personas/' . $p['id'] . '/edit') ?>"
                                        class="btn btn-primary btn-sm mb-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>


<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        var table = $('#personalTable').DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
            order: [[0, "asc"]],
            responsive: true,
            fixedHeader: true,
            pageLength: 10
        });

        // 🔍 Filtro combinado de varias columnas
        $('#filtroGlobal').on('keyup', function () {
            var valor = $(this).val().toLowerCase();

            // Usamos el API de DataTables para buscar
            table.rows().every(function () {
                var data = this.data();

                // Combina las columnas deseadas (ajusta los índices según tus columnas)
                var combinado = (
                    data[1] + ' ' + data[2] + ' ' + data[5] + ' ' + data[10]
                ).toLowerCase();

                // Guardamos coincidencia en una propiedad
                this.combinado = combinado.includes(valor);
            });

            // Aplicamos filtro global
            $.fn.dataTable.ext.search = [];
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var combinado = (
                    data[1] + ' ' + data[2] + ' ' + data[5] + ' ' + data[10]
                ).toLowerCase();
                return combinado.includes(valor);
            });

            table.draw(); // 🔁 Redibuja TODAS las filas, no solo las visibles
        });
    });
</script>
<?= $this->endSection(); ?>