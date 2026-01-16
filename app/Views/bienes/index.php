<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<style>
    /* Ocultar tabla hasta que DataTables esté listo */
    #bienesTable {
        opacity: 0;
        transition: opacity 0.3s ease-in;
    }
    #bienesTable.dt-ready {
        opacity: 1;
    }
    /* Loading spinner */
    #tableLoading {
        text-align: center;
        padding: 3rem;
        display: block;
    }
    #tableLoading.hide {
        display: none;
    }
</style>

<h3 class="my-3" id="titulo">Bienes</h3>
<!-- Mensajes Flash -->
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>



<div class="d-flex justify-content-between mb-4">
    <div>
        <a href="<?= base_url('bienes/new') ?>" class="btn btn-info">
            <i class="fas fa-plus"></i> Agregar Bien
        </a>
        
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalSubidaMasiva">
            <i class="fas fa-upload"></i> Subida Masiva
        </button>
        
        <a href="<?= base_url('bienes/descargarPlantillaCSV') ?>" class="btn btn-secondary">
            <i class="fas fa-download"></i> Descargar Plantilla CSV
        </a>
    </div>

    <button id="btnExportarFiltrado" class="btn btn-success mb-2">
        <i class="fas fa-file-excel"></i> Exportar Excel
    </button>
</div>

<!-- 🔍 Panel de Filtros -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <button class="btn btn-link text-white text-decoration-none w-100 text-start" type="button" data-toggle="collapse" data-target="#filtrosPanel" aria-expanded="false" aria-controls="filtrosPanel">
                <i class="fas fa-filter"></i> Filtros de Búsqueda
                <i class="fas fa-chevron-right float-end" id="filtrosChevron"></i>
            </button>
        </h5>
    </div>
    <div class="collapse" id="filtrosPanel">
        <div class="card-body">
            <div class="row g-3">
                <!-- Fila 1: Información Básica -->
                <div class="col-12"><h6 class="text-primary mb-2"><i class="fas fa-info-circle"></i> Información Básica</h6></div>
                <div class="col-md-3">
                    <label for="filtro_codigo" class="form-label">Código Patrimonial</label>
                    <input type="text" class="form-control form-control-sm" id="filtro_codigo" placeholder="Buscar código...">
                </div>
                <div class="col-md-3">
                    <label for="filtro_tipo" class="form-label">Tipo de Bien</label>
                    <select class="form-select form-select-sm" id="filtro_tipo">
                        <option value="">Todos los tipos</option>
                        <option value="computadora">💻 Computadora</option>
                        <option value="laptop">💻 Laptop</option>
                        <option value="all_in_one">🖥️ All in One</option>
                        <option value="monitor">🖥️ Monitor</option>
                        <option value="monitor_con_procesador" style="background-color: #fff3cd; font-weight: bold;">⚠️ Monitores con Procesador (Revisar)</option>
                        <option value="teclado">⌨️ Teclado</option>
                        <option value="mouse">🖱️ Mouse</option>
                        <option value="impresora">🖨️ Impresora</option>
                        <option value="scanner">📠 Scanner</option>
                        <option value="multifuncional">🖨️ Multifuncional</option>
                        <option value="switch">🔌 Switch</option>
                        <option value="router">📡 Router</option>
                        <option value="access_point">📶 Access Point</option>
                        <option value="camara">📹 Cámara</option>
                        <option value="proyector">📽️ Proyector</option>
                        <option value="servidor">🖥️ Servidor</option>
                        <option value="nas">💾 NAS</option>
                        <option value="ups">🔋 UPS</option>
                        <option value="rack">🗄️ Rack</option>
                        <option value="tablet">📱 Tablet</option>
                        <option value="otro">📦 Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_marca" class="form-label">Marca</label>
                    <select class="form-select form-select-sm" id="filtro_marca">
                        <option value="">Todas las marcas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_modelo" class="form-label">Modelo</label>
                    <select class="form-select form-select-sm" id="filtro_modelo">
                        <option value="">Todos los modelos</option>
                    </select>
                </div>

                <!-- Fila 2: Detalles del Equipo -->
                <div class="col-12 mt-3"><h6 class="text-primary mb-2"><i class="fas fa-laptop"></i> Especificaciones Técnicas</h6></div>
                <div class="col-md-3">
                    <label for="filtro_descripcion" class="form-label">Descripción</label>
                    <input type="text" class="form-control form-control-sm" id="filtro_descripcion" placeholder="Buscar descripción...">
                </div>
                <div class="col-md-3">
                    <label for="filtro_serie" class="form-label">Serie</label>
                    <input type="text" class="form-control form-control-sm" id="filtro_serie" placeholder="Buscar serie...">
                </div>
                <div class="col-md-3 filtro-tecnico" style="display: none;">
                    <label for="filtro_procesador" class="form-label">Procesador</label>
                    <select class="form-select form-select-sm" id="filtro_procesador">
                        <option value="">Todos</option>
                    </select>
                </div>
                <div class="col-md-3 filtro-tecnico" style="display: none;">
                    <label for="filtro_memoria" class="form-label">Memoria RAM</label>
                    <select class="form-select form-select-sm" id="filtro_memoria">
                        <option value="">Todas</option>
                    </select>
                </div>

                <!-- Fila 3: Sistema y Software -->
                <div class="col-md-3 filtro-tecnico" style="display: none;">
                    <label for="filtro_disco" class="form-label">Tipo de Disco</label>
                    <select class="form-select form-select-sm" id="filtro_disco">
                        <option value="">Todos</option>
                    </select>
                </div>
                <div class="col-md-3 filtro-tecnico" style="display: none;">
                    <label for="filtro_almacenamiento" class="form-label">Espacio Disco (GB)</label>
                    <input type="text" class="form-control form-control-sm" id="filtro_almacenamiento" placeholder="Ej: 256, 512...">
                </div>
                <div class="col-md-3 filtro-tecnico" style="display: none;">
                    <label for="filtro_sistema_operativo" class="form-label">Sistema Operativo</label>
                    <select class="form-select form-select-sm" id="filtro_sistema_operativo">
                        <option value="">Todos</option>
                    </select>
                </div>
                <div class="col-md-3 filtro-tecnico" style="display: none;">
                    <label for="filtro_office" class="form-label">Versión Office</label>
                    <select class="form-select form-select-sm" id="filtro_office">
                        <option value="">Todas</option>
                    </select>
                </div>

                <!-- Fila 4: Estado y Ubicación -->
                <div class="col-12 mt-3"><h6 class="text-primary mb-2"><i class="fas fa-map-marker-alt"></i> Estado y Ubicación</h6></div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select form-select-sm" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="asignado">Asignado</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="retirado">Retirado</option>
                        <option value="regular">Regular</option>
                        <option value="bueno">Bueno</option>
                        <option value="malo">Malo</option>
                        <option value="nuevo">Nuevo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_garantia" class="form-label">Garantía</label>
                    <select class="form-select form-select-sm" id="filtro_garantia">
                        <option value="">Todos</option>
                        <option value="en garantía">En garantía</option>
                        <option value="garantía caducada">Garantía caducada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_departamento" class="form-label">Departamento</label>
                    <select class="form-select form-select-sm" id="filtro_departamento">
                        <option value="">Todos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_local" class="form-label">Local/Sede</label>
                    <select class="form-select form-select-sm" id="filtro_local">
                        <option value="">Todos</option>
                    </select>
                </div>

                <!-- Fila 5: Usuario y Fecha -->
                <div class="col-md-3">
                    <label for="filtro_usuario" class="form-label">Usuario Asignado</label>
                    <input type="text" class="form-control form-control-sm" id="filtro_usuario" 
                           placeholder="Escriba al menos 3 letras..." autocomplete="off">
                    <ul id="filtroUsuarioSuggestions" class="list-group" 
                        style="display: none; position: absolute; z-index: 1050; max-height: 200px; overflow-y: auto; width: 90%;"></ul>
                </div>
                <div class="col-md-3">
                    <label for="filtro_fecha" class="form-label">Fecha Adquisición</label>
                    <input type="date" class="form-control form-control-sm" id="filtro_fecha">
                </div>

                <!-- Botones -->
                <div class="col-12">
                    <hr>
                    <button id="clearFilters" class="btn btn-outline-secondary">
                        <i class="fas fa-eraser"></i> Limpiar Filtros
                    </button>
                    <small class="text-muted ms-3">
                        <i class="fas fa-info-circle"></i> Los filtros se aplican automáticamente al escribir o seleccionar
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 📊 Tabla de Bienes -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- Loading Spinner -->
        <div id="tableLoading">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">
                <i class="fas fa-sync-alt fa-spin"></i> Cargando tabla de bienes...
            </p>
        </div>
        
        <div class="table-responsive" style="overflow-x: hidden;">
            <table class="table table-hover table-striped table-sm mb-0" id="bienesTable" aria-describedby="titulo" style="font-size: 0.875rem; width: 100%;">
                <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
        <tr class="text-center align-middle">
            <th scope="col" style="font-weight: 600;"><i class="fas fa-hashtag"></i> ID</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-barcode"></i> Código</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-tag"></i> Tipo</th>
            <th scope="col" style="font-weight: 600; text-align: left;"><i class="fas fa-align-left"></i> Descripción</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-industry"></i> Marca</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-box"></i> Modelo</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-qrcode"></i> Serie</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-building"></i> Local</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-sitemap"></i> Departamento</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-check-circle"></i> Estado</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-user"></i> Usuario</th>
            <th scope="col" style="font-weight: 600;"><i class="fas fa-cog"></i> Acciones</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($bienes as $b): ?>
            <tr 
                data-procesador="<?= htmlspecialchars($b['procesador'] ?? '') ?>"
                data-memoria="<?= htmlspecialchars($b['memoria'] ?? '') ?>"
                data-disco="<?= htmlspecialchars($b['tipo_disco'] ?? '') ?>"
                data-espacio="<?= htmlspecialchars($b['espacio_disco'] ?? '') ?>"
                data-so="<?= htmlspecialchars($b['sistema_operativo'] ?? '') ?>"
                data-office="<?= htmlspecialchars($b['office'] ?? '') ?>"
            >
                <td class="text-center align-middle"><strong class="text-primary"><?= $b['id'] ?></strong></td>
                <td class="text-center align-middle">
                    <span class="badge bg-dark text-white" style="font-size: 0.85rem; padding: 0.45rem 0.7rem; font-family: 'Courier New', monospace; letter-spacing: 0.5px; font-weight: 600;">
                        <?= $b['cod_patrimonial'] ?>
                    </span>
                </td>
                <td class="text-center align-middle" data-tipo="<?= $b['tipo_bien'] ?? 'otro' ?>">
                    <?php
                    $iconos = [
                        'computadora' => '💻',
                        'laptop' => '💻',
                        'all_in_one' => '🖥️',
                        'monitor' => '🖥️',
                        'teclado' => '⌨️',
                        'mouse' => '🖱️',
                        'impresora' => '🖨️',
                        'scanner' => '📠',
                        'multifuncional' => '🖨️',
                        'switch' => '🔌',
                        'router' => '📡',
                        'access_point' => '📶',
                        'camara' => '📹',
                        'proyector' => '📽️',
                        'servidor' => '🖥️',
                        'nas' => '💾',
                        'ups' => '🔋',
                        'rack' => '🗄️',
                        'tablet' => '📱',
                        'otro' => '📦'
                    ];
                    $tipo = $b['tipo_bien'] ?? 'otro';
                    $tipoNombre = ucfirst(str_replace('_', ' ', $tipo));
                    echo '<span title="' . $tipoNombre . '" style="font-size: 1.2rem;">' . ($iconos[$tipo] ?? '📦') . '</span> <small class="d-none d-xl-inline text-muted">' . $tipoNombre . '</small>';
                    ?>
                </td>
                <td class="align-middle" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= $b['descripcion'] ?>"><?= $b['descripcion'] ?></td>
                <td class="text-center align-middle"><span class="text-dark fw-medium"><?= $b['marca'] ?></span></td>
                <td class="text-center align-middle"><span class="text-muted"><?= $b['modelo'] ?></span></td>
                <td class="text-center align-middle"><small class="font-monospace text-secondary"><?= $b['serie'] ?></small></td>
                <td class="text-center align-middle"><small class="text-info"><?= $b['nombre_local'] ?? '-' ?></small></td>
                <td class="text-center align-middle"><small><?= $b['nombre_departamento'] ?></small></td>
                <td class="text-center align-middle">
                    <?php
                    $estadoBadges = [
                        'asignado' => 'success',
                        'activo' => 'primary',
                        'mantenimiento' => 'warning',
                        'retirado' => 'danger',
                        'nuevo' => 'info',
                        'bueno' => 'success',
                        'regular' => 'warning',
                        'malo' => 'danger'
                    ];
                    $badgeClass = $estadoBadges[$b['estado']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $badgeClass ?>" style="min-width: 80px; padding: 0.35rem 0.6rem;"><?= ucfirst($b['estado']) ?></span>
                </td>
                <td class="text-center align-middle"><small class="text-dark"><?= $b['nombre_persona'] ?: '<span class="text-muted">-</span>' ?></small></td>
                <td class="text-center align-middle">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                        <a href="<?= base_url('bienes/' . $b['id']) ?>" class="btn btn-outline-info" data-bs-toggle="tooltip" title="Ver detalles" style="padding: 0.25rem 0.5rem;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= base_url('bienes/' . $b['id'] . '/edit') ?>" class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Editar" style="padding: 0.25rem 0.5rem;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" onclick="abrirModalBaja(<?= $b['id'] ?>)" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Dar de baja" style="padding: 0.25rem 0.5rem;">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal para dar de baja un bien -->
<div class="modal fade" id="modalBaja" tabindex="-1" role="dialog" aria-labelledby="modalBajaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBajaLabel">Dar de Baja un Bien</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formBaja" action="<?= base_url('bienes/desactivar') ?>" method="post"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="bien_id" name="bien_id">
                    <div class="form-group">
                        <label for="motivo_baja">Motivo de la Baja</label>
                        <textarea class="form-control" id="motivo_baja" name="motivo_baja" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="usuario_baja">Usuario que da la Baja</label>
                        <input type="text" class="form-control" id="usuario_baja" name="usuario_baja"
                            value="<?= session('usuario') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="foto_frente">Foto Frente</label>
                        <input type="file" class="form-control" id="foto_frente" name="foto_frente" accept="image/*"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="foto_lateral">Foto Lateral</label>
                        <input type="file" class="form-control" id="foto_lateral" name="foto_lateral" accept="image/*"
                            required>
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
    function confirmarEliminacion(id) {
        if (confirm("¿Estás seguro de que deseas eliminar este bien?")) {
            window.location.href = "<?= base_url('bienes/desactivar/') ?>" + id;

        }
    }

    function abrirModalBaja(id) {
        document.getElementById('bien_id').value = id; // Pasar el ID al modal 
        $('#modalBaja').modal('show'); // Mostrar el modal 
    } 
</script>


<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>

<script>

    $(function () {

        // 🔍 Filtros personalizados para DataTables
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                // Obtener el nodo de la fila para acceder a los atributos data-*
                const row = settings.aoData[dataIndex].nTr;
                const $row = $(row);
                
                // Filtro de tipo
                const tipoFiltro = $('#filtro_tipo').val();
                if (tipoFiltro) {
                    const tipoValor = $row.find('td[data-tipo]').attr('data-tipo');
                    
                    // Filtro especial: Monitores con procesador (All in One mal clasificados)
                    if (tipoFiltro === 'monitor_con_procesador') {
                        // Solo mostrar monitores que cumplan criterios específicos
                        if (tipoValor !== 'monitor') {
                            return false;
                        }
                        
                        const procesadorValor = ($row.data('procesador') || '').toString().trim().toUpperCase();
                        const descripcionValor = (data[3] || '').toString().toUpperCase();
                        
                        // Palabras clave que indican que es All in One
                        const palabrasClave = [
                            'PROCESADOR INTEGRADO',
                            'ALL IN ONE',
                            'ALL-IN-ONE',
                            'AIO',
                            'CON PROCESADOR',
                            'COMPUTADORA TODO EN UNO'
                        ];
                        
                        // Verificar si tiene descripción con palabras clave
                        const tieneDescripcionAIO = palabrasClave.some(palabra => 
                            descripcionValor.includes(palabra)
                        );
                        
                        // Verificar si tiene procesador real (no vacío, no "NO APLICA")
                        const tieneProcesador = procesadorValor !== '' && 
                                               procesadorValor !== 'NO APLICA';
                        
                        // Solo mostrar si cumple AL MENOS UNA condición
                        return (tieneProcesador || tieneDescripcionAIO);
                    } else {
                        // Filtro normal por tipo
                        if (tipoValor !== tipoFiltro) return false;
                    }
                }
                
                // Filtro de procesador
                const procesadorFiltro = $('#filtro_procesador').val();
                if (procesadorFiltro) {
                    const procesadorValor = $row.data('procesador');
                    if (procesadorValor !== procesadorFiltro) return false;
                }
                
                // Filtro de memoria
                const memoriaFiltro = $('#filtro_memoria').val();
                if (memoriaFiltro) {
                    const memoriaValor = $row.data('memoria');
                    if (memoriaValor !== memoriaFiltro) return false;
                }
                
                // Filtro de tipo de disco
                const discoFiltro = $('#filtro_disco').val();
                if (discoFiltro) {
                    const discoValor = $row.data('disco');
                    if (discoValor !== discoFiltro) return false;
                }

                // Filtro de almacenamiento (espacio_disco)
                const almacenamientoFiltro = $('#filtro_almacenamiento').val();
                if (almacenamientoFiltro) {
                    const espacioValor = $row.data('espacio');
                    if (!espacioValor || !espacioValor.toString().includes(almacenamientoFiltro)) return false;
                }
                
                // Filtro de sistema operativo
                const soFiltro = $('#filtro_sistema_operativo').val();
                if (soFiltro) {
                    const soValor = $row.data('so');
                    if (soValor !== soFiltro) return false;
                }
                
                // Filtro de versión de Office
                const officeFiltro = $('#filtro_office').val();
                if (officeFiltro) {
                    const officeValor = $row.data('office');
                    if (officeValor !== officeFiltro) return false;
                }
                
                return true;
            }
        );

        var table = $('#bienesTable').DataTable({
            retrieve: true,
            pageLength: 25,
            order: [[0, 'desc']],
            responsive: true,
            scrollX: false,
            autoWidth: false,
            deferRender: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
                search: "Buscar en la tabla:",
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)"
            },
            columnDefs: [
                { targets: [0], width: '50px' },      // ID
                { targets: [1], width: '110px' },     // Código
                { targets: [2], width: '90px' },      // Tipo
                { targets: [3], width: 'auto' },      // Descripción
                { targets: [4], width: '100px' },     // Marca
                { targets: [5], width: '100px' },     // Modelo
                { targets: [6], width: '110px' },     // Serie
                { targets: [7], width: '90px' },      // Local
                { targets: [8], width: '110px' },     // Departamento
                { targets: [9], width: '90px' },      // Estado
                { targets: [10], width: '100px' },    // Usuario
                { targets: [11], width: '130px' }     // Acciones
            ]
        });

        // Mostrar tabla con un pequeño delay para evitar FOUC
        setTimeout(function() {
            $('#bienesTable').addClass('dt-ready');
            $('#tableLoading').addClass('hide');
        }, 100);

        // 🎯 Cargar opciones iniciales de filtros desde los datos de la tabla
        function cargarOpcionesFiltros() {
            const tipos = new Set();
            const marcas = new Set();
            const modelos = new Set();
            const locales = new Set();
            const departamentos = new Set();
            const procesadores = new Set();
            const memorias = new Set();
            const discos = new Set();
            const sistemasOperativos = new Set();
            const versionsOffice = new Set();

            // Recorrer todas las filas de la tabla (datos originales, no filtrados)
            table.rows().every(function() {
                const data = this.data();
                const node = this.node();
                
                // Obtener el tipo desde el atributo data-tipo
                const tipo = $(node).find('td[data-tipo]').attr('data-tipo');
                const marcaText = $(data[4]).text().trim(); // Marca
                const modeloText = $(data[5]).text().trim(); // Modelo
                const localText = $(data[7]).text().trim(); // Local
                const deptoText = $(data[8]).text().trim(); // Departamento

                if (tipo) tipos.add(tipo);
                if (marcaText) marcas.add(marcaText);
                if (modeloText) modelos.add(modeloText);
                if (localText) locales.add(localText);
                if (deptoText) departamentos.add(deptoText);

                // Obtener datos técnicos desde los atributos data-* de la fila
                const procesador = $(node).data('procesador');
                const memoria = $(node).data('memoria');
                const disco = $(node).data('disco');
                const so = $(node).data('so');
                const office = $(node).data('office');

                if (procesador) procesadores.add(procesador);
                if (memoria) memorias.add(memoria);
                if (disco) discos.add(disco);
                if (so) sistemasOperativos.add(so);
                if (office) versionsOffice.add(office);
            });

            // Llenar select de marcas
            $('#filtro_marca').empty().append('<option value="">Todas las marcas</option>');
            Array.from(marcas).sort().forEach(m => {
                if (m) $('#filtro_marca').append(`<option value="${m}">${m}</option>`);
            });

            // Llenar select de modelos
            $('#filtro_modelo').empty().append('<option value="">Todos los modelos</option>');
            Array.from(modelos).sort().forEach(m => {
                if (m) $('#filtro_modelo').append(`<option value="${m}">${m}</option>`);
            });

            // Llenar select de locales
            $('#filtro_local').empty().append('<option value="">Todos</option>');
            Array.from(locales).sort().forEach(l => {
                if (l) $('#filtro_local').append(`<option value="${l}">${l}</option>`);
            });

            // Llenar select de departamentos
            $('#filtro_departamento').empty().append('<option value="">Todos</option>');
            Array.from(departamentos).sort().forEach(d => {
                if (d) $('#filtro_departamento').append(`<option value="${d}">${d}</option>`);
            });

            // Llenar select de procesadores
            $('#filtro_procesador').empty().append('<option value="">Todos</option>');
            Array.from(procesadores).sort().forEach(p => {
                if (p) $('#filtro_procesador').append(`<option value="${p}">${p}</option>`);
            });

            // Llenar select de memoria
            $('#filtro_memoria').empty().append('<option value="">Todas</option>');
            Array.from(memorias).sort((a, b) => {
                const numA = parseInt(a);
                const numB = parseInt(b);
                return numA - numB;
            }).forEach(m => {
                if (m) $('#filtro_memoria').append(`<option value="${m}">${m}</option>`);
            });

            // Llenar select de discos
            $('#filtro_disco').empty().append('<option value="">Todos</option>');
            Array.from(discos).sort().forEach(d => {
                if (d) $('#filtro_disco').append(`<option value="${d}">${d}</option>`);
            });

            // Llenar select de sistemas operativos
            $('#filtro_sistema_operativo').empty().append('<option value="">Todos</option>');
            Array.from(sistemasOperativos).sort().forEach(so => {
                if (so) $('#filtro_sistema_operativo').append(`<option value="${so}">${so}</option>`);
            });

            // Llenar select de versiones de Office
            $('#filtro_office').empty().append('<option value="">Todas</option>');
            Array.from(versionsOffice).sort().forEach(o => {
                if (o) $('#filtro_office').append(`<option value="${o}">${o}</option>`);
            });
        }

        // 🔄 Actualizar opciones de marca y modelo según el tipo seleccionado
        function actualizarFiltrosEnCascada() {
            const tipoSeleccionado = $('#filtro_tipo').val();
            const marcaSeleccionada = $('#filtro_marca').val();

            const marcas = new Set();
            const modelos = new Set();

            // Filtrar según el tipo
            table.rows().every(function() {
                const data = this.data();
                const node = this.node();
                
                // Obtener el tipo desde el atributo data-tipo
                const tipo = $(node).find('td[data-tipo]').attr('data-tipo');
                const marcaText = $(data[4]).text().trim(); // Marca
                const modeloText = $(data[5]).text().trim(); // Modelo

                // Si hay tipo seleccionado, solo considerar bienes de ese tipo
                if (!tipoSeleccionado || tipo === tipoSeleccionado) {
                    if (marcaText) marcas.add(marcaText);
                    
                    // Si hay marca seleccionada, solo mostrar modelos de esa marca
                    if (!marcaSeleccionada || marcaText === marcaSeleccionada) {
                        if (modeloText) modelos.add(modeloText);
                    }
                }
            });

            // Actualizar select de marcas
            const marcaActual = $('#filtro_marca').val();
            $('#filtro_marca').empty().append('<option value="">Todas las marcas</option>');
            Array.from(marcas).sort().forEach(m => {
                if (m) $('#filtro_marca').append(`<option value="${m}">${m}</option>`);
            });
            if (marcas.has(marcaActual)) {
                $('#filtro_marca').val(marcaActual);
            }

            // Actualizar select de modelos
            const modeloActual = $('#filtro_modelo').val();
            $('#filtro_modelo').empty().append('<option value="">Todos los modelos</option>');
            Array.from(modelos).sort().forEach(m => {
                if (m) $('#filtro_modelo').append(`<option value="${m}">${m}</option>`);
            });
            if (modelos.has(modeloActual)) {
                $('#filtro_modelo').val(modeloActual);
            }
        }

        // Cargar opciones iniciales
        cargarOpcionesFiltros();

        // � Alternar ícono del chevron al colapsar/expandir
        $('#filtrosPanel').on('show.bs.collapse shown.bs.collapse', function () {
            $('#filtrosChevron').removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }).on('hide.bs.collapse hidden.bs.collapse', function () {
            $('#filtrosChevron').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        // 👁️ Mostrar/ocultar filtros técnicos según el tipo de bien seleccionado
        function actualizarVisibilidadFiltrosTecnicos() {
            const tipoSeleccionado = $('#filtro_tipo').val();
            
            // Tipos que requieren campos técnicos (procesador, memoria, disco, SO, office)
            const tiposConCamposTecnicos = ['computadora', 'laptop', 'tablet', 'servidor'];
            
            if (tiposConCamposTecnicos.includes(tipoSeleccionado)) {
                $('.filtro-tecnico').slideDown(300);
            } else if (tipoSeleccionado === '') {
                // Si no hay tipo seleccionado, mostrar todos los filtros
                $('.filtro-tecnico').slideDown(300);
            } else {
                // Ocultar y limpiar filtros técnicos para otros tipos
                $('.filtro-tecnico').slideUp(300);
                $('#filtro_procesador, #filtro_memoria, #filtro_disco, #filtro_almacenamiento, #filtro_sistema_operativo, #filtro_office').val('');
            }
        }

        // Mostrar filtros técnicos al cargar si no hay filtro de tipo
        actualizarVisibilidadFiltrosTecnicos();

        // 🔍 Aplicar filtros cuando cambie el tipo
        $('#filtro_tipo').on('change', function() {
            actualizarVisibilidadFiltrosTecnicos();
            actualizarFiltrosEnCascada();
            aplicarFiltros();
        });

        // Cuando cambie la marca, actualizar modelos
        $('#filtro_marca').on('change', function() {
            actualizarFiltrosEnCascada();
            aplicarFiltros();
        });

        // 🔍 Función para aplicar todos los filtros
        function aplicarFiltros() {
            // Limpiar filtros anteriores de columnas
            table.columns().search('');

            // Aplicar filtro por columna (excepto tipo que usa filtro personalizado)
            const filtros = {
                1: $('#filtro_codigo').val(),        // Código
                // 2: tipo - usa filtro personalizado
                3: $('#filtro_descripcion').val(),   // Descripción
                4: $('#filtro_marca').val(),         // Marca
                5: $('#filtro_modelo').val(),        // Modelo
                6: $('#filtro_serie').val(),         // Serie
                7: $('#filtro_local').val(),         // Local
                8: $('#filtro_departamento').val(),  // Departamento
                9: $('#filtro_estado').val(),        // Estado
                10: $('#filtro_usuario').val()       // Usuario
            };

            Object.keys(filtros).forEach(col => {
                if (filtros[col]) {
                    table.column(col).search(filtros[col]);
                }
            });

            // Redibujar la tabla (esto activa el filtro personalizado del tipo)
            table.draw();
        }

        // Aplicar filtros en todos los inputs
        $('#filtro_codigo, #filtro_descripcion, #filtro_serie, #filtro_usuario, #filtro_fecha, #filtro_almacenamiento').on('keyup change', aplicarFiltros);
        $('#filtro_modelo, #filtro_local, #filtro_departamento, #filtro_estado, #filtro_garantia').on('change', aplicarFiltros);
        
        // Aplicar filtros en los nuevos campos técnicos (usan filtro personalizado)
        $('#filtro_procesador, #filtro_memoria, #filtro_disco, #filtro_sistema_operativo, #filtro_office').on('change', function() {
            table.draw(); // Redibujar para activar el filtro personalizado
        });

        // 🧹 Limpiar todos los filtros
        $('#clearFilters').on('click', function() {
            $('#filtro_codigo, #filtro_descripcion, #filtro_serie, #filtro_usuario, #filtro_fecha, #filtro_almacenamiento').val('');
            $('#filtro_tipo, #filtro_marca, #filtro_modelo, #filtro_local, #filtro_departamento, #filtro_estado, #filtro_garantia').val('');
            $('#filtro_procesador, #filtro_memoria, #filtro_disco, #filtro_sistema_operativo, #filtro_office').val('');
            
            table.columns().search('').draw();
            cargarOpcionesFiltros();
        });

        // 📥 Exportar Excel con filtros
        $('#btnExportarFiltrado').on('click', function () {
            let params = {};

            // 1. BÚSQUEDA GLOBAL
            params.search = table.search() || '';

            // 2. FILTROS DESDE EL PANEL - Información Básica
            params.cod_patrimonial = $('#filtro_codigo').val() || '';
            params.tipo_bien = $('#filtro_tipo').val() || '';
            params.descripcion = $('#filtro_descripcion').val() || '';
            params.marca = $('#filtro_marca').val() || '';
            params.modelo = $('#filtro_modelo').val() || '';
            params.serie = $('#filtro_serie').val() || '';
            
            // 3. FILTROS TÉCNICOS
            params.procesador = $('#filtro_procesador').val() || '';
            params.memoria = $('#filtro_memoria').val() || '';
            params.tipo_disco = $('#filtro_disco').val() || '';
            params.espacio_disco = $('#filtro_almacenamiento').val() || '';
            params.sistema_operativo = $('#filtro_sistema_operativo').val() || '';
            params.office = $('#filtro_office').val() || '';
            
            // 4. FILTROS DE UBICACIÓN Y ESTADO
            params.local = $('#filtro_local').val() || '';
            params.departamento = $('#filtro_departamento').val() || '';
            params.estado = $('#filtro_estado').val() || '';
            params.usuario = $('#filtro_usuario').val() || '';
            params.fecha_adquisicion = $('#filtro_fecha').val() || '';
            params.estado_garantia = $('#filtro_garantia').val() || '';

            // ...existing code...
            console.log('Params enviados:', params);

            // 6. Generar URL y descargar
            let qs = $.param(params);
            let url = "<?= base_url('bienes/exportarFiltrado') ?>?" + qs;

            console.log('URL:', url);
            window.location = url;
        });

        // Autocompletado de usuario en filtro
        const filtroUsuarioInput = document.getElementById('filtro_usuario');
        const filtroUsuarioSuggestions = document.getElementById('filtroUsuarioSuggestions');
        let usuarioDebounceTimer;

        filtroUsuarioInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(usuarioDebounceTimer);

            if (query.length < 3) {
                filtroUsuarioSuggestions.style.display = 'none';
                return;
            }

            usuarioDebounceTimer = setTimeout(() => {
                fetch(`<?= base_url('bienes/getUsuariosSugeridos') ?>?usuario=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        filtroUsuarioSuggestions.innerHTML = '';

                        if (data.length === 0) {
                            filtroUsuarioSuggestions.innerHTML = '<li class="list-group-item list-group-item-sm text-muted">No se encontraron usuarios</li>';
                            filtroUsuarioSuggestions.style.display = 'block';
                            return;
                        }

                        data.forEach(usuario => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action list-group-item-sm';
                            li.style.cursor = 'pointer';
                            li.style.padding = '0.5rem 0.75rem';
                            li.textContent = usuario.nombre_completo;

                            li.addEventListener('click', function() {
                                filtroUsuarioInput.value = this.textContent;
                                filtroUsuarioSuggestions.style.display = 'none';
                                // Aplicar filtros automáticamente
                                table.draw();
                            });

                            filtroUsuarioSuggestions.appendChild(li);
                        });

                        filtroUsuarioSuggestions.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error al buscar usuarios:', error);
                        filtroUsuarioSuggestions.style.display = 'none';
                    });
            }, 300);
        });

        // Ocultar sugerencias de usuario al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (e.target !== filtroUsuarioInput && e.target !== filtroUsuarioSuggestions) {
                filtroUsuarioSuggestions.style.display = 'none';
            }
        });

    });
</script>

<!-- Modal Subida Masiva -->
<div class="modal fade" id="modalSubidaMasiva" tabindex="-1" role="dialog" aria-labelledby="modalSubidaMasivaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSubidaMasivaLabel">
                    <i class="fas fa-upload"></i> Subida Masiva de Bienes
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('bienes/subida_masiva') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Esta herramienta permite:</strong>
                        <ul class="mt-2 mb-0">
                            <li><strong>Crear nuevos bienes:</strong> Agrega filas con código patrimonial único (deja el ID vacío)</li>
                            <li><strong>Actualizar bienes existentes:</strong> Modifica los datos y se actualizarán solo los campos cambiados</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Instrucciones:</strong>
                        <ol class="mt-2 mb-0">
                            <li>Descarga la plantilla CSV con el botón "Descargar Plantilla CSV"</li>
                            <li><strong>Para actualizar:</strong> Edita los campos que necesites modificar</li>
                            <li><strong>Para crear nuevos:</strong> Agrega filas al final con:
                                <ul>
                                    <li>ID vacío o cualquier valor no numérico</li>
                                    <li>Código patrimonial único (obligatorio)</li>
                                    <li>Los demás campos según necesites</li>
                                </ul>
                            </li>
                            <li>Guarda el archivo como CSV (UTF-8)</li>
                            <li>Sube el archivo aquí</li>
                        </ol>
                        <p class="mt-2 mb-0"><strong>Importante:</strong> El sistema identifica bienes por código patrimonial. Si el código ya existe, actualizará ese bien.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="archivo_csv">Seleccionar archivo CSV</label>
                        <input type="file" class="form-control-file" id="archivo_csv" name="archivo_csv" accept=".csv" required>
                        <small class="form-text text-muted">Solo se permiten archivos CSV</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Subir y Procesar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>