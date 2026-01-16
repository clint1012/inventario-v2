<?= $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container-fluid mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">👤 Detalles de la Persona</h4>
            <div>
                <a href="<?= base_url('auditoria/historial/Personas/' . $persona['id']) ?>" class="btn btn-info btn-sm" title="Ver historial de cambios">
                    <i class="fas fa-history"></i> Historial
                </a>
                <a href="<?= base_url('personas') ?>" class="btn btn-light btn-sm">← Volver</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">DNI:</h6>
                    <p class="font-weight-bold"><?= esc($persona['dni']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Nombre Completo:</h6>
                    <p class="font-weight-bold">
                        <?= esc($persona['nombre']) . ' ' . esc($persona['ape_paterno']) . ' ' . esc($persona['ape_materno']) ?>
                    </p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Régimen Laboral:</h6>
                    <p class="font-weight-bold"><?= esc($persona['regimen_laboral']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Fecha Inicio Contrato:</h6>
                    <p class="font-weight-bold"><?= esc($persona['fecha_inicio']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Fecha Fin Contrato:</h6>
                    <p class="font-weight-bold"><?= esc($persona['fecha_fin']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Correo:</h6>
                    <p class="font-weight-bold"><?= esc($persona['correo']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Teléfono:</h6>
                    <p class="font-weight-bold"><?= esc($persona['telefono']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Dirección Domiciliaria:</h6>
                    <p class="font-weight-bold"><?= esc($persona['direccion_domiciliaria']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Modalidad:</h6>
                    <p class="font-weight-bold text-capitalize"><?= esc($persona['modalidad']) ?></p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Local:</h6>
                    <p class="font-weight-bold"><?= esc($persona['nombre_local']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>