<?= $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h4>Asignar IP a Bien</h4>

    <?php if (session()->getFlashdata('mensaje')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('mensaje') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('ip/asignar') ?>" method="post">

        <div class="form-group">
            <label for="ip_id">Seleccionar IP:</label>
            <select class="form-control" name="ip_id" id="ip_id" required>
                <option value="">Seleccione una IP disponible</option>
                <?php foreach ($ips as $ip): ?>
                    <option value="<?= esc($ip['id']) ?>"><?= esc($ip['direccion_ip']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="bien_id">Seleccionar Bien (SBN):</label>
            <select class="form-control" name="bien_id" id="bien_id" required>
                <option value="">Seleccione un bien</option>
                <?php foreach ($bienes as $bien): ?>
                    <option value="<?= esc($bien['id']) ?>">
                        <?= esc($bien['cod_patrimonial']) ?> - <?= esc($bien['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="observaciones">Observaciones:</label>
            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Asignar</button>
        <a href="<?= base_url('ip') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Ya no necesitas ningún script si no usas Select2 -->
<?= $this->endSection(); ?>