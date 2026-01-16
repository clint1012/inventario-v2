<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>📱 Editar Celular</h2>
        <div>
            <a href="<?= base_url('auditoria/historial/Celulares/' . $celular['id']) ?>" class="btn btn-info btn-sm" title="Ver historial de cambios">
                <i class="fas fa-history"></i> Ver Historial
            </a>
            <a href="<?= base_url('celulares') ?>" class="btn btn-secondary btn-sm">← Volver</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('celulares/actualizar/' . $celular['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>IMEI <span class="text-danger">*</span></label>
                    <input type="text" name="imei" class="form-control" required 
                           value="<?= esc($celular['imei']) ?>">
                </div>

                <div class="form-group">
                    <label>Número de Serie</label>
                    <input type="text" name="numero_serie" class="form-control" 
                           value="<?= esc($celular['numero_serie'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Modelo <span class="text-danger">*</span></label>
                    <input type="text" name="modelo" class="form-control" required 
                           value="<?= esc($celular['modelo']) ?>">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"><?= esc($celular['descripcion'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-success">Actualizar Celular</button>
                <a href="<?= base_url('celulares') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
