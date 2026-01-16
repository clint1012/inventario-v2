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
    .badge-status {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    .card-header h5 {
        font-size: 1rem;
        margin-bottom: 0;
    }
    .card-body {
        padding: 1rem;
    }
    .mb-3 {
        margin-bottom: 0.75rem !important;
    }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1"><i class="fas fa-eye text-primary"></i> Detalle del Bien</h4>
        <p class="text-muted mb-0 small">Código: <strong><?= $bien['cod_patrimonial'] ?></strong></p>
    </div>
    <div>
        <a href="<?= base_url('auditoria/historial/Bienes/' . $bien['id']) ?>" class="btn btn-sm btn-info" title="Ver historial de cambios">
            <i class="fas fa-history"></i> Ver Historial
        </a>
        <a href="<?= base_url('bienes/' . $bien['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="<?= base_url('bienes') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
    </div>
</div>

<!-- Mensajes Flash -->
<?php if (session()->getFlashdata('error') !== null): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row g-3">
    <!-- Columna Izquierda -->
    <div class="col-lg-6">
        <!-- Información Básica -->
        <div class="card shadow-sm detail-card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información Básica</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">Código Patrimonial</div>
                    <div class="detail-value">
                        <span class="badge bg-dark text-white" style="font-size: 1rem; padding: 0.5rem 1rem; font-family: monospace;">
                            <?= $bien['cod_patrimonial'] ?>
                        </span>
                    </div>
                </div>

                <?php if (isset($bien['tipo_bien'])): ?>
                <div class="mb-3">
                    <div class="detail-label">Tipo de Bien</div>
                    <div class="detail-value">
                        <?php
                        $iconos = [
                            'computadora' => '💻', 'laptop' => '💻', 'all_in_one' => '🖥️', 'monitor' => '🖥️',
                            'teclado' => '⌨️', 'mouse' => '🖱️', 'impresora' => '🖨️',
                            'scanner' => '📠', 'multifuncional' => '🖨️', 'switch' => '🔌',
                            'router' => '📡', 'access_point' => '📶', 'camara' => '📹',
                            'proyector' => '📽️', 'servidor' => '🖥️', 'nas' => '💾',
                            'ups' => '🔋', 'rack' => '🗄️', 'tablet' => '📱', 'otro' => '📦'
                        ];
                        $tipo = $bien['tipo_bien'] ?? 'otro';
                        echo ($iconos[$tipo] ?? '📦') . ' ' . ucfirst(str_replace('_', ' ', $tipo));
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <div class="detail-label">Descripción</div>
                    <div class="detail-value"><?= $bien['descripcion'] ?></div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Estado</div>
                    <div class="detail-value">
                        <?php
                        $estadoBadges = [
                            'asignado' => 'success', 'activo' => 'primary',
                            'mantenimiento' => 'warning', 'retirado' => 'danger',
                            'nuevo' => 'info', 'bueno' => 'success',
                            'regular' => 'warning', 'malo' => 'danger'
                        ];
                        $badgeClass = $estadoBadges[$bien['estado']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $badgeClass ?> badge-status">
                            <?= ucfirst($bien['estado']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Especificaciones Técnicas -->
        <div class="card shadow-sm detail-card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-microchip"></i> Especificaciones Técnicas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Marca</div>
                        <div class="detail-value"><?= $bien['marca'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Modelo</div>
                        <div class="detail-value"><?= $bien['modelo'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="detail-label">Serie</div>
                        <div class="detail-value"><code><?= $bien['serie'] ?: '-' ?></code></div>
                    </div>
                    <div class="col-md-6 mb-3" id="campo_procesador">
                        <div class="detail-label">Procesador</div>
                        <div class="detail-value"><?= $bien['procesador'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3" id="campo_memoria">
                        <div class="detail-label">Memoria RAM</div>
                        <div class="detail-value"><?= $bien['memoria'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3" id="campo_tipo_disco">
                        <div class="detail-label">Tipo de Disco</div>
                        <div class="detail-value"><?= $bien['tipo_disco'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3" id="campo_espacio_disco">
                        <div class="detail-label">Espacio de Disco</div>
                        <div class="detail-value"><?= $bien['espacio_disco'] ? $bien['espacio_disco'] . ' GB' : '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3" id="campo_sistema_operativo">
                        <div class="detail-label">Sistema Operativo</div>
                        <div class="detail-value"><?= $bien['sistema_operativo'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3" id="campo_ver_office">
                        <div class="detail-label">Versión Office</div>
                        <div class="detail-value"><?= ($bien['ver_office'] ?? $bien['office'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-12 mb-3" id="campo_Ip">
                        <div class="detail-label">Dirección IP</div>
                        <div class="detail-value"><code><?= $bien['Ip'] ?: '-' ?></code></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Derecha -->
    <div class="col-lg-6">
        <!-- Ubicación y Asignación -->
        <div class="card shadow-sm detail-card mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Ubicación y Asignación</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">Departamento</div>
                    <div class="detail-value">
                        <i class="fas fa-sitemap text-primary"></i>
                        <?php
                        foreach ($departamentos as $departamento) {
                            if ($departamento['id'] == $bien['id_departamento']) {
                                echo $departamento['nombre'];
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Local/Sede</div>
                    <div class="detail-value">
                        <i class="fas fa-building text-primary"></i>
                        <?= $bien['local_nombre'] ?? '-' ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Persona Asignada</div>
                    <div class="detail-value">
                        <?php if (isset($bien['persona_nombre']) && $bien['persona_nombre']): ?>
                            <i class="fas fa-user text-success"></i>
                            <strong><?= $bien['persona_nombre'] ?></strong>
                        <?php else: ?>
                            <span class="text-muted">Sin asignar</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Compra -->
        <div class="card shadow-sm detail-card mb-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Información de Compra</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">Proveedor</div>
                    <div class="detail-value"><?= $bien['proveedor_nombre'] ?? '-' ?></div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Fecha de Adquisición</div>
                    <div class="detail-value">
                        <i class="fas fa-calendar text-primary"></i>
                        <?= $bien['fecha_adquisicion'] ? date('d/m/Y', strtotime($bien['fecha_adquisicion'])) : '-' ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">N° Documento de Compra</div>
                    <div class="detail-value"><?= $bien['num_doc_compra'] ?: '-' ?></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Años de Garantía</div>
                        <div class="detail-value"><?= $bien['años_garantia'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-label">Estado de Garantía</div>
                        <div class="detail-value">
                            <?php if ($bien['estado_garantia'] == 'en garantía'): ?>
                                <span class="badge bg-success">✓ En Garantía</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Caducada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="card shadow-sm detail-card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-info"></i> Información Adicional</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">ID del Bien</div>
                    <div class="detail-value"><span class="badge bg-primary">#<?= $bien['id'] ?></span></div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Fecha de Registro</div>
                    <div class="detail-value">
                        <?= isset($bien['created_at']) ? date('d/m/Y H:i', strtotime($bien['created_at'])) : '-' ?>
                    </div>
                </div>

                <?php if (isset($bien['updated_at'])): ?>
                <div class="mb-3">
                    <div class="detail-label">Última Actualización</div>
                    <div class="detail-value">
                        <?= date('d/m/Y H:i', strtotime($bien['updated_at'])) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Botones Inferiores -->
<div class="d-flex justify-content-between mt-3 mb-3">
    <a href="<?= base_url('bienes') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
    <a href="<?= base_url('bienes/' . $bien['id'] . '/edit') ?>" class="btn btn-warning">
        <i class="fas fa-edit"></i> Editar este Bien
    </a>
</div>

<script>
    // Ocultar campos que tienen el valor "NO APLICA"
    document.addEventListener('DOMContentLoaded', function() {
        const camposTecnicos = [
            'procesador', 'memoria', 'tipo_disco', 'espacio_disco', 
            'sistema_operativo', 'ver_office', 'Ip'
        ];

        camposTecnicos.forEach(campo => {
            const elemento = document.getElementById('campo_' + campo);
            if (elemento) {
                const valor = elemento.querySelector('.detail-value')?.textContent.trim();
                if (valor === 'NO APLICA' || valor === '-' && campo !== 'espacio_disco') {
                    // Ocultar solo si el valor es "NO APLICA" o vacío
                    if (valor === 'NO APLICA') {
                        elemento.style.display = 'none';
                    }
                }
            }
        });
    });
</script>

<?= $this->endSection(); ?>
