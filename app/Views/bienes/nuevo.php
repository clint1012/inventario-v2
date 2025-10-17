<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<h3 class="my-3">Nuevo bien</h3>

<?php if (session()->getFlashdata('error') !== null) { ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error'); ?>
    </div>

<?php } ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('bienes') ?>" class="row g-3" method="post" autocomplete="off">

    <div class="col-md-4">
        <label for="cod_patrimonial" class="form-label">Cod_patrimonial</label>
        <input type="text" class="form-control" id="cod_patrimonial" name="cod_patrimonial"
            value="<?= set_value('cod_patrimonial') ?>" required autofocus maxlength="12"
            placeholder="Ingrese 12 caracteres numéricos">
        <div id="cod_patrimonial_error" class="text-danger"></div>
    </div>

    <div class="col-md-8">
        <label for="descripcion" class="form-label">Descripcion</label>
        <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?= set_value('descripcion') ?>" required>
    </div>

    <div class="col-md-6">
        <label for="marca" class="form-label">Marca</label>
        <select class="form-select form-control" id="marca" name="marca" required onchange="toggleOtraMarca(this)">
            <option value="">Seleccionar</option>
            <option value="hp" <?= set_value('marca') === 'hp' ? 'selected' : '' ?>>HP</option>
            <option value="lenovo" <?= set_value('marca') === 'lenovo' ? 'selected' : '' ?>>Lenovo</option>
            <option value="dell" <?= set_value('marca') === 'dell' ? 'selected' : '' ?>>Dell</option>
            <option value="viewsonic" <?= set_value('marca') === 'viewsonic' ? 'selected' : '' ?>>Viewsonic</option>
            <option value="toshiba" <?= set_value('marca') === 'toshiba' ? 'selected' : '' ?>>Toshiba</option>
            <option value="epson" <?= set_value('marca') === 'epson' ? 'selected' : '' ?>>Epson</option>
            <option value="xerox" <?= set_value('marca') === 'xerox' ? 'selected' : '' ?>>Xerox</option>
            <option value="otro" <?= set_value('marca') === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>
    </div>

    <div class="col-md-6" id="otraMarcaDiv" style="display: none;">
        <label for="otraMarca" class="form-label">Especifique otra marca</label>
        <input type="text" class="form-control" id="otraMarca" name="otraMarca" value="<?= set_value('otraMarca') ?>">
    </div>

    <div class="col-md-6">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" class="form-control" id="modelo" name="modelo" value="<?= set_value('modelo') ?>" required>
    </div>

    <div class="col-md-6">
        <label for="serie" class="form-label">Serie</label>
        <input type="text" class="form-control" id="serie" name="serie" value="<?= set_value('serie') ?>">
    </div>

    <div class="col-md-6">
        <label for="procesador" class="form-label">Procesador</label>
        <select class="form-select form-control" id="procesador" name="procesador" required onchange="toggleOtroProcesador()">
            <option value="">Seleccionar</option>
            <option value="core i3" <?= set_value('procesador') === 'core i3' ? 'selected' : '' ?>>Core i3</option>
            <option value="core i5-7ma" <?= set_value('procesador') === 'core i5-7ma' ? 'selected' : '' ?>>Core i5-7ma Gen</option>
            <option value="core i5-9na" <?= set_value('procesador') === 'core i5-9na' ? 'selected' : '' ?>>Core i5-9na Gen</option>
            <option value="core i7-7ma" <?= set_value('procesador') === 'core i7-7ma' ? 'selected' : '' ?>>Core i7-7ma Gen</option>
            <option value="core i7-9na" <?= set_value('procesador') === 'core i7-9na' ? 'selected' : '' ?>>Core i7-9na Gen</option>
            <option value="core i7-10ma" <?= set_value('procesador') === 'core i7-10ma' ? 'selected' : '' ?>>Core i7-10ma Gen</option>
            <option value="core i7-11va" <?= set_value('procesador') === 'core i7-11va' ? 'selected' : '' ?>>Core i7-11va Gen</option>
            <option value="core i7-ultra" <?= set_value('procesador') === 'core i7-utra' ? 'selected' : '' ?>>Core i7-Ultra</option>
            <option value="NO APLICA" <?= set_value('procesador') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
            <option value="otro" <?= set_value('procesador') === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>

        <input type="text" class="form-control mt-2" id="procesador_otro" name="procesador_otro"
            value="<?= set_value('procesador_otro') ?>"
            placeholder="Especifique otro procesador"
            style="display: none;">
    </div>

    <div class="col-md-6">
        <label for="memoria" class="form-label">Memoria</label>
        <select class="form-select form-control" id="memoria" name="memoria" required onchange="toggleOtroInput()">
            <option value="">Seleccionar</option>
            <option value="4gb" <?= set_value('memoria') === '4gb' ? 'selected' : '' ?>>4GB</option>
            <option value="6gb" <?= set_value('memoria') === '6gb' ? 'selected' : '' ?>>6GB</option>
            <option value="8gb" <?= set_value('memoria') === '8gb' ? 'selected' : '' ?>>8GB</option>
            <option value="10gb" <?= set_value('memoria') === '10gb' ? 'selected' : '' ?>>10GB</option>
            <option value="12gb" <?= set_value('memoria') === '12gb' ? 'selected' : '' ?>>12GB</option>
            <option value="16gb" <?= set_value('memoria') === '16gb' ? 'selected' : '' ?>>16GB</option>
            <option value="NO APLICA" <?= set_value('memoria') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
            <option value="otro" <?= set_value('memoria') === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>

        <input type="text" class="form-control mt-2" id="memoria_otro" name="memoria_otro"
            value="<?= set_value('memoria_otro') ?>"
            placeholder="Especifique otra memoria"
            style="display: none;">
    </div>

    <div class="col-md-6">
        <label for="tipo_disco" class="form-label">Tipo de disco</label>
        <select class="form-select form-control" id="tipo_disco" name="tipo_disco" required >
            <option value="">Seleccionar</option>
            <option value="M.2" <?= set_value('tipo_disco') === 'M.2' ? 'selected' : '' ?>>M.2</option>
            <option value="SSD 2.5" <?= set_value('tipo_disco') === 'SSD 2.5' ? 'selected' : '' ?>>SSD 2.5</option>
            <option value="HDD" <?= set_value('tipo_disco') === 'HDD' ? 'selected' : '' ?>>HDD</option>
            <option value="NO APLICA" <?= set_value('tipo_disco') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="espacio_disco" class="form-label">Espacio de disco</label>
        <input type="text" class="form-control" id="espacio_disco" name="espacio_disco" value="<?= set_value('espacio_disco') ?>" required>
    </div>

    <div class="col-md-6">
        <label for="sistema_operativo" class="form-label">Sistema Operativo</label>
        <select class="form-select form-control" id="sistema_operativo" name="sistema_operativo" required onchange="toggleOtroSO(this)">
            <option value="">Seleccionar</option>
            <option value="Windows 10" <?= set_value('sistema_operativo') === 'Windows 10' ? 'selected' : '' ?>>Windows 10</option>
            <option value="Windows 11" <?= set_value('sistema_operativo') === 'Windows 11' ? 'selected' : '' ?>>Windows 11</option>
            <option value="Linux" <?= set_value('sistema_operativo') === 'Linux' ? 'selected' : '' ?>>Linux</option>
            <option value="MacOs" <?= set_value('sistema_operativo') === 'MacOs' ? 'selected' : '' ?>>MacOs</option>
            <option value="NO APLICA" <?= set_value('sistema_operativo') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
            <option value="otro" <?= set_value('sistema_operativo') === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>
    </div>
    <div class="col-md-6" id="otroSODiv" style="display: <?= set_value('sistema_operativo') === 'otros' ? 'block' : 'none'; ?>;">
        <label for="otroSO" class="form-label">Especifique el sistema operativo</label>
        <input type="text" class="form-control" id="otroSO" name="otroSO" value="<?= set_value('otroSO') ?>">
    </div>

    <div class="col-md-6">
        <label for="ver_office" class="form-label">Version de Office</label>
        <select class="form-select form-control" id="ver_office" name="ver_office" required >
            <option value="">Seleccionar</option>
            <option value="Microsoft 365" <?= set_value('ver_office') === 'Microsoft 365' ? 'selected' : '' ?>>Microsoft 365</option>
            <option value="Microsoft Office Hogar y Empresas 2016" <?= set_value('ver_office') === 'Microsoft Hogar y Empresas 2016' ? 'selected' : '' ?>>Microsoft Office Hogar y Empresas 2016</option>
            <option value="Microsoft Office Hogar y Empresas 2019" <?= set_value('ver_office') === 'Microsoft Hogar y Empresas 2019' ? 'selected' : '' ?>>Microsoft Office Hogar y Empresas 2019</option>
            <option value="Microsoft Office Hogar y Empresas 2021" <?= set_value('ver_office') === 'Microsoft Hogar y Empresas 2021' ? 'selected' : '' ?>>Microsoft Office Hogar y Empresas 2021</option>
            <option value="Microsoft Office Profesional 2021" <?= set_value('ver_office') === 'Microsoft Profesional 2021' ? 'selected' : '' ?>>Microsoft Office Profesional 2021</option>
            <option value="Microsoft Office LTSC Standard 2021" <?= set_value('ver_office') === 'Microsoft Office LTSC Standard 2021' ? 'selected' : '' ?>>Microsoft Office LTSC Standard 2021</option>
            <option value="Microsoft Office Standard 2007" <?= set_value('ver_office') === 'Microsoft Office Standard 2007' ? 'selected' : '' ?>>Microsoft Office Standard 2007</option>
            <option value="NO APLICA" <?= set_value('ver_office') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="Ip" class="form-label">IP Address</label>
        <input type="text" class="form-control" id="Ip" name="Ip" value="<?= set_value('Ip') ?>" required>
    </div>

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select class="form-select form-control" id="estado" name="estado" required>
            <option value="">Seleccionar</option>
            <option value="bueno" <?= set_value('estado') === 'bueno' ? 'selected' : '' ?>>Bueno</option>
            <option value="regular" <?= set_value('estado') === 'regular' ? 'selected' : '' ?>>Regular</option>
            <option value="malo" <?= set_value('estado') === 'malo' ? 'selected' : '' ?>>Malo</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="fecha_adquisicion" class="form-label">Fecha_adquisicion</label>
        <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion" value="<?= set_value('fecha_adquisicion') ?>">
    </div>

    <div class="col-md-6">
        <label for="años_garantia" class="form-label">Años de garantia</label>
        <input type="text" class="form-control" id="años_garantia" name="años_garantia" value="<?= set_value('años_garantia') ?>">
    </div>

    <div class="col-md-6">
        <label for="proveedor" class="form-label">Proveedor</label>
        <input type="text" class="form-control" id="proveedor" name="proveedor" value="<?= set_value('proveedor') ?>">
    </div>

    <div class="col-md-6">
        <label for="departamento" class="form-label">Departamento</label>
        <select class="form-select form-control" id="departamento" name="departamento" required>
            <option value="">Seleccionar</option>
            <?php foreach ($departamentos as $departamento) : ?>
                <option value="<?= $departamento['id']; ?>"><?= $departamento['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="usuario" class="form-label">Asignar a Persona:</label>
        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Escriba un nombre">
        <input type="hidden" id="usuarioId" name="id_personas"> <!-- ID oculto de la persona -->
        <ul id="usuarioSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000;"></ul>
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


    <div class="col-12">
        <a href="<?= base_url('bienes') ?>" class="btn btn-secondary">Regresar</a>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>

</form>


<script>
    function toggleOtroInput() {
        const memoriaSelect = document.getElementById('memoria');
        const otroInput = document.getElementById('memoria_otro');

        // Mostrar el input si selecciona "otro"
        if (memoriaSelect.value === 'otro') {
            otroInput.style.display = 'block';
            otroInput.required = true;
        } else {
            otroInput.style.display = 'none';
            otroInput.required = false;
        }
    }

    function toggleOtroProcesador() {
        const procesadorSelect = document.getElementById('procesador');
        const otroInput = document.getElementById('procesador_otro');

        // Mostrar el input si selecciona "otro"
        if (procesadorSelect.value === 'otro') {
            otroInput.style.display = 'block';
            otroInput.required = true;
        } else {
            otroInput.style.display = 'none';
            otroInput.required = false;
        }
    }

    function toggleOtraMarca(select) {
        const otraMarcaDiv = document.getElementById('otraMarcaDiv');
        const otraMarcaInput = document.getElementById('otraMarca');

        if (select.value === 'otro') {
            otraMarcaDiv.style.display = 'block';
            otraMarcaInput.required = true; // Hacer obligatorio el campo cuando se selecciona "Otro"
        } else {
            otraMarcaDiv.style.display = 'none';
            otraMarcaInput.required = false; // Deshabilitar obligatoriedad si no es "Otro"
            otraMarcaInput.value = ''; // Limpiar el campo de texto
        }
    }

    // Ejecutar al cargar la página para mantener el estado correcto al recargar el formulario
    window.addEventListener('DOMContentLoaded', () => {
        const marcaSelect = document.getElementById('marca');
        toggleOtraMarca(marcaSelect);
    });

    function toggleOtroSO(selectElement) {
        const otroSODiv = document.getElementById('otroSODiv');
        if (selectElement.value === 'otro') {
            otroSODiv.style.display = 'block';
        } else {
            otroSODiv.style.display = 'none';
        }
    }
</script>
<?= $this->endSection(); ?>