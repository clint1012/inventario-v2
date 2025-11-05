<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3">Editar bien</h3>



<?php if (session()->getFlashdata('error') !== null) { ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>

<?php } ?>



<form action="<?= base_url('bienes/' . $bien['id']); ?>" class="row g-3" method="post" autocomplete="off"
    onsubmit="return confirmarEnvio()">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="bien_id" value="<?= $bien['id']; ?>">

    <div class="col-md-4">
        <label for="cod_patrimonial" class="form-label">Cod_patrimonial</label>
        <input type="text" class="form-control" id="cod_patrimonial" name="cod_patrimonial"
            value="<?= $bien['cod_patrimonial'] ?>" required autofocus>
    </div>

    <div class="col-md-8">
        <label for="descripcion" class="form-label">Descripcion</label>
        <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?= $bien['descripcion'] ?>"
            required>
    </div>

    <!--Inicio select marca-->
    <div class="col-md-6">
        <label for="marca" class="form-label">Marca</label>
        <select class="form-select form-control" id="marca" name="marca" required onchange="toggleOtraMarca(this)">
            <option value="">Seleccionar</option>
            <option value="hp" <?= $bien['marca'] === 'hp' ? 'selected' : '' ?>>HP</option>
            <option value="lenovo" <?= $bien['marca'] === 'lenovo' ? 'selected' : '' ?>>Lenovo</option>
            <option value="dell" <?= $bien['marca'] === 'dell' ? 'selected' : '' ?>>Dell</option>
            <option value="microsoft" <?= $bien['marca'] === 'microsoft' ? 'selected' : '' ?>>Microsoft</option>
            <option value="otro" <?= $bien['marca'] === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>
    </div>
    <div class="col-md-6" id="otraMarcaDiv" style="display: <?= $bien['marca'] === 'otro' ? 'block' : 'none'; ?>">
        <label for="otraMarca" class="form-label">Especifique otra marca</label>
        <input type="text" class="form-control" id="otraMarca" name="otraMarca" value="<?= $bien['marca'] ?>">
    </div>
    <!--Fin select marca-->

    <div class="col-md-6">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" class="form-control" id="modelo" name="modelo" value="<?= $bien['modelo'] ?>" required>
    </div>

    <div class="col-md-6">
        <label for="serie" class="form-label">Serie</label>
        <input type="text" class="form-control" id="serie" name="serie" value="<?= $bien['serie'] ?>">
    </div>

    <!--Inicio select procesador-->
    <div class="col-md-6">
        <label for="procesador" class="form-label">Procesador</label>
        <select class="form-select form-control" id="procesador" name="procesador" required
            onchange="toggleOtroProcesador(this)">
            <option value="">Seleccionar</option>
            <option value="core_i3" <?= $bien['procesador'] === 'core_i3' ? 'selected' : '' ?>>Core i3</option>
            <option value="core i5-7ma" <?= $bien['procesador'] === 'core i5-7ma' ? 'selected' : '' ?>>core i5-7ma</option>
            <option value="core i5-9na" <?= $bien['procesador'] === 'core i5-9na' ? 'selected' : '' ?>>core i5-9na</option>
            <option value="core i7-7ma" <?= $bien['procesador'] === 'core i7-7ma' ? 'selected' : '' ?>>core i7-7ma</option>
            <option value="core i7-9na" <?= $bien['procesador'] === 'core i7-9na' ? 'selected' : '' ?>>core i7-9na</option>
            <option value="core i7-10ma" <?= $bien['procesador'] === 'core i7-10ma' ? 'selected' : '' ?>>core i7-10ma
            </option>
            <option value="core i7-11va" <?= $bien['procesador'] === 'core i7-11va' ? 'selected' : '' ?>>core i7-11va
            </option>
            <option value="core i7-ultra" <?= $bien['procesador'] === 'core i7-ultra' ? 'selected' : '' ?>>core i7-ultra
            </option>
            <option value="NO_APLICA" <?= $bien['procesador'] === 'NO_APLICA' ? 'selected' : '' ?>>No Aplica</option>
            <option value="otro" <?= $bien['procesador'] === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>
    </div>
    <div class="col-md-6" id="otroProcesadorDiv"
        style="display: <?= $bien['procesador'] === 'otro' ? 'block' : 'none'; ?>">
        <label for="otroProcesador" class="form-label">Especifique otro procesador</label>
        <input type="text" class="form-control" id="otroProcesador" name="otroProcesador"
            value="<?= $bien['procesador'] ?>">
    </div>
    <!--Fin select procesador-->

    <!--Inicio select memoria-->
    <div class="col-md-6">
        <label for="memoria" class="form-label">Memoria</label>
        <select class="form-select form-control" id="memoria" name="memoria" required
            onchange="toggleOtraMemoria(this)">
            <option value="">Seleccionar</option>
            <option value="4GB" <?= $bien['memoria'] === '4GB' ? 'selected' : '' ?>>4GB</option>
            <option value="8GB" <?= $bien['memoria'] === '8GB' ? 'selected' : '' ?>>8GB</option>
            <option value="16GB" <?= $bien['memoria'] === '16GB' ? 'selected' : '' ?>>16GB</option>
            <option value="NO_APLICA" <?= $bien['memoria'] === 'NO_APLICA' ? 'selected' : '' ?>>No Aplica</option>
            <option value="otro" <?= $bien['memoria'] === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>
    </div>
    <div class="col-md-6" id="otraMemoriaDiv" style="display: <?= $bien['memoria'] === 'otro' ? 'block' : 'none'; ?>">
        <label for="otraMemoria" class="form-label">Especifique otra memoria</label>
        <input type="text" class="form-control" id="otraMemoria" name="otraMemoria" value="<?= $bien['memoria'] ?>">
    </div>
    <!--Fin select memoria-->

    <!--Inicio select sistema_operativo-->
    <div class="col-md-6">
        <label for="sistema_operativo" class="form-label">Sistema Operativo</label>
        <select class="form-select form-control" id="sistema_operativo" name="sistema_operativo" required
            onchange="toggleOtroSO(this)">
            <option value="">Seleccionar</option>
            <option value="windows 10" <?= $bien['sistema_operativo'] === 'windows 10' ? 'selected' : '' ?>>Windows 10
            </option>
            <option value="windows 11" <?= $bien['sistema_operativo'] === 'windows 11' ? 'selected' : '' ?>>Windows 11
            </option>
            <option value="linux" <?= $bien['sistema_operativo'] === 'linux' ? 'selected' : '' ?>>Linux</option>
            <option value="macos" <?= $bien['sistema_operativo'] === 'macos' ? 'selected' : '' ?>>MacOS</option>
            <option value="NO_APLICA" <?= old('sistema_operativo') === 'NO_APLICA' || $bien['sistema_operativo'] === 'NO_APLICA' ? 'selected' : '' ?>>No Aplica</option>
            <option value="otro" <?= $bien['sistema_operativo'] === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>
    </div>
    <div class="col-md-6" id="otroSODiv"
        style="display: <?= $bien['sistema_operativo'] === 'otro' ? 'block' : 'none'; ?>;">
        <label for="otroSO" class="form-label">Especifique otro sistema operativo</label>
        <input type="text" class="form-control" id="otroSO" name="otroSO"
            value="<?= old('otroSO') ?: ($bien['otroSO'] ?? '') ?>">

    </div>

    <!--Fin select sistema_operativo-->

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select class="form-select form-control" id="estado" name="estado" required>
            <option value="">Seleccionar</option>
            <option value="bueno" <?= $bien['estado'] === 'bueno' ? 'selected' : '' ?>>Bueno</option>
            <option value="regular" <?= $bien['estado'] === 'regular' ? 'selected' : '' ?>>Regular</option>
            <option value="malo" <?= $bien['estado'] === 'malo' ? 'selected' : '' ?>>Malo</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="fecha_adquisicion" class="form-label">Fecha_adquisicion</label>
        <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion"
            value="<?= $bien['fecha_adquisicion'] ?>">
    </div>

    <div class="col-md-6">
        <label for="años_garantia" class="form-label">Años Garantia</label>
        <input type="text" class="form-control" id="años_garantia" name="años_garantia"
            value="<?= $bien['años_garantia'] ?>">
    </div>

    <div class="col-md-6">
        <label for="proveedor" class="form-label">Proveedor</label>
        <input type="text" class="form-control" id="proveedor" name="proveedor" value="<?= $bien['proveedor'] ?>">
    </div>

    <div class="col-md-6">
        <label for="usuario" class="form-label">Asignar a Persona:</label>
        <input type="text" class="form-control" id="usuario" name="usuario"
            value="<?= isset($persona_nombre) ? $persona_nombre : '' ?>" placeholder="Escriba un nombre">
        <input type="hidden" id="usuarioId" name="id_personas"
            value="<?= isset($bien['id_personas']) ? $bien['id_personas'] : '' ?>">
        <!-- ID oculto de la persona -->
        <ul id="usuarioSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000;"></ul>
    </div>

    <div class="col-md-6">
        <label for="departamento" class="form-label">Departamento</label>
        <select class="form-select form-control" id="departamento" name="departamento" required>
            <option value="">Seleccionar</option>
            <?php foreach ($departamentos as $departamento): ?>
                <option value="<?= $departamento['id']; ?>" <?php echo ($departamento['id'] == $bien['id_departamento']) ? 'selected' : ''; ?>><?= $departamento['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="id_locales" class="form-label">Asignar una sede</label>
        <select class="form-select form-control" id="id_locales" name="id_locales" required>
            <option value="">Seleccionar</option>
            <?php foreach ($locales as $local): ?>
                <option value="<?= $local['id']; ?>" <?php echo ($local['id'] == $bien['id_locales']) ? 'selected' : ''; ?>>
                    <?= $local['nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div><br><br><br>
    <div class="col-12" style="padding-top: 10px;">
        <a href="#" onclick="abrirModalMantenimiento(<?= $bien['id'] ?>)" class="btn btn-secondary"> Solicitar
            mantenimiento</a>
        <a href="<?= base_url('bienes') ?>" class="btn btn-secondary">Regresar</a>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>


<!-- Modal para dar mantenimiento a un bien -->
<div class="modal fade" id="modalMantenimiento" tabindex="-1" role="dialog" aria-labelledby="modalMantenimientoLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMantenimientoLabel">Mantenimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formMantenimiento" action="<?= base_url('bienes/getMantenimiento') ?>" method="post"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="bien_id" name="bien_id">
                    <div class="form-group">
                        <label for="motivo_mantenimiento">Motivo del Mantenimiento</label>
                        <textarea class="form-control" id="motivo_mantenimiento" name="motivo_mantenimiento"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tipo_mantenimiento">Tipo de mantenimiento</label>
                        <select class="form-control" id="tipo_mantenimiento" name="tipo_mantenimiento" required>
                            <option value="">Seleccione una opción</option>
                            <option value="Preventivo" <?= session('tipo_mantenimiento') === 'Preventivo' ? 'selected' : '' ?>>Preventivo</option>
                            <option value="Correctivo" <?= session('tipo_mantenimiento') === 'Correctivo' ? 'selected' : '' ?>>Correctivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usuario_mantenimiento">Usuario que autoriza el mantenimiento</label>
                        <input type="text" class="form-control" id="usuario_mantenimiento" name="usuario_mantenimiento"
                            value="<?= session('usuario') ?>" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"> Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Baja</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>


    function toggleOtraMarca(selectElement) {
        const otraMarcaDiv = document.getElementById('otraMarcaDiv');
        if (selectElement.value === 'otro') {
            otraMarcaDiv.style.display = 'block';
        } else {
            otraMarcaDiv.style.display = 'none';
            document.getElementById('otraMarca').value = ''; // Limpia el campo
        }
    }

    function toggleOtroProcesador(selectElement) {
        const otroProcesadorDiv = document.getElementById('otroProcesadorDiv');
        if (selectElement.value === 'otro') {
            otroProcesadorDiv.style.display = 'block';
        } else {
            otroProcesadorDiv.style.display = 'none';
            document.getElementById('otroProcesador').value = ''; // Limpia el campo si no es "Otro"
        }
    }

    function toggleOtraMemoria(selectElement) {
        const otraMemoriaDiv = document.getElementById('otraMemoriaDiv');
        if (selectElement.value === 'otro') {
            otraMemoriaDiv.style.display = 'block';
        } else {
            otraMemoriaDiv.style.display = 'none';
            document.getElementById('otraMemoria').value = ''; // Limpia el campo si no es "Otro"
        }
    }

    function toggleOtroSO(select) {
        const otroSODiv = document.getElementById('otroSODiv');
        if (select.value === 'otro') {
            otroSODiv.style.display = 'block';
        } else {
            otroSODiv.style.display = 'none';
            document.getElementById('otroSO').value = ''; // Limpiar el campo
        }
    }

    function confirmarEnvio() {
        return confirm("¿Estás seguro de que deseas guardar los cambios?");
    }

    function abrirModalMantenimiento(id) {
        document.getElementById('bien_id').value = id; // Pasar el ID al modal 
        $('#modalMantenimiento').modal('show'); // Mostrar el modal 
    } 
</script>

<?= $this->endSection(); ?>