<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h2>📱 Registrar Nuevo Celular</h2>
    <a href="<?= base_url('celulares') ?>" class="btn btn-secondary mb-3">← Volver</a>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('celulares/guardar') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>IMEI <span class="text-danger">*</span></label>
                    <input type="text" name="imei" class="form-control" required 
                           placeholder="Ej: 123456789012345">
                    <small class="form-text text-muted">El IMEI debe ser único</small>
                </div>

                <div class="form-group">
                    <label>Número de Serie</label>
                    <input type="text" name="numero_serie" class="form-control" 
                           placeholder="Ej: SN123456">
                </div>

                <div class="form-group">
                    <label>Modelo <span class="text-danger">*</span></label>
                    <input type="text" name="modelo" class="form-control" required 
                           placeholder="Ej: Samsung Galaxy A52">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" 
                              placeholder="Características adicionales del equipo"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Guardar Celular</button>
                <a href="<?= base_url('celulares') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
