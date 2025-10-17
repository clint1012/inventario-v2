<?= $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3" id="titulo">Gestión de Direcciones IP</h3>

<?php if (session()->has('mensaje')) : ?>
    <div class="alert alert-success"><?= session('mensaje') ?></div>
<?php endif; ?>

<a href="<?= base_url('ip/asignar') ?>" class="btn btn-primary mb-3">Asignar IP</a>
<a href="<?= base_url('ip/eliminar192') ?>" class="btn btn-danger mb-3" onclick="return confirm('¿Deseas eliminar todas las IPs 192.x.x.x?')">Eliminar IPs 192.*</a>
<a href="#" id="exportExcel" class="btn btn-success mb-3">Exportar a Excel</a>
<a href="#" id="exportPDF" class="btn btn-danger mb-3">Exportar a PDF</a>

<div class="mb-3">
    <a href="<?= base_url('ip') ?>" class="btn btn-secondary <?= !$subred_actual ? 'active' : '' ?>">Todos</a>
    <a href="<?= base_url('ip?subred=172.17.10') ?>" class="btn btn-primary <?= $subred_actual === '172.17.10' ? 'active' : '' ?>">172.17.10.x</a>
    <a href="<?= base_url('ip?subred=172.17.11') ?>" class="btn btn-primary <?= $subred_actual === '172.17.11' ? 'active' : '' ?>">172.17.11.x</a>
    <a href="<?= base_url('ip?subred=172.17.12') ?>" class="btn btn-primary <?= $subred_actual === '172.17.12' ? 'active' : '' ?>">172.17.12.x</a>
</div>

<!-- Filtros -->
<div id="filterContainer" class="card p-3 mb-3" style="display: none;">
    <div class="row mb-3">
        <!-- Filtrar por Subred -->
        <div class="col-md-4">
            <label for="filterSubred">Filtrar por Subred:</label>
            <select id="filterSubred" class="form-control">
                <option value="">Todas</option>
                <?php
                $subredes = [];
                foreach ($ips as $ip) {
                    $partes = explode('.', $ip['direccion_ip']);
                    $subred = $partes[0] . '.' . $partes[1] . '.' . $partes[2];
                    $subredes[$subred] = true;
                }
                foreach (array_keys($subredes) as $subred):
                ?>
                    <option value="<?= $subred ?>"><?= $subred ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Buscar por IP -->
        <div class="col-md-4">
            <label for="filterIP">Buscar por Dirección IP:</label>
            <input type="text" id="filterIP" class="form-control" placeholder="Ej. 172.16.1.3">
        </div>

        <!-- Buscar por Patrimonial -->
        <div class="col-md-4">
            <label for="filterPatrimonial">Buscar por Cod. Patrimonial:</label>
            <input type="text" id="filterPatrimonial" class="form-control" placeholder="Ej. TC-000123">
        </div>
    </div>

    <button id="clearFilters" class="btn btn-warning">Limpiar Filtros</button>
</div>

<table class="table table-hover table-bordered my-3 mb-4 mt-4" id="tablaIps">
    <thead class="table-dark">
        <tr>
            <th>Dirección IP</th>
            <th>Estado</th>
            <th>Código Patrimonial</th>
            <th>Descripción</th>
            <th>Fecha Asignación</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ips as $ip) : ?>
            <tr>
                <td><?= esc($ip['direccion_ip']) ?></td>
                <td><?= esc($ip['estado']) ?></td>
                <td><?= esc($ip['cod_patrimonial'] ?? '-') ?></td>
                <td><?= esc($ip['descripcion'] ?? '-') ?></td>
                <td><?= esc($ip['fecha_asignacion'] ?? '-') ?></td>
                <td>
                    <?php if ($ip['estado'] === 'asignado') : ?>
                        <a href="<?= base_url('ip/liberar/' . $ip['id']) ?>" class="btn btn-warning btn-sm" onclick="return confirm('¿Deseas liberar esta IP?')">Liberar</a>
                    <?php else : ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('#tablaIps').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            }
        });
    });
</script>

<script>
    // Mostrar / Ocultar filtros
    document.getElementById('toggleFilters').addEventListener('click', function () {
        const container = document.getElementById('filterContainer');
        container.style.display = (container.style.display === 'none') ? 'block' : 'none';
    });

    // Filtrado en tabla
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('tablaIps');
        const rows = table.querySelectorAll('tbody tr');

        function filterTable() {
            const subred = document.getElementById('filterSubred').value.trim();
            const ip = document.getElementById('filterIP').value.toLowerCase().trim();
            const patrimonial = document.getElementById('filterPatrimonial').value.toLowerCase().trim();

            rows.forEach(row => {
                const ipText = row.cells[0].innerText.toLowerCase();
                const patrimonialText = row.cells[2].innerText.toLowerCase();
                const subredText = ipText.split('.').slice(0, 3).join('.');

                const show =
                    (subred === '' || subred === subredText) &&
                    (ip === '' || ipText.includes(ip)) &&
                    (patrimonial === '' || patrimonialText.includes(patrimonial));

                row.style.display = show ? '' : 'none';
            });
        }

        document.getElementById('filterSubred').addEventListener('change', filterTable);
        document.getElementById('filterIP').addEventListener('input', filterTable);
        document.getElementById('filterPatrimonial').addEventListener('input', filterTable);
        document.getElementById('clearFilters').addEventListener('click', function () {
            document.getElementById('filterSubred').value = '';
            document.getElementById('filterIP').value = '';
            document.getElementById('filterPatrimonial').value = '';
            filterTable();
        });
    });
</script>

<script>
document.getElementById('exportExcel').addEventListener('click', function () {
    exportFiltered('excel');
});

document.getElementById('exportPDF').addEventListener('click', function () {
    exportFiltered('pdf');
});

function exportFiltered(type) {
    const subred = document.getElementById('filterSubred').value;
    const ip = document.getElementById('filterIP').value;
    const patrimonial = document.getElementById('filterPatrimonial').value;

    let url = type === 'excel' ? '<?= base_url('ip/exportarExcel') ?>' : '<?= base_url('ip/exportarPDF') ?>';
    url += '?subred=' + encodeURIComponent(subred) +
           '&ip=' + encodeURIComponent(ip) +
           '&patrimonial=' + encodeURIComponent(patrimonial);

    window.open(url, '_blank');
}
</script>



<?= $this->endSection(); ?>
