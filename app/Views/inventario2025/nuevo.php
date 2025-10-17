<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h2 class="my-3">Asignar nuevo</h2>

<?php if (session()->getFlashdata('error') !== null) { ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>
<?php } ?>

<form action="<?= base_url('inventario2025'); ?>" class="row g-3" method="post" autocomplete="off">

    <div class="col-md-6">
        <label for="usuario" class="form-label">Asignar a Persona:</label>
        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Escriba un nombre">
        <input type="hidden" id="usuarioId" name="id_personas"> <!-- ID oculto de la persona -->
        <ul id="usuarioSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000;"></ul>
    </div>

    <div class="col-md-6">
        <label for="id_departamentos" class="form-label">Departamento</label>
        <select class="form-select form-control" id="id_departamentos" name="id_departamentos" required>
            <option value="">Seleccionar</option>
            <?php foreach ($departamentos as $departamento) : ?>
                <option value="<?= $departamento['id']; ?>"><?= $departamento['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="id_locales" class="form-label">Asignar una sede:</label>
        <select class="form-select form-control" id="id_locales" name="id_locales" required>
            <option value="">Seleccione una sede</option>
            <?php foreach ($locales as $local): ?>
                <option value="<?= $local['id']; ?>"><?= $local['nombre']; ?></option>
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
            <input type="text" class="form-control sbn-input" id="<?= $campo ?>" name="<?= $campo ?>" placeholder="<?= strtoupper($label) ?>">
            <input type="text" class="form-control" id="<?= $campo ?>_desc" name="<?= $campo ?>_desc" readonly>
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
    });
</script>

<?= $this->endSection(); ?>