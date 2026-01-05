<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard - Sistema de Inventario OTI</h1>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Equipos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Equipos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_bienes) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-laptop fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipos Activos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Equipos Activos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($bienes_activos) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- En Mantenimiento -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            En Mantenimiento</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($bienes_mantenimiento) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipos Asignados -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Asignados</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                    <?= number_format($bienes_asignados) ?>
                                </div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <?php $porcentaje = $total_bienes > 0 ? round(($bienes_asignados / $total_bienes) * 100) : 0; ?>
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?= $porcentaje ?>%" aria-valuenow="<?= $porcentaje ?>"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Gráfico de Movimientos por Mes -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Movimientos - Últimos 6 Meses</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($movimientos_por_mes)): ?>
                    <div class="chart-area" style="height: 300px;">
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
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Estado de Equipos</h6>
            </div>
            <div class="card-body">
                <?php if ($total_bienes > 0): ?>
                    <div class="chart-pie pt-4 pb-2" style="height: 200px;">
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
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Últimos 5 Movimientos</h6>
            </div>
            <div class="card-body">
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
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 - Usuarios con Más Equipos</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($top_usuarios)): ?>
                    <?php foreach ($top_usuarios as $usuario): ?>
                        <div class="mb-3">
                            <div class="small text-gray-700 mb-1">
                                <strong><?= strtoupper($usuario['nombre'] . ' ' . $usuario['ape_paterno'] . ' ' . $usuario['ape_materno']) ?></strong>
                            </div>
                            <div class="progress">
                                <?php $max = isset($top_usuarios[0]['total_equipos']) ? $top_usuarios[0]['total_equipos'] : 1; ?>
                                <?php $porcentaje_usuario = round(($usuario['total_equipos'] / $max) * 100); ?>
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= $porcentaje_usuario ?>%"
                                    aria-valuenow="<?= $porcentaje_usuario ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?= $usuario['total_equipos'] ?> equipo<?= $usuario['total_equipos'] > 1 ? 's' : '' ?>
                                </div>
                            </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
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
            const myAreaChart = new Chart(ctxLine.getContext('2d'), {
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
            const myPieChart = new Chart(ctxPie.getContext('2d'), {
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
                    }
                }
            });
        }
    <?php endif; ?>
</script>
<?= $this->endSection() ?>

<?= $this->endSection(); ?>