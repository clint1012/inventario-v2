<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h2>➕ Nuevo Movimiento</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('movimientos') ?>" method="post">
        <?= csrf_field() ?>

        <!-- Tipo de movimiento -->
        <div class="form-group">
            <label for="tipo_movimiento">Tipo de movimiento</label>
            <select name="tipo_movimiento" id="tipo_movimiento" class="form-control" required>
                <option value="asignacion">Asignación</option>
                <option value="prestamo">Prestamo</option>
                <option value="retiro">Retiro</option>
                <option value="cambio">Cambio</option>
            </select>
        </div>

        <!-- Fecha -->
        <div class="form-group">
            <label for="fecha_movimiento">Fecha</label>
            <input type="datetime-local" name="fecha_movimiento" id="fecha_movimiento" class="form-control"
                value="<?= date('Y-m-d\TH:i') ?>" required>
        </div>

        <!-- Persona -->
        <div class="form-group">
            <label for="id_personas">Persona</label>
            <select name="id_personas" id="id_personas" class="form-control" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($personas as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= $p['nombre'] ?>     <?= $p['ape_paterno'] ?>     <?= $p['ape_materno'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Departamento -->
        <div class="form-group">
            <label for="id_departamentos">Departamento</label>
            <select name="id_departamentos" id="id_departamentos" class="form-control" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($departamentos as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= $d['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Local -->
        <div class="form-group">
            <label for="id_locales">Local</label>
            <select name="id_locales" id="id_locales" class="form-control" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($locales as $l): ?>
                    <option value="<?= $l['id'] ?>"><?= $l['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 📌 Contenedor para Asignación -->
        <div id="contenedor_asignar" class="form-group">
            <label for="buscador_asignar">Buscar bienes para asignar</label>
            <select id="buscador_asignar" class="form-control"></select>
            <ul id="lista_asignar" class="list-group mt-2"></ul>
        </div>

        <!-- 📌 Contenedor para prestamo -->
        <div id="contenedor_prestar" class="form-group">
            <label for="buscador_prestar">Buscar bienes para prestar</label>
            <select id="buscador_prestar" class="form-control"></select>
            <ul id="lista_prestar" class="list-group mt-2"></ul>
        </div>

        <!-- 📌 Contenedor para Retiro -->
        <div id="contenedor_retirar" class="form-group">
            <label for="buscador_retirar">Buscar bienes para retirar</label>
            <select id="buscador_retirar" class="form-control"></select>
            <ul id="lista_retirar" class="list-group mt-2"></ul>
        </div>

        <!-- Observaciones -->
        <div class="form-group mt-3">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="<?= base_url('movimientos') ?>" class="btn btn-secondary">Volver</a>
    </form>
</div>

<?= $this->endSection(); ?>