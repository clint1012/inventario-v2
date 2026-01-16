<?php
// ...existing code...
try {
    $db = \Config\Database::connect();
    echo '<div style="background:#ffe;border:1px solid #cc0;padding:5px 10px;margin-bottom:10px;font-size:13px;">Base de datos activa: <b>' . $db->getDatabase() . '</b></div>';
} catch (Exception $e) {
    echo '<div style="background:#fcc;color:#900;padding:5px 10px;">Error obteniendo base de datos: ' . $e->getMessage() . '</div>';
}
?>
<?php echo $this->extend('plantilla'); ?>
<!-- jQuery debe cargarse antes de cualquier script que use $ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?= $this->section('contenido'); ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Inventarios</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?= count($inventarios) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Bienes</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?= array_sum(array_map(function($inv) use ($detalles) { return count($detalles[$inv['id']] ?? []); }, $inventarios)) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Año Actual</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?= date('Y') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 text-right align-self-center">
        <a class="btn btn-sm btn-danger" href="#" id="btnExportarPDF">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a class="btn btn-sm btn-success" href="#" id="btnExportarExcel">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
        <a class="btn btn-sm btn-secondary" href="<?= base_url('inventario'); ?>">Nuevo inventario</a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="form-row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" id="buscadorInventario"
                    placeholder="Buscar por nombre de usuario o código SBN...">
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filtroAnio">
                    <option value="">Todos los años</option>
                    <?php
                    $anios = array_unique(array_column($inventarios, 'anio'));
                    rsort($anios);
                    foreach ($anios as $anio): ?>
                        <option value="<?= $anio ?>"><?= $anio ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filtroMes">
                    <option value="">Todos los meses</option>
                    <option value="Enero">Enero</option>
                    <option value="Febrero">Febrero</option>
                    <option value="Marzo">Marzo</option>
                    <option value="Abril">Abril</option>
                    <option value="Mayo">Mayo</option>
                    <option value="Junio">Junio</option>
                    <option value="Julio">Julio</option>
                    <option value="Agosto">Agosto</option>
                    <option value="Septiembre">Septiembre</option>
                    <option value="Octubre">Octubre</option>
                    <option value="Noviembre">Noviembre</option>
                    <option value="Diciembre">Diciembre</option>
                </select>
            </div>
        </div>

        <?php if (empty($inventarios)): ?>
            <!-- jQuery debe cargarse antes de cualquier script que use $ -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <div class="p-4 text-center text-muted">No hay inventarios registrados.</div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($inventarios as $inv): ?>
                    <div class="list-group-item inventario-item mb-4" data-usuario="<?= esc(strtolower($inv['usuario'] ?? '')); ?>"
                        data-anio="<?= esc($inv['anio']); ?>" data-mes="<?= esc($inv['mes'] ?? ''); ?>"
                        data-bienes="<?= esc(strtolower(json_encode(array_column($detalles[$inv['id']] ?? [], 'cod_patrimonial')))); ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="mb-1 text-primary"><i class="fas fa-user"></i> <?= esc($inv['usuario'] ?? 'Usuario desconocido'); ?></h5>
                                <small class="d-block">
                                    <span class="badge badge-info">Periodo: <?= esc($inv['mes'] ?? '—'); ?> / <?= esc($inv['anio']); ?></span>
                                </small>
                                <?php if (!empty($inv['regimen'])): ?>
                                    <small class="d-block text-muted">
                                        <i class="fas fa-briefcase"></i> Régimen: <?= esc($inv['regimen']); ?>
                                    </small>
                                <?php endif; ?>
                                <?php if (!empty($inv['jefe'])): ?>
                                    <small class="d-block text-muted">
                                        <i class="fas fa-user-tie"></i> Jefe Responsable: <strong><?= esc($inv['jefe']); ?></strong>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-success badge-pill mr-2"
                                    style="min-height: 2rem; display: flex; align-items: center; justify-content: center; font-size:1.1em;">
                                    <i class="fas fa-box"></i> <?= count($detalles[$inv['id']] ?? []); ?> bienes
                                </span>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('inventario/editar/' . $inv['id']); ?>"
                                        class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="eliminarInventario(<?= $inv['id']; ?>)" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($detalles[$inv['id']])): ?>
                            <div class="mt-3">
                                <table class="table table-bordered table-hover table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>SBN</th>
                                            <th>Descripción</th>
                                            <th>Marca</th>
                                            <th>Serie</th>
                                            <th>Local</th>
                                            <th>Departamento</th>
                                            <th>Condición</th>
                                            <th>Comentario</th>
                                            <th>Verificado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalles[$inv['id']] as $detalle): ?>
                                            <tr>
                                                <td><?= esc($detalle['cod_patrimonial'] ?? '—'); ?></td>
                                                <td><?= esc($detalle['descripcion'] ?? '—'); ?></td>
                                                <td><?= esc($detalle['marca'] ?? '—'); ?></td>
                                                <td><?= esc($detalle['serie'] ?? '—'); ?></td>
                                                <td><?= esc($detalle['local'] ?? '—'); ?></td>
                                                <td><?= esc($detalle['departamento'] ?? '—'); ?></td>
                                                <td><?= esc($detalle['condicion'] ?? '—'); ?> 
                                                    <!-- ...existing code... -->
                                                </td>
                                                <td><?= esc($detalle['comentario'] ?? ''); ?></td>
                                                <td>
                                                    <?php if ($detalle['verificado']): ?>
                                                        <span class="badge badge-success">Sí</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">No</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const eliminarInventarioUrl = "<?= base_url('inventario/eliminar'); ?>";
    const csrfTokenName = "<?= csrf_token() ?>";

    function filtrarInventarios() {
        const searchTerm = $('#buscadorInventario').val().toLowerCase().trim();
        const filtroAnio = $('#filtroAnio').val();
        const filtroMes = $('#filtroMes').val();

        $('.inventario-item').each(function () {
            const usuario = String($(this).data('usuario') || '');
            const anio = String($(this).data('anio') || '');
            const mes = String($(this).data('mes') || '');
            const bienesJson = $(this).data('bienes');
            const bienes = typeof bienesJson === 'string' ? bienesJson : JSON.stringify(bienesJson || []);

            const coincideTexto = !searchTerm || usuario.includes(searchTerm) || bienes.includes(searchTerm);
            const coincideAnio = !filtroAnio || anio === filtroAnio;
            const coincideMes = !filtroMes || mes === filtroMes;

            if (coincideTexto && coincideAnio && coincideMes) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function construirUrlExportar(baseUrl) {
        const params = new URLSearchParams();
        const busqueda = $('#buscadorInventario').val().trim();
        const anio = $('#filtroAnio').val();
        const mes = $('#filtroMes').val();

        if (busqueda) params.append('busqueda', busqueda);
        if (anio) params.append('anio', anio);
        if (mes) params.append('mes', mes);

        return baseUrl + (params.toString() ? '?' + params.toString() : '');
    }

    function eliminarInventario(inventarioId) {
        if (!confirm('¿Está seguro de eliminar este inventario? Esta acción no se puede deshacer.')) {
            return;
        }

        $.post(eliminarInventarioUrl, {
            [csrfTokenName]: $('input[name="<?= csrf_token() ?>"]').val(),
            inventario_id: inventarioId
        })
            .done(function (response) {
                location.reload();
            })
            .fail(function () {
                alert('No se pudo eliminar el inventario.');
            });
    }

    $(document).ready(function() {
        $('#buscadorInventario').on('keyup', filtrarInventarios);
        $('#filtroAnio').on('change', filtrarInventarios);
        $('#filtroMes').on('change', filtrarInventarios);

        $('#btnExportarPDF').on('click', function (e) {
            e.preventDefault();
            const url = construirUrlExportar('<?= base_url('inventario/exportar-pdf'); ?>');
            window.open(url, '_blank');
        });

        $('#btnExportarExcel').on('click', function (e) {
            e.preventDefault();
            const url = construirUrlExportar('<?= base_url('inventario/exportar-excel'); ?>');
            window.open(url, '_blank');
        });
    });

    $('#btnExportarExcel').on('click', function (e) {
        e.preventDefault();
        const url = construirUrlExportar('<?= base_url('inventario/exportar-excel'); ?>');
        window.location.href = url;
    });
</script>
<?= $this->endSection(); ?>

