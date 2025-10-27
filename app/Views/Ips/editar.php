<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h2>Editar Asignación de IP: **<?= esc($ip['direccion_ip']) ?>**</h2>
<p>Modifique los datos de asignación a Persona, Área, Piso y Bien Patrimonial.</p>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<form action="<?= base_url('ip/actualizar/' . $ip['id']) ?>" method="post">

    <div class="row">
        
        <div class="col-md-4 form-group">
            <label for="id_persona">Persona (Nombre)</label>
            <select name="id_persona" id="id_persona" class="form-control">
                <option value="">-- No Asignada --</option>
                <?php foreach ($personas as $persona): ?>
                    <option value="<?= $persona['id'] ?>" 
                        <?= old('id_persona', $ip['id_persona']) == $persona['id'] ? 'selected' : '' ?>>
                        <?= esc($persona['nombre_completo']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        
        <div class="col-md-4 form-group">
            <label for="id_departamento">Área (Departamento)</label>
            <select name="id_departamento" id="id_departamento" class="form-control">
                <option value="">-- No Asignada --</option>
                <?php foreach ($departamentos as $depto): ?>
                    <option value="<?= $depto['id'] ?>" 
                        <?= old('id_departamento', $ip['id_departamento']) == $depto['id'] ? 'selected' : '' ?>>
                        <?= esc($depto['nombre']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        
        <div class="col-md-4 form-group">
            <label for="piso">Piso</label>
            <input type="text" name="piso" id="piso" class="form-control" 
                   value="<?= old('piso', $ip['piso']) ?>" placeholder="Ej: Piso 3">
        </div>
    </div>

    <div class="form-group">
        <label for="bien_id">Bien Patrimonial (Cód. y Descripción)</label>
        <select name="bien_id" id="bien_id" class="form-control">
            <option value="">-- No Asignado --</option>
            <?php foreach ($bienes as $bien): ?>
                <option value="<?= $bien['id'] ?>" 
                    <?= old('bien_id', $ip['bien_id']) == $bien['id'] ? 'selected' : '' ?>>
                    <?= esc($bien['cod_patrimonial']) . ' - ' . esc($bien['descripcion']) ?>
                </option>
            <?php endforeach ?>
        </select>
        <small class="form-text text-muted">Vincular un activo (computadora, impresora, etc.) a esta dirección IP.</small>
    </div>

    <div class="form-group">
        <label for="observaciones">Observaciones</label>
        <textarea name="observaciones" id="observaciones" class="form-control"><?= old('observaciones', $ip['observaciones']) ?></textarea>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="<?= base_url('ip') ?>" class="btn btn-secondary">Cancelar</a>
    </div>

</form>

<?= $this->endSection(); ?>