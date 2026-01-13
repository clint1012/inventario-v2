<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<style>
    .detail-card {
        border-left: 4px solid #0d6efd;
    }
    .detail-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.15rem;
    }
    .detail-value {
        font-size: 0.875rem;
        color: #212529;
        padding: 0.35rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    .json-viewer {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="fas fa-info-circle text-primary"></i> Detalle del Evento
        </h4>
        <p class="text-muted mb-0 small">ID: <strong>#<?= $evento['id'] ?></strong></p>
    </div>
    <a href="<?= base_url('auditoria') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<div class="row g-3">
    <!-- Información Principal -->
    <div class="col-lg-6">
        <div class="card shadow-sm detail-card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Información del Evento</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">Fecha y Hora</div>
                    <div class="detail-value">
                        <i class="far fa-clock text-primary me-2"></i>
                        <?php
                        $fecha = new DateTime($evento['created_at'], new DateTimeZone('UTC'));
                        $fecha->setTimezone(new DateTimeZone('America/Lima'));
                        echo $fecha->format('d/m/Y H:i:s');
                        ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Usuario</div>
                    <div class="detail-value">
                        <i class="fas fa-user text-success me-2"></i>
                        <strong><?= $evento['usuario_nombre'] ?></strong>
                        <?php if ($evento['usuario_id']): ?>
                            <small class="text-muted">(ID: <?= $evento['usuario_id'] ?>)</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Módulo</div>
                    <div class="detail-value">
                        <span class="badge bg-info" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?= $evento['modulo'] ?>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Acción Realizada</div>
                    <div class="detail-value">
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
                        <span class="badge bg-<?= $color ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?= $evento['accion'] ?>
                        </span>
                    </div>
                </div>

                <?php if ($evento['registro_id']): ?>
                    <div class="mb-3">
                        <div class="detail-label">ID del Registro Afectado</div>
                        <div class="detail-value">
                            <span class="badge bg-dark" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                                #<?= $evento['registro_id'] ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Información Técnica -->
    <div class="col-lg-6">
        <div class="card shadow-sm detail-card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-server me-2"></i>Información Técnica</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">Dirección IP</div>
                    <div class="detail-value">
                        <i class="fas fa-network-wired text-warning me-2"></i>
                        <code style="font-size: 1rem;"><?= $evento['ip_address'] ?></code>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">User Agent</div>
                    <div class="detail-value" style="word-break: break-all;">
                        <i class="fas fa-desktop text-info me-2"></i>
                        <small><?= $evento['user_agent'] ?></small>
                    </div>
                </div>

                <?php
                // Detectar navegador y SO
                $ua = $evento['user_agent'];
                $browser = 'Desconocido';
                $os = 'Desconocido';

                if (strpos($ua, 'Chrome') !== false) $browser = 'Chrome';
                elseif (strpos($ua, 'Firefox') !== false) $browser = 'Firefox';
                elseif (strpos($ua, 'Safari') !== false) $browser = 'Safari';
                elseif (strpos($ua, 'Edge') !== false) $browser = 'Edge';

                if (strpos($ua, 'Windows') !== false) $os = 'Windows';
                elseif (strpos($ua, 'Mac') !== false) $os = 'macOS';
                elseif (strpos($ua, 'Linux') !== false) $os = 'Linux';
                elseif (strpos($ua, 'Android') !== false) $os = 'Android';
                elseif (strpos($ua, 'iOS') !== false) $os = 'iOS';
                ?>

                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="detail-label">Navegador</div>
                        <div class="detail-value">
                            <i class="fab fa-<?= strtolower($browser) ?> me-2"></i>
                            <?= $browser ?>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="detail-label">Sistema Operativo</div>
                        <div class="detail-value">
                            <?php
                            $osIcons = [
                                'Windows' => 'fa-windows',
                                'macOS' => 'fa-apple',
                                'Linux' => 'fa-linux',
                                'Android' => 'fa-android'
                            ];
                            $icon = $osIcons[$os] ?? 'fa-desktop';
                            ?>
                            <i class="fab <?= $icon ?> me-2"></i>
                            <?= $os ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles Adicionales -->
    <?php if (!empty($evento['detalles'])): ?>
        <div class="col-12">
            <div class="card shadow-sm detail-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>Detalles Adicionales</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($evento['detalles_decoded'])): ?>
                        <!-- JSON Formateado -->
                        <div class="json-viewer">
                            <pre class="mb-0"><code><?= json_encode($evento['detalles_decoded'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></code></pre>
                        </div>
                    <?php else: ?>
                        <!-- Texto plano -->
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <?= nl2br(htmlspecialchars($evento['detalles'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Botón para regresar al final -->
<div class="mt-4 d-flex justify-content-center">
    <a href="<?= base_url('auditoria') ?>" class="btn btn-secondary btn-lg">
        <i class="fas fa-arrow-left me-2"></i>Regresar a la Lista
    </a>
</div>

<?= $this->endSection(); ?>
