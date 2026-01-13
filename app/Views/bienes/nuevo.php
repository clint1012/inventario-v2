<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<style>
    .form-card {
        border-left: 4px solid #0d6efd;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    .card-header h5 {
        font-size: 1rem;
        margin-bottom: 0;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
    }
    .form-control, .form-select {
        font-size: 0.875rem;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1"><i class="fas fa-plus-circle text-primary"></i> Registrar Nuevo Bien</h4>
        <p class="text-muted mb-0 small">Complete la información del bien a registrar</p>
    </div>
    <div>
        <a href="<?= base_url('bienes') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
    </div>
</div>

<!-- Mensajes Flash -->
<?php if (session()->getFlashdata('error') !== null): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<form action="<?= base_url('bienes') ?>" method="post" autocomplete="off">
    <div class="row g-3">
        
        <!-- Card: Información Básica -->
        <div class="col-12">
            <div class="card shadow-sm form-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cod_patrimonial" class="form-label">Código Patrimonial <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cod_patrimonial" name="cod_patrimonial"
                                value="<?= set_value('cod_patrimonial') ?>" required autofocus maxlength="12"
                                placeholder="Ingrese 12 caracteres numéricos">
                            <div id="cod_patrimonial_error" class="text-danger"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="tipo_bien" class="form-label">Tipo de Bien <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="tipo_bien" name="tipo_bien" required onchange="actualizarCamposPorTipo()">
                                <option value="">-- Seleccione el tipo --</option>
                                <option value="computadora" <?= set_value('tipo_bien') === 'computadora' ? 'selected' : '' ?>>💻 Computadora (CPU)</option>
                                <option value="laptop" <?= set_value('tipo_bien') === 'laptop' ? 'selected' : '' ?>>💻 Laptop/Portátil</option>
                                <option value="all_in_one" <?= set_value('tipo_bien') === 'all_in_one' ? 'selected' : '' ?>>🖥️ All in One (AIO)</option>
                                <option value="monitor" <?= set_value('tipo_bien') === 'monitor' ? 'selected' : '' ?>>🖥️ Monitor</option>
                                <option value="teclado" <?= set_value('tipo_bien') === 'teclado' ? 'selected' : '' ?>>⌨️ Teclado</option>
                                <option value="mouse" <?= set_value('tipo_bien') === 'mouse' ? 'selected' : '' ?>>🖱️ Mouse</option>
                                <option value="impresora" <?= set_value('tipo_bien') === 'impresora' ? 'selected' : '' ?>>🖨️ Impresora</option>
                                <option value="scanner" <?= set_value('tipo_bien') === 'scanner' ? 'selected' : '' ?>>📠 Scanner</option>
                                <option value="multifuncional" <?= set_value('tipo_bien') === 'multifuncional' ? 'selected' : '' ?>>🖨️ Multifuncional</option>
                                <option value="switch" <?= set_value('tipo_bien') === 'switch' ? 'selected' : '' ?>>🔌 Switch</option>
                                <option value="router" <?= set_value('tipo_bien') === 'router' ? 'selected' : '' ?>>📡 Router</option>
                                <option value="access_point" <?= set_value('tipo_bien') === 'access_point' ? 'selected' : '' ?>>📶 Access Point</option>
                                <option value="camara" <?= set_value('tipo_bien') === 'camara' ? 'selected' : '' ?>>📹 Cámara Videovigilancia</option>
                                <option value="proyector" <?= set_value('tipo_bien') === 'proyector' ? 'selected' : '' ?>>📽️ Proyector</option>
                                <option value="servidor" <?= set_value('tipo_bien') === 'servidor' ? 'selected' : '' ?>>🖥️ Servidor</option>
                                <option value="nas" <?= set_value('tipo_bien') === 'nas' ? 'selected' : '' ?>>💾 NAS (Storage)</option>
                                <option value="ups" <?= set_value('tipo_bien') === 'ups' ? 'selected' : '' ?>>🔋 UPS</option>
                                <option value="rack" <?= set_value('tipo_bien') === 'rack' ? 'selected' : '' ?>>🗄️ Rack/Gabinete</option>
                                <option value="tablet" <?= set_value('tipo_bien') === 'tablet' ? 'selected' : '' ?>>📱 Tablet</option>
                                <option value="otro" <?= set_value('tipo_bien') === 'otro' ? 'selected' : '' ?>>📦 Otro</option>
                            </select>
                            <small class="form-text text-muted">Seleccione primero el tipo para mostrar solo los campos necesarios</small>
                        </div>

                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion"
                                value="<?= set_value('descripcion') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="marca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="marca" name="marca" required onchange="toggleOtraMarca(this)">
                                <option value="">Seleccionar</option>
                                <option value="hp" <?= set_value('marca') === 'hp' ? 'selected' : '' ?>>HP</option>
                                <option value="lenovo" <?= set_value('marca') === 'lenovo' ? 'selected' : '' ?>>Lenovo</option>
                                <option value="dell" <?= set_value('marca') === 'dell' ? 'selected' : '' ?>>Dell</option>
                                <option value="microsoft" <?= set_value('marca') === 'microsoft' ? 'selected' : '' ?>>Microsoft</option>
                                <option value="viewsonic" <?= set_value('marca') === 'viewsonic' ? 'selected' : '' ?>>Viewsonic</option>
                                <option value="toshiba" <?= set_value('marca') === 'toshiba' ? 'selected' : '' ?>>Toshiba</option>
                                <option value="epson" <?= set_value('marca') === 'epson' ? 'selected' : '' ?>>Epson</option>
                                <option value="xerox" <?= set_value('marca') === 'xerox' ? 'selected' : '' ?>>Xerox</option>
                                <option value="LG" <?= set_value('marca') === 'LG' ? 'selected' : '' ?>>LG</option>
                                <option value="Mikrotik" <?= set_value('marca') === 'Mikrotik' ? 'selected' : '' ?>>Mikrotik</option>
                                <option value="otro" <?= set_value('marca') === 'otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="otraMarcaDiv" style="display: none;">
                            <label for="otraMarca" class="form-label">Especifique otra marca</label>
                            <input type="text" class="form-control" id="otraMarca" name="otraMarca" value="<?= set_value('otraMarca') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modelo" name="modelo" value="<?= set_value('modelo') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="serie" class="form-label">Serie</label>
                            <input type="text" class="form-control" id="serie" name="serie" value="<?= set_value('serie') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Especificaciones Técnicas -->
        <div class="col-12">
            <div class="card shadow-sm form-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-microchip me-2"></i>Especificaciones Técnicas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="procesador" class="form-label">Procesador <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="procesador" name="procesador" required
                                onchange="toggleOtroProcesador()">
                                <option value="">Seleccionar</option>
                                <option value="core i3" <?= set_value('procesador') === 'core i3' ? 'selected' : '' ?>>Core i3</option>
                                <option value="core i5-7ma" <?= set_value('procesador') === 'core i5-7ma' ? 'selected' : '' ?>>Core i5-7ma Gen</option>
                                <option value="core i5-9na" <?= set_value('procesador') === 'core i5-9na' ? 'selected' : '' ?>>Core i5-9na Gen</option>
                                <option value="core i7-7ma" <?= set_value('procesador') === 'core i7-7ma' ? 'selected' : '' ?>>Core i7-7ma Gen</option>
                                <option value="core i7-9na" <?= set_value('procesador') === 'core i7-9na' ? 'selected' : '' ?>>Core i7-9na Gen</option>
                                <option value="core i7-10ma" <?= set_value('procesador') === 'core i7-10ma' ? 'selected' : '' ?>>Core i7-10ma Gen</option>
                                <option value="core i7-11va" <?= set_value('procesador') === 'core i7-11va' ? 'selected' : '' ?>>Core i7-11va Gen</option>
                                <option value="core i7-14va" <?= set_value('procesador') === 'core i7-14va' ? 'selected' : '' ?>>Core i7-14va Gen</option>
                                <option value="core i7-ultra" <?= set_value('procesador') === 'core i7-utra' ? 'selected' : '' ?>>Core i7-Ultra</option>
                                <option value="NO APLICA" <?= set_value('procesador') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
                                <option value="otro" <?= set_value('procesador') === 'otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="procesador_otro" name="procesador_otro"
                                value="<?= set_value('procesador_otro') ?>" placeholder="Especifique otro procesador"
                                style="display: none;">
                        </div>

                        <div class="col-md-6">
                            <label for="memoria" class="form-label">Memoria <span class="text-danger">*</span></label>
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
                                value="<?= set_value('memoria_otro') ?>" placeholder="Especifique otra memoria" style="display: none;">
                        </div>

                        <div class="col-md-6">
                            <label for="tipo_disco" class="form-label">Tipo de Disco <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="tipo_disco" name="tipo_disco" required>
                                <option value="">Seleccionar</option>
                                <option value="M.2" <?= set_value('tipo_disco') === 'M.2' ? 'selected' : '' ?>>M.2</option>
                                <option value="SSD 2.5" <?= set_value('tipo_disco') === 'SSD 2.5' ? 'selected' : '' ?>>SSD 2.5</option>
                                <option value="HDD" <?= set_value('tipo_disco') === 'HDD' ? 'selected' : '' ?>>HDD</option>
                                <option value="NO APLICA" <?= set_value('tipo_disco') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="espacio_disco" class="form-label">Espacio de Disco</label>
                            <input type="text" class="form-control" id="espacio_disco" name="espacio_disco"
                                value="<?= set_value('espacio_disco') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="sistema_operativo" class="form-label">Sistema Operativo <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="sistema_operativo" name="sistema_operativo" required
                                onchange="toggleOtroSO(this)">
                                <option value="">Seleccionar</option>
                                <option value="Windows 10" <?= set_value('sistema_operativo') === 'Windows 10' ? 'selected' : '' ?>>Windows 10</option>
                                <option value="Windows 11" <?= set_value('sistema_operativo') === 'Windows 11' ? 'selected' : '' ?>>Windows 11</option>
                                <option value="Linux" <?= set_value('sistema_operativo') === 'Linux' ? 'selected' : '' ?>>Linux</option>
                                <option value="MacOs" <?= set_value('sistema_operativo') === 'MacOs' ? 'selected' : '' ?>>MacOs</option>
                                <option value="NO APLICA" <?= set_value('sistema_operativo') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
                                <option value="otro" <?= set_value('sistema_operativo') === 'otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="otroSODiv"
                            style="display: <?= set_value('sistema_operativo') === 'otros' ? 'block' : 'none'; ?>;">
                            <label for="otroSO" class="form-label">Especifique el Sistema Operativo</label>
                            <input type="text" class="form-control" id="otroSO" name="otroSO" value="<?= set_value('otroSO') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="ver_office" class="form-label">Versión de Office <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="ver_office" name="ver_office" required>
                                <option value="">Seleccionar</option>
                                <option value="Microsoft 365" <?= set_value('ver_office') === 'Microsoft 365' ? 'selected' : '' ?>>Microsoft 365</option>
                                <option value="Microsoft Office Hogar y Empresas 2016" <?= set_value('ver_office') === 'Microsoft Hogar y Empresas 2016' ? 'selected' : '' ?>>Microsoft Office Hogar y Empresas 2016</option>
                                <option value="Microsoft Office Hogar y Empresas 2019" <?= set_value('ver_office') === 'Microsoft Hogar y Empresas 2019' ? 'selected' : '' ?>>Microsoft Office Hogar y Empresas 2019</option>
                                <option value="Microsoft Office Hogar y Empresas 2021" <?= set_value('ver_office') === 'Microsoft Hogar y Empresas 2021' ? 'selected' : '' ?>>Microsoft Office Hogar y Empresas 2021</option>
                                <option value="Microsoft Office Hogar y Empresas 2024" <?= set_value('ver_office') === 'Microsoft Hogar y Empresas 2024' ? 'selected' : '' ?>>Microsoft Office Hogar y Empresas 2024</option>
                                <option value="Microsoft Office Profesional 2021" <?= set_value('ver_office') === 'Microsoft Profesional 2021' ? 'selected' : '' ?>>Microsoft Office Profesional 2021</option>
                                <option value="Microsoft Office LTSC Standard 2021" <?= set_value('ver_office') === 'Microsoft Office LTSC Standard 2021' ? 'selected' : '' ?>>Microsoft Office LTSC Standard 2021</option>
                                <option value="Microsoft Office Standard 2007" <?= set_value('ver_office') === 'Microsoft Office Standard 2007' ? 'selected' : '' ?>>Microsoft Office Standard 2007</option>
                                <option value="NO APLICA" <?= set_value('ver_office') === 'NO APLICA' ? 'selected' : '' ?>>No Aplica</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="Ip" class="form-label">Dirección IP</label>
                            <input type="text" class="form-control" id="Ip" name="Ip" value="<?= set_value('Ip') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Ubicación, Asignación y Compra -->
        <div class="col-12">
            <div class="card shadow-sm form-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Ubicación, Asignación y Compra</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="estado" name="estado" required>
                                <option value="">Seleccionar</option>
                                <option value="bueno" <?= set_value('estado') === 'bueno' ? 'selected' : '' ?>>Bueno</option>
                                <option value="regular" <?= set_value('estado') === 'regular' ? 'selected' : '' ?>>Regular</option>
                                <option value="malo" <?= set_value('estado') === 'malo' ? 'selected' : '' ?>>Malo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_adquisicion" class="form-label">Fecha de Adquisición</label>
                            <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion"
                                value="<?= set_value('fecha_adquisicion') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="num_doc_compra" class="form-label">Número Orden de Compra</label>
                            <input type="text" class="form-control" id="num_doc_compra" name="num_doc_compra"
                                value="<?= set_value('num_doc_compra') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="años_garantia" class="form-label">Años de Garantía</label>
                            <input type="text" class="form-control" id="años_garantia" name="años_garantia"
                                value="<?= set_value('años_garantia') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control" id="proveedor_id" name="proveedor_id" required>
                                <option value="">Seleccione proveedor...</option>
                                <?php foreach ($proveedores as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= strtoupper($p['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="departamento" class="form-label">Departamento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="departamento" name="departamento_nombre" 
                                   placeholder="Escriba al menos 3 letras..." autocomplete="off">
                            <input type="hidden" id="departamentoId" name="departamento" required>
                            <ul id="departamentoSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000; max-height: 200px; overflow-y: auto;"></ul>
                            <small class="form-text text-muted">Escriba al menos 3 letras para ver sugerencias</small>
                        </div>

                        <div class="col-md-6">
                            <label for="usuario" class="form-label">Asignar a Persona</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Escriba un nombre">
                            <input type="hidden" id="usuarioId" name="id_personas">
                            <ul id="usuarioSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000;"></ul>
                        </div>

                        <div class="col-md-6">
                            <label for="id_locales" class="form-label">Sede <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="id_locales" name="id_locales" required>
                                <option value="">Seleccione una sede</option>
                                <?php foreach ($locales as $local): ?>
                                    <option value="<?= $local['id']; ?>"><?= $local['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <a href="<?= base_url('bienes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Regresar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Guardar
            </button>
        </div>
    </div>
</form>


<script>
    // 🎯 Configuración de campos requeridos por tipo de bien
    const camposPorTipo = {
        computadora: ['procesador', 'memoria', 'tipo_disco', 'espacio_disco', 'sistema_operativo', 'ver_office', 'Ip'],
        laptop: ['procesador', 'memoria', 'tipo_disco', 'espacio_disco', 'sistema_operativo', 'ver_office'],
        all_in_one: ['procesador', 'memoria', 'tipo_disco', 'espacio_disco', 'sistema_operativo', 'ver_office', 'Ip'],
        monitor: [],
        teclado: [],
        mouse: [],
        impresora: ['Ip'],
        scanner: [],
        multifuncional: ['Ip'],
        switch: ['Ip'],
        router: ['Ip'],
        access_point: ['Ip'],
        camara: ['Ip'],
        proyector: [],
        servidor: ['procesador', 'memoria', 'tipo_disco', 'espacio_disco', 'sistema_operativo', 'Ip'],
        nas: ['memoria', 'tipo_disco', 'espacio_disco', 'Ip'],
        ups: [],
        rack: [],
        tablet: ['sistema_operativo'],
        otro: []
    };

    function actualizarCamposPorTipo() {
        const tipoBien = document.getElementById('tipo_bien').value;
        
        if (!tipoBien) {
            return; // Si no hay tipo seleccionado, no hacer nada
        }

        const camposRequeridos = camposPorTipo[tipoBien] || [];
        
        // Lista de todos los campos técnicos que pueden ocultarse
        const camposTecnicos = [
            'procesador', 'memoria', 'tipo_disco', 'espacio_disco', 
            'sistema_operativo', 'ver_office', 'Ip'
        ];

        camposTecnicos.forEach(campo => {
            const elemento = document.getElementById(campo);
            const row = elemento?.closest('.col-md-6');
            
            if (row) {
                if (camposRequeridos.includes(campo)) {
                    // Mostrar campo
                    row.style.display = 'block';
                    elemento.required = true;
                    // Limpiar "NO APLICA" si existe
                    if (elemento.value === 'NO APLICA') {
                        elemento.value = '';
                    }
                } else {
                    // Ocultar campo y establecer "NO APLICA"
                    row.style.display = 'none';
                    elemento.required = false;
                    elemento.value = 'NO APLICA';
                }
            }

            // Manejar también los divs especiales (otroProcesador, otroSO, etc.)
            if (campo === 'procesador') {
                const procesadorOtro = document.getElementById('procesador_otro');
                if (procesadorOtro?.parentElement) {
                    procesadorOtro.parentElement.style.display = camposRequeridos.includes(campo) ? 'none' : 'none';
                }
            }
            
            if (campo === 'sistema_operativo') {
                const otroSODiv = document.getElementById('otroSODiv');
                if (otroSODiv) {
                    otroSODiv.style.display = 'none';
                }
            }
            
            if (campo === 'memoria') {
                const memoriaOtro = document.getElementById('memoria_otro');
                if (memoriaOtro) {
                    memoriaOtro.style.display = 'none';
                }
            }
        });

        // Manejo especial para espacio_disco (es input text)
        const espacioDisco = document.getElementById('espacio_disco');
        if (espacioDisco) {
            const row = espacioDisco.closest('.col-md-6');
            if (row) {
                if (camposRequeridos.includes('espacio_disco')) {
                    row.style.display = 'block';
                    espacioDisco.required = false; // Es opcional incluso si se muestra
                } else {
                    row.style.display = 'none';
                    espacioDisco.value = '';
                }
            }
        }
    }

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
        
        // Aplicar configuración de campos según tipo seleccionado
        actualizarCamposPorTipo();
    });

    function toggleOtroSO(selectElement) {
        const otroSODiv = document.getElementById('otroSODiv');
        if (selectElement.value === 'otro') {
            otroSODiv.style.display = 'block';
        } else {
            otroSODiv.style.display = 'none';
        }
    }

    // Autocompletado de departamento
    document.addEventListener('DOMContentLoaded', function() {
        const departamentoInput = document.getElementById('departamento');
        const departamentoIdInput = document.getElementById('departamentoId');
        const departamentoSuggestions = document.getElementById('departamentoSuggestions');

        let debounceTimer;

        departamentoInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(debounceTimer);

            if (query.length < 3) {
                departamentoSuggestions.style.display = 'none';
                departamentoIdInput.value = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`<?= base_url('bienes/departamentos') ?>?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        departamentoSuggestions.innerHTML = '';

                        if (data.length === 0) {
                            departamentoSuggestions.innerHTML = '<li class="list-group-item text-muted">No se encontraron departamentos</li>';
                            departamentoSuggestions.style.display = 'block';
                            return;
                        }

                        data.forEach(departamento => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action';
                            li.style.cursor = 'pointer';
                            li.textContent = departamento.nombre;
                            li.dataset.id = departamento.id;

                            li.addEventListener('click', function() {
                                departamentoInput.value = this.textContent;
                                departamentoIdInput.value = this.dataset.id;
                                departamentoSuggestions.style.display = 'none';
                            });

                            departamentoSuggestions.appendChild(li);
                        });

                        departamentoSuggestions.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error al buscar departamentos:', error);
                        departamentoSuggestions.style.display = 'none';
                    });
            }, 300);
        });

        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (e.target !== departamentoInput && e.target !== departamentoSuggestions) {
                departamentoSuggestions.style.display = 'none';
            }
        });
    });
</script>
<?= $this->endSection(); ?>