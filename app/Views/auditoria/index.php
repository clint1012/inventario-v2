<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<style>
    .form-card {
        border-radius: 8px;
        transition: box-shadow 0.3s ease;
    }
    .stats-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .badge-accion {
        font-size: 0.8rem;
        padding: 0.4rem 0.7rem;
    }
    .timeline-item {
        border-left: 2px solid #dee2e6;
        padding-left: 1.5rem;
        position: relative;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 8px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #0d6efd;
    }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="fas fa-history text-primary"></i> Auditoría del Sistema
        </h4>
        <p class="text-muted mb-0 small">Registro de eventos y acciones de usuarios</p>
    </div>
    <div>
        <a href="<?= base_url('auditoria/exportar?' . http_build_query($filtros)) ?>" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Exportar Excel
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stats-card shadow-sm" style="border-left-color: #0d6efd;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Eventos</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['total_eventos']) ?></h3>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem;">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card shadow-sm" style="border-left-color: #28a745;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Eventos Hoy</h6>
                        <h3 class="mb-0"><?= number_format($estadisticas['eventos_hoy']) ?></h3>
                    </div>
                    <div class="text-success" style="font-size: 2.5rem;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card shadow-sm" style="border-left-color: #ffc107;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Módulos Activos</h6>
                        <h3 class="mb-0"><?= count($estadisticas['por_modulo']) ?></h3>
                    </div>
                    <div class="text-warning" style="font-size: 2.5rem;">
                        <i class="fas fa-th-large"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card shadow-sm" style="border-left-color: #17a2b8;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Usuarios Activos Hoy</h6>
                        <h3 class="mb-0"><?= count($estadisticas['usuarios_activos']) ?></h3>
                    </div>
                    <div class="text-info" style="font-size: 2.5rem;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow-sm form-card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
    </div>
    <div class="card-body">
        <form method="get" action="<?= base_url('auditoria') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" 
                           value="<?= $filtros['usuario'] ?? '' ?>" placeholder="Nombre del usuario">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Módulo</label>
                    <select name="modulo" class="form-select">
                        <option value="">Todos</option>
                        <option value="Bienes" <?= ($filtros['modulo'] ?? '') === 'Bienes' ? 'selected' : '' ?>>Bienes</option>
                        <option value="Movimientos" <?= ($filtros['modulo'] ?? '') === 'Movimientos' ? 'selected' : '' ?>>Movimientos</option>
                        <option value="Usuarios" <?= ($filtros['modulo'] ?? '') === 'Usuarios' ? 'selected' : '' ?>>Usuarios</option>
                        <option value="Inventario" <?= ($filtros['modulo'] ?? '') === 'Inventario' ? 'selected' : '' ?>>Inventario</option>
                        <option value="Licencias" <?= ($filtros['modulo'] ?? '') === 'Licencias' ? 'selected' : '' ?>>Licencias</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Acción</label>
                    <select name="accion" class="form-select">
                        <option value="">Todas</option>
                        <option value="CREAR" <?= ($filtros['accion'] ?? '') === 'CREAR' ? 'selected' : '' ?>>Crear</option>
                        <option value="EDITAR" <?= ($filtros['accion'] ?? '') === 'EDITAR' ? 'selected' : '' ?>>Editar</option>
                        <option value="ELIMINAR" <?= ($filtros['accion'] ?? '') === 'ELIMINAR' ? 'selected' : '' ?>>Eliminar</option>
                        <option value="LOGIN" <?= ($filtros['accion'] ?? '') === 'LOGIN' ? 'selected' : '' ?>>Login</option>
                        <option value="LOGOUT" <?= ($filtros['accion'] ?? '') === 'LOGOUT' ? 'selected' : '' ?>>Logout</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" 
                           value="<?= $filtros['fecha_desde'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" 
                           value="<?= $filtros['fecha_hasta'] ?? '' ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Auditoría -->
<div class="card shadow-sm form-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-calendar me-1"></i>Fecha/Hora</th>
                        <th><i class="fas fa-user me-1"></i>Usuario</th>
                        <th><i class="fas fa-cube me-1"></i>Módulo</th>
                        <th><i class="fas fa-bolt me-1"></i>Acción</th>
                        <th><i class="fas fa-network-wired me-1"></i>IP</th>
                        <th class="text-center"><i class="fas fa-cog me-1"></i>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditoria)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No hay registros de auditoría
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($auditoria as $evento): ?>
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?php
                                        $fecha = new DateTime($evento['created_at'], new DateTimeZone('UTC'));
                                        $fecha->setTimezone(new DateTimeZone('America/Lima'));
                                        echo $fecha->format('d/m/Y H:i:s');
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                    <strong><?= $evento['usuario_nombre'] ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-info badge-accion">
                                        <?= $evento['modulo'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $accionColors = [
                                        'CREAR' => 'success',
                                        'EDITAR' => 'warning',
                                        'ELIMINAR' => 'danger',
                                        'LOGIN' => 'primary',
                                        'LOGOUT' => 'secondary',
                                        'VER' => 'info'
                                    ];
                                    $color = $accionColors[$evento['accion']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?> badge-accion">
                                        <?= $evento['accion'] ?>
                                    </span>
                                </td>
                                <td>
                                    <code><?= $evento['ip_address'] ?></code>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('auditoria/' . $evento['id']) ?>" 
                                       class="btn btn-sm btn-primary" title="Ver detalles">
                                        <i class="fas fa-eye"></i> Detalles
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if ($pager->getPageCount() > 1): ?>
            <div class="mt-3">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
