<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h2 class="my-3">Editar Asignación</h2>

<?php if (session()->getFlashdata('error') !== null) { ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>
<?php } ?>

<form action="<?= base_url('inventario2025/' . $inventario['id']); ?>" class="row g-3" method="post" autocomplete="off">
    <input type="hidden" name="_method" value="PUT">

    <div class="col-md-6">
        <label for="id_personas" class="form-label">Asignar a usuario:</label>
        <select class="form-select form-control" id="id_personas" name="id_personas" required>
            <option value="">Seleccione un usuario</option>
            <?php foreach ($personas as $persona): ?>
                <option value="<?= $persona['id']; ?>" <?= $persona['id'] == $inventario['id_personas'] ? 'selected' : ''; ?>><?= $persona['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="id_departamentos" class="form-label">Departamento</label>
        <select class="form-select form-control" id="id_departamentos" name="id_departamentos" required>
            <option value="">Seleccionar</option>
            <?php foreach ($departamentos as $departamento) : ?>
                <option value="<?= $departamento['id']; ?>" <?= $departamento['id'] == $inventario['id_departamentos'] ? 'selected' : ''; ?>><?= $departamento['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="id_locales" class="form-label">Asignar una sede:</label>
        <select class="form-select form-control" id="id_locales" name="id_locales" required>
            <option value="">Seleccione una sede</option>
            <?php foreach ($locales as $local): ?>
                <option value="<?= $local['id']; ?>" <?= $local['id'] == $inventario['id_locales'] ? 'selected' : ''; ?>><?= $local['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-12 mt-4 mb-3">
        <h4>Asignar</h4>
    </div>

    <?php
    $equipos = [
        "pc_escritorio" => "PC Escritorio",
        "teclado" => "Teclado",
        "monitor" => "Monitor",
        "impresora" => "Impresora",
        "scanner" => "Scanner",
        "otro" => "Otro"
    ];

    foreach ($equipos as $campo => $label): ?>
        <div class="col-md-4">
            <label for="<?= $campo ?>" class="form-label"><?= $label ?></label>
            <input type="text" class="form-control sbn-input" id="<?= $campo ?>" name="<?= $campo ?>" placeholder="<?= strtoupper($label) ?>" value="<?= isset($inventario[$campo]) ? $inventario[$campo] : ''; ?>">
            <input type="text" class="form-control" id="<?= $campo ?>_desc" name="<?= $campo ?>_desc" readonly value="<?= isset($inventario[$campo . '_desc']) ? $inventario[$campo . '_desc'] : ''; ?>">
        </div>
    <?php endforeach; ?>

    <div class="col-12 mt-4">
        <a href="<?= base_url('inventario2025') ?>" class="btn btn-secondary">Regresar</a>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>

</form>

<!-- AJAX para obtener la descripción de los bienes -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(".sbn-input").on("change", function() {
            let inputId = $(this).attr("id");
            let codPatrimonial = $(this).val();

            if (codPatrimonial !== "") {
                $.ajax({
                    url: "<?= base_url('inventario2025/getBienDescripcion') ?>",
                    type: "POST",
                    data: {
                        codPatrimonial: codPatrimonial
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.descripcion) {
                            $("#" + inputId + "_desc").val(response.descripcion);
                        } else {
                            $("#" + inputId + "_desc").val("No encontrado");
                        }
                    },
                    error: function() {
                        $("#" + inputId + "_desc").val("Error en la consulta");
                    }
                });
            } else {
                $("#" + inputId + "_desc").val("");
            }
        });

        // Pre-cargar las descripciones si ya existen en el formulario
        $(".sbn-input").each(function() {
            let codPatrimonial = $(this).val();
            let inputId = $(this).attr("id");

            if (codPatrimonial !== "") {
                $.ajax({
                    url: "<?= base_url('inventario2025/getBienDescripcion') ?>",
                    type: "POST",
                    data: {
                        codPatrimonial: codPatrimonial
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.descripcion) {
                            $("#" + inputId + "_desc").val(response.descripcion);
                        } else {
                            $("#" + inputId + "_desc").val("No encontrado");
                        }
                    },
                    error: function() {
                        $("#" + inputId + "_desc").val("Error en la consulta");
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection(); ?>