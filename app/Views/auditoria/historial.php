<?= $this->extend('plantilla') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history text-primary"></i> Historial de Cambios
        </h1>
        <a href="<?= base_url('auditoria') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver a Auditoría
        </a>
    </div>

    <!-- Información del Registro -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle"></i> Información del Registro
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Módulo:</strong> <span class="badge badge-primary"><?= esc($modulo) ?></span></p>
                </div>
                <div class="col-md-4">
                    <p><strong>ID del Registro:</strong> <span class="badge badge-info"><?= esc($registro_id) ?></span></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total de Eventos:</strong> <span class="badge badge-success"><?= count($historial) ?></span></p>
                </div>
            </div>

            <?php if ($registro): ?>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary">Datos Actuales:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <?php foreach ($registro as $campo => $valor): ?>
                                    <?php if (!in_array($campo, ['created_at', 'updated_at', 'deleted_at'])): ?>
                                        <tr>
                                            <td class="font-weight-bold" style="width: 200px;"><?= esc(ucfirst(str_replace('_', ' ', $campo))) ?></td>
                                            <td><?= esc($valor ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Timeline de Cambios -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-stream"></i> Línea de Tiempo de Eventos
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($historial)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <p class="mb-0">No hay eventos registrados para este elemento.</p>
                </div>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($historial as $index => $evento): 
                        $iconMap = [
                            'CREAR' => ['icon' => 'fa-plus-circle', 'color' => 'success'],
                            'EDITAR' => ['icon' => 'fa-edit', 'color' => 'warning'],
                            'ELIMINAR' => ['icon' => 'fa-trash', 'color' => 'danger'],
                            'ACTIVAR' => ['icon' => 'fa-check-circle', 'color' => 'success'],
                            'DESACTIVAR' => ['icon' => 'fa-times-circle', 'color' => 'danger'],
                            'RECUPERAR' => ['icon' => 'fa-undo', 'color' => 'info'],
                            'ASIGNAR' => ['icon' => 'fa-user-plus', 'color' => 'primary'],
                            'LIBERAR' => ['icon' => 'fa-user-minus', 'color' => 'secondary'],
                        ];

                        $accion = strtoupper($evento['accion']);
                        $iconData = $iconMap[$accion] ?? ['icon' => 'fa-circle', 'color' => 'secondary'];
                    ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?= $iconData['color'] ?>">
                                <i class="fas <?= $iconData['icon'] ?> text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">
                                                    <span class="badge badge-<?= $iconData['color'] ?>"><?= esc($evento['accion']) ?></span>
                                                    <small class="text-muted ml-2">
                                                        <i class="fas fa-clock"></i>
                                                        <?= date('d/m/Y H:i:s', strtotime($evento['created_at'])) ?>
                                                    </small>
                                                </h6>
                                                <p class="mb-2">
                                                    <i class="fas fa-user text-primary"></i>
                                                    <strong><?= esc($evento['usuario_nombre']) ?></strong>
                                                </p>
                                                <p class="mb-0 text-muted">
                                                    <i class="fas fa-network-wired"></i>
                                                    <?= esc($evento['ip_address']) ?>
                                                </p>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <?php if (!empty($evento['detalles'])): ?>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            type="button" 
                                                            data-toggle="collapse" 
                                                            data-target="#detalles-<?= $evento['id'] ?>">
                                                        <i class="fas fa-eye"></i> Ver Detalles
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if (!empty($evento['detalles'])): ?>
                                            <div class="collapse mt-3" id="detalles-<?= $evento['id'] ?>">
                                                <hr>
                                                <h6 class="text-primary"><i class="fas fa-info-circle"></i> Detalles:</h6>
                                                <?php 
                                                    $detalles = json_decode($evento['detalles'], true);
                                                    if (json_last_error() === JSON_ERROR_NONE && is_array($detalles)):
                                                ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <?php foreach ($detalles as $key => $value): ?>
                                                                <tr>
                                                                    <td class="font-weight-bold" style="width: 200px;">
                                                                        <?= esc(ucfirst(str_replace('_', ' ', $key))) ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if (is_array($value)): ?>
                                                                            <pre class="mb-0"><?= json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                                                        <?php else: ?>
                                                                            <?= esc($value) ?>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </table>
                                                    </div>
                                                <?php else: ?>
                                                    <pre class="bg-light p-3 rounded"><?= esc($evento['detalles']) ?></pre>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e3e6f0;
    }

    .timeline-item {
        position: relative;
        padding-left: 70px;
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: 15px;
        top: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        z-index: 1;
    }

    .timeline-content {
        margin-bottom: 30px;
    }

    pre {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        font-size: 12px;
        max-height: 300px;
        overflow: auto;
    }
</style>
<?= $this->endSection() ?>
