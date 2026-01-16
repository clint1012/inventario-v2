<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<style>
    .stat-card {
        border-radius: 12px;
        border: none;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }
    
    .stat-card-header {
        background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);
        color: white;
        padding: 0.85rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stat-card-title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }
    
    .stat-card-body {
        padding: 1rem 1rem;
        background: white;
    }
    
    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        line-height: 1;
        margin-bottom: 0.35rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 0.75rem;
    }
    
    .export-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    
    .export-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        text-decoration: none;
        transform: scale(1.05);
    }
    
    .tipo-bien-card {
        border-left: 4px solid #c41e3a;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .tipo-bien-card:hover {
        box-shadow: 0 4px 15px rgba(196, 30, 58, 0.2);
        transform: translateX(5px);
    }
    
    .tipo-icon {
        font-size: 1.5rem;
        color: #c41e3a;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    /* Estilos unificados para tablas */
    .table-responsive table {
        font-size: 0.875rem;
    }
    
    .table-responsive thead th {
        background: #f8f9fc;
        color: #5a5c69;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
    }
    
    .table-responsive tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-responsive tbody tr:hover {
        background: #f8f9fc;
    }
    
    /* Progress bars unificados */
    .progress {
        background: #e9ecef;
        border-radius: 6px;
        overflow: hidden;
    }
    
    /* Alertas sin datos unificadas */
    .text-center.text-muted {
        color: #858796 !important;
    }
    
    .text-center.text-muted i {
        opacity: 0.5;
    }
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tachometer-alt text-danger mr-2"></i>
        Dashboard - Sistema de Inventario OTI
    </h1>
    <a href="<?= base_url('dashboard/exportarTipoBien/todos') ?>" class="btn btn-sm btn-danger shadow-sm">
        <i class="fas fa-download fa-sm"></i> Exportar Todo
    </a>
</div>

<!-- Estadísticas Generales -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="section-title">
            <i class="fas fa-chart-bar mr-2"></i>Resumen General
        </h5>
    </div>
</div>

<div class="row">
    <!-- Total Equipos -->
    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
        <div class="card stat-card shadow">
            <div class="stat-card-body">
                <div class="stat-icon" style="background: rgba(220, 38, 38, 0.1);">
                    <i class="fas fa-laptop" style="color: #c41e3a;"></i>
                </div>
                <div class="stat-number"><?= number_format($total_bienes) ?></div>
                <div class="text-muted small font-weight-600">Total Equipos</div>
            </div>
        </div>
    </div>

    <!-- Equipos Activos -->
    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
        <div class="card stat-card shadow">
            <div class="stat-card-body">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                </div>
                <div class="stat-number"><?= number_format($bienes_activos) ?></div>
                <div class="text-muted small font-weight-600">Equipos Activos</div>
            </div>
        </div>
    </div>

    <!-- En Mantenimiento -->
    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
        <div class="card stat-card shadow">
            <div class="stat-card-body">
                <div class="stat-icon" style="background: rgba(251, 191, 36, 0.1);">
                    <i class="fas fa-tools" style="color: #fbbf24;"></i>
                </div>
                <div class="stat-number"><?= number_format($bienes_mantenimiento) ?></div>
                <div class="text-muted small font-weight-600">En Mantenimiento</div>
            </div>
        </div>
    </div>

    <!-- Equipos Asignados -->
    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
        <div class="card stat-card shadow">
            <div class="stat-card-body">
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1);">
                    <i class="fas fa-user-check" style="color: #3b82f6;"></i>
                </div>
                <div class="stat-number"><?= number_format($bienes_asignados) ?></div>
                <div class="text-muted small font-weight-600">Asignados</div>
                <?php $porcentaje = $total_bienes > 0 ? round(($bienes_asignados / $total_bienes) * 100) : 0; ?>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: <?= $porcentaje ?>%"></div>
                </div>
                <small class="text-muted"><?= $porcentaje ?>% del total</small>
            </div>
        </div>
    </div>

    <!-- Total Personas -->
    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
        <div class="card stat-card shadow">
            <div class="stat-card-body">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1);">
                    <i class="fas fa-users" style="color: #8b5cf6;"></i>
                </div>
                <div class="stat-number"><?= number_format($total_personas ?? 0) ?></div>
                <div class="text-muted small font-weight-600">Personas Activas</div>
            </div>
        </div>
    </div>

    <!-- Total Licencias -->
    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
        <div class="card stat-card shadow">
            <div class="stat-card-body">
                <div class="stat-icon" style="background: rgba(236, 72, 153, 0.1);">
                    <i class="fas fa-key" style="color: #ec4899;"></i>
                </div>
                <div class="stat-number"><?= number_format($licencias_activas ?? 0) ?></div>
                <div class="text-muted small font-weight-600">Licencias Activas</div>
            </div>
        </div>
    </div>
</div>

<!-- ALERTAS - Licencias y Préstamos por Vencer -->
<?php if (!empty($licencias_por_vencer) || !empty($prestamos_por_vencer)): ?>
<div class="row mb-4">
    <div class="col-12">
        <h5 class="section-title">
            <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>Alertas y Notificaciones
        </h5>
    </div>
</div>

<div class="row">
    <!-- Alertas de Licencias por Vencer -->
    <?php if (!empty($licencias_por_vencer)): ?>
    <div class="col-xl-6 col-lg-12 mb-4">
        <div class="card shadow border-left-warning">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-key mr-2"></i>Licencias por Vencer (30 días)
                </h6>
                <span class="badge badge-warning badge-pill"><?= count($licencias_por_vencer) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Licencia</th>
                                <th>Tipo</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($licencias_por_vencer as $lic): ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('licencias') ?>" class="text-primary font-weight-bold">
                                        <?= esc($lic['nombre']) ?>
                                    </a>
                                </td>
                                <td><small><?= esc($lic['tipo']) ?></small></td>
                                <td>
                                    <small><?= date('d/m/Y', strtotime($lic['fecha_vencimiento'])) ?></small>
                                </td>
                                <td>
                                    <?php 
                                        $dias = (int)$lic['dias_restantes'];
                                        $badgeClass = 'badge-success';
                                        if ($dias <= 7) $badgeClass = 'badge-danger';
                                        elseif ($dias <= 15) $badgeClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $dias ?> día<?= $dias != 1 ? 's' : '' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Alertas de Préstamos por Vencer -->
    <?php if (!empty($prestamos_por_vencer)): ?>
    <div class="col-xl-6 col-lg-12 mb-4">
        <div class="card shadow border-left-info">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-handshake mr-2"></i>Préstamos por Vencer (7 días)
                </h6>
                <span class="badge badge-info badge-pill"><?= count($prestamos_por_vencer) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Bienes</th>
                                <th>Devolución</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestamos_por_vencer as $pres): ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('movimientos') ?>" class="text-primary font-weight-bold">
                                        <?= strtoupper(esc($pres['nombre']) . ' ' . esc($pres['ape_paterno'])) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-secondary"><?= $pres['total_bienes'] ?></span>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y', strtotime($pres['fecha_devolucion_estimada'])) ?></small>
                                </td>
                                <td>
                                    <?php 
                                        $dias = (int)$pres['dias_restantes'];
                                        $badgeClass = 'badge-success';
                                        if ($dias <= 2) $badgeClass = 'badge-danger';
                                        elseif ($dias <= 5) $badgeClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $dias ?> día<?= $dias != 1 ? 's' : '' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Estadísticas por Tipo de Bien -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="section-title">
            <i class="fas fa-cubes mr-2"></i>Bienes por Tipo
        </h5>
    </div>
</div>

<div class="row">
    <?php if (!empty($bienes_por_tipo)): ?>
        <?php 
        $iconos = [
            'computadora' => 'fa-desktop',
            'laptop' => 'fa-laptop',
            'monitor' => 'fa-tv',
            'impresora' => 'fa-print',
            'scanner' => 'fa-scanner',
            'proyector' => 'fa-video',
            'tablet' => 'fa-tablet-alt',
            'telefono' => 'fa-phone',
            'servidor' => 'fa-server',
            'switch' => 'fa-network-wired',
            'router' => 'fa-wifi',
            'ups' => 'fa-battery-full',
            'disco duro externo' => 'fa-hdd',
            'teclado' => 'fa-keyboard',
            'mouse' => 'fa-mouse',
            'webcam' => 'fa-camera',
            'microfono' => 'fa-microphone',
            'parlantes' => 'fa-volume-up',
            'otros' => 'fa-box'
        ];
        ?>
        <?php foreach ($bienes_por_tipo as $tipo): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card stat-card shadow-sm">
                    <div class="stat-card-header">
                        <span class="stat-card-title">
                            <i class="fas <?= $iconos[$tipo['tipo_bien']] ?? 'fa-box' ?> mr-2"></i>
                            <?= ucfirst($tipo['tipo_bien']) ?>
                        </span>
                        <a href="<?= base_url('dashboard/exportarTipoBien/' . urlencode($tipo['tipo_bien'])) ?>" 
                           class="export-btn">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    </div>
                    <div class="stat-card-body">
                        <div class="stat-number"><?= number_format($tipo['total']) ?></div>
                        <div class="text-muted small">
                            <?php $pct = $total_bienes > 0 ? round(($tipo['total'] / $total_bienes) * 100, 1) : 0; ?>
                            <?= $pct ?>% del total
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                No hay datos de bienes por tipo disponibles.
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Gráfico de Movimientos por Mes -->
    <div class="col-xl-8 col-lg-7">
        <div class="card stat-card shadow-sm">
            <div class="stat-card-header">
                <span class="stat-card-title">
                    <i class="fas fa-chart-line mr-2"></i>Movimientos - Últimos 6 Meses
                </span>
            </div>
            <div class="stat-card-body">
                <?php if (!empty($movimientos_por_mes)): ?>
                    <div class="chart-area" style="height: 280px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>No hay movimientos registrados en los últimos 6 meses</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gráfico de Estado de Equipos -->
    <div class="col-xl-4 col-lg-5">
        <div class="card stat-card shadow-sm">
            <div class="stat-card-header">
                <span class="stat-card-title">
                    <i class="fas fa-chart-pie mr-2"></i>Estado de Equipos
                </span>
            </div>
            <div class="stat-card-body">
                <?php if ($total_bienes > 0): ?>
                    <div class="chart-pie pb-2" style="height: 200px;">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Activos (<?= $bienes_activos ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Asignados (<?= $bienes_asignados ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Mantenimiento (<?= $bienes_mantenimiento ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Préstamo (<?= $bienes_prestamo ?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Baja (<?= $bienes_baja ?>)
                        </span>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-chart-pie fa-3x mb-3"></i>
                        <p>No hay equipos registrados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Últimos Movimientos -->
    <div class="col-xl-6 col-lg-6">
        <div class="card stat-card shadow-sm">
            <div class="stat-card-header">
                <span class="stat-card-title">
                    <i class="fas fa-history mr-2"></i>Últimos 5 Movimientos
                </span>
            </div>
            <div class="stat-card-body">
                <?php if (!empty($ultimos_movimientos)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimos_movimientos as $mov): ?>
                                    <tr>
                                        <td><small><?= date('d/m/Y H:i', strtotime($mov['fecha_movimiento'])) ?></small></td>
                                        <td>
                                            <span class="badge badge-<?=
                                                $mov['tipo_movimiento'] === 'asignacion' ? 'primary' :
                                                ($mov['tipo_movimiento'] === 'prestamo' ? 'info' :
                                                    ($mov['tipo_movimiento'] === 'retiro' ? 'danger' : 'success'))
                                                ?>">
                                                <?= ucfirst($mov['tipo_movimiento']) ?>
                                            </span>
                                        </td>
                                        <td><small><?= strtoupper($mov['nombre'] . ' ' . $mov['ape_paterno']) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-exchange-alt fa-2x mb-2"></i>
                        <p>No hay movimientos registrados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Usuarios -->
    <div class="col-xl-6 col-lg-6">
        <div class="card stat-card shadow-sm">
            <div class="stat-card-header">
                <span class="stat-card-title">
                    <i class="fas fa-trophy mr-2"></i>Top 5 - Usuarios con Más Equipos
                </span>
            </div>
            <div class="stat-card-body">
                <?php if (!empty($top_usuarios)): ?>
                    <?php foreach ($top_usuarios as $usuario): ?>
                        <div class="mb-3">
                            <div class="small text-gray-700 mb-1 font-weight-600">
                                <?= strtoupper($usuario['nombre'] . ' ' . $usuario['ape_paterno'] . ' ' . $usuario['ape_materno']) ?>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <?php $max = isset($top_usuarios[0]['total_equipos']) ? $top_usuarios[0]['total_equipos'] : 1; ?>
                                <?php $porcentaje_usuario = round(($usuario['total_equipos'] / $max) * 100); ?>
                                <div class="progress-bar" style="width: <?= $porcentaje_usuario ?>%; background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);" 
                                    role="progressbar" aria-valuenow="<?= $porcentaje_usuario ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted"><?= $usuario['total_equipos'] ?> equipo<?= $usuario['total_equipos'] > 1 ? 's' : '' ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>No hay equipos asignados a usuarios</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    // Esperar a que Chart.js esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración global de Chart.js para prevenir conflictos
        if (typeof Chart !== 'undefined') {
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;
        }
        
        <?php if (!empty($movimientos_por_mes)): ?>
        // Gráfico de Líneas - Movimientos por Mes
        const movimientosPorMes = <?= json_encode($movimientos_por_mes) ?>;
        const labels = Object.keys(movimientosPorMes).map(k => movimientosPorMes[k].label);
        const asignaciones = Object.keys(movimientosPorMes).map(k => movimientosPorMes[k].asignacion);
        const prestamos = Object.keys(movimientosPorMes).map(k => movimientosPorMes[k].prestamo);
        const retiros = Object.keys(movimientosPorMes).map(k => movimientosPorMes[k].retiro);
        const devoluciones = Object.keys(movimientosPorMes).map(k => movimientosPorMes[k].devolucion);

        const ctxLine = document.getElementById('myAreaChart');
        if (ctxLine) {
            // Limpiar canvas completamente
            const canvasParent = ctxLine.parentNode;
            const newCanvas = document.createElement('canvas');
            newCanvas.id = 'myAreaChart';
            canvasParent.replaceChild(newCanvas, ctxLine);
            
            window.myAreaChart = new Chart(newCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Asignaciones',
                        data: asignaciones,
                        borderColor: 'rgb(78, 115, 223)',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: 'rgb(78, 115, 223)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Préstamos',
                        data: prestamos,
                        borderColor: 'rgb(54, 185, 204)',
                        backgroundColor: 'rgba(54, 185, 204, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: 'rgb(54, 185, 204)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Retiros',
                        data: retiros,
                        borderColor: 'rgb(231, 74, 59)',
                        backgroundColor: 'rgba(231, 74, 59, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: 'rgb(231, 74, 59)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Devoluciones',
                        data: devoluciones,
                        borderColor: 'rgb(28, 200, 138)',
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: 'rgb(28, 200, 138)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            enabled: true,
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            titleMarginBottom: 10,
                            bodySpacing: 5,
                            callbacks: {
                                title: function (context) {
                                    return context[0].label;
                                },
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y;
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                padding: 10
                            },
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                padding: 10
                            }
                        }
                    },
                    elements: {
                        line: {
                            borderJoinStyle: 'round'
                        }
                    },
                    hover: {
                        mode: 'index',
                        intersect: false,
                        animationDuration: 0
                    },
                    animation: {
                        duration: 750
                    }
                }
            });
        }
    <?php endif; ?>

    <?php if ($total_bienes > 0 && !empty($distribucion_estados)): ?>
        // Gráfico de Dona - Estados
        const distribucion = <?= json_encode($distribucion_estados) ?>;
        const estadosLabels = Object.values(distribucion).map(e => e.label);
        const estadosData = Object.values(distribucion).map(e => e.valor);

        const coloresEstados = {
            'Activos': 'rgb(28, 200, 138)',
            'Asignados': 'rgb(54, 185, 204)',
            'Mantenimiento': 'rgb(246, 194, 62)',
            'Préstamo': 'rgb(78, 115, 223)',
            'Disponible': 'rgb(156, 163, 175)',
            'Baja': 'rgb(231, 74, 59)'
        };

        const estadosColors = estadosLabels.map(label => coloresEstados[label] || 'rgb(156, 163, 175)');

        const ctxPie = document.getElementById('myPieChart');
        if (ctxPie) {
            // Limpiar canvas completamente
            const canvasParent = ctxPie.parentNode;
            const newCanvas = document.createElement('canvas');
            newCanvas.id = 'myPieChart';
            canvasParent.replaceChild(newCanvas, ctxPie);
            
            window.myPieChart = new Chart(newCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: estadosLabels,
                    datasets: [{
                        data: estadosData,
                        backgroundColor: estadosColors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function (context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    hover: {
                        animationDuration: 0
                    },
                    animation: {
                        duration: 750
                    }
                }
            });
        }
    <?php endif; ?>
    
    }); // Fin DOMContentLoaded
</script>
<?= $this->endSection() ?>

<?= $this->endSection(); ?>