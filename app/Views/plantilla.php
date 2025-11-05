<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inventario OTI - Tribunal Constitucional</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url("./sb2/vendor/fontawesome-free/css/all.min.css") ?>" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url("./sb2/css/sb-admin-2.min.css") ?>" rel="stylesheet">

    <!-- Incluir los archivos CSS y JS de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url() ?>">
                <img class="logo_imagen" src="<?= base_url("sb2/img/tc_logo_superior.png") ?>" width="120%">
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?= base_url() ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Inicio</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                General
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Configuracion</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">General</h6>

                        <a class="collapse-item" href="<?= base_url("index.php/personas") ?>">Personal</a>
                         <a class="collapse-item" href="<?= base_url("index.php/proveedor") ?>">Proveedores</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - bienes -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url("index.php/bienes"); ?>">
                    <i class="fas fa-desktop"></i>
                    <span>Bienes</span>
                </a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" aria-controls="collapseThree">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Soporte Técnico</span>
                </a>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                         <a class="collapse-item" href="<?= base_url("index.php/mantenimiento") ?>">Mantenimiento</a>
                         <a class="collapse-item" href="<?= base_url("index.php/#") ?>">Optimizacion</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Inventario</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">

                        <a class="collapse-item" href="<?= base_url("index.php/bienes") ?>">Inventario</a>
                        <a class="collapse-item" href="<?= base_url('movimientos') ?>">Movimientos</a>
                        <a class="collapse-item" href="<?= base_url("index.php/baja") ?>">Baja</a>
                        <a class="collapse-item" href="<?= base_url("index.php/ip") ?>"><i class="fas fa-network-wired mr-1"></i> IPs</a>

                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Seguridad
            </div>

            <!-- Nav Item - Usuarios -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('usuarios'); ?>">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->

        </ul>
        <!-- End of Sidebar -->


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Buscar"
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small user-nombre"><?= session('nombre') ?></span>
                                <?php
                                $foto = session('foto') ?? null;
                                $imgSrc = $foto ? base_url('uploads/usuarios/' . $foto) : base_url('img/avatar-default.png');
                                ?>
                                <img class="img-profile rounded-circle" src="<?= $imgSrc ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalPerfil">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= base_url('/logout') ?>">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <?php echo $this->renderSection('contenido'); ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Tribunal Constitucional del Perú</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Desea salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione “Cerrar sesión” a continuación si está listo para finalizar su sesión
                    actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="<?= base_url('/logout') ?>">Salir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Perfil -->
    <div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mi Perfil</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna Foto -->
                        <div class="col-md-4 text-center">
                            <?php
                            $foto = session('foto');
                            $avatar = $foto ? base_url('uploads/usuarios/' . $foto)
                                : base_url('img/avatar-default.png');
                            ?>
                            <img id="previewFoto" src="<?= $avatar ?>" class="img-fluid rounded-circle mb-3"
                                style="max-width:180px;">
                            <form id="formFoto" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="usuario_id" value="<?= session('usuario_id') ?>">
                                <div class="form-group">
                                    <label for="foto">Cambiar foto (JPG/PNG, máx 2MB)</label>
                                    <input id="inputFoto" type="file" name="foto" class="form-control-file"
                                        accept="image/*">
                                </div>
                                <button class="btn btn-primary btn-block" type="submit">Subir foto</button>
                            </form>
                        </div>

                        <!-- Columna Datos -->
                        <div class="col-md-8">
                            <form id="formDatos">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label>Usuario</label>
                                    <input type="text" class="form-control" value="<?= esc(session('usuario')) ?>"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input name="nombre" type="text" class="form-control"
                                        value="<?= esc(session('nombre')) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Correo</label>
                                    <input name="correo" type="email" class="form-control"
                                        value="<?= esc(session('correo')) ?>">
                                </div>
                                <button class="btn btn-success" type="submit">Guardar cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>




    <!-- Cargar jQuery localmente (asegúrate de que sea la versión correcta) -->
    <script src="<?= base_url("./sb2/vendor/jquery/jquery.min.js") ?>"></script>

    <!-- jQuery UI (necesario para .autocomplete) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- Cargar Bootstrap después de jQuery -->
    <script src="<?= base_url("./sb2/vendor/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>



    <!-- Cargar DataTables JavaScript después de jQuery -->
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>


    <!-- Cargar otros scripts de plugins -->
    <script src="<?= base_url("./sb2/vendor/jquery-easing/jquery.easing.min.js") ?>"></script>
    <script src="<?= base_url("./sb2/js/sb-admin-2.min.js") ?>"></script>
    <script src="<?= base_url("./sb2/vendor/chart.js/Chart.min.js") ?>"></script>
    <script src="<?= base_url("./sb2/js/demo/chart-area-demo.js") ?>"></script>
    <script src="<?= base_url("./sb2/js/demo/chart-pie-demo.js") ?>"></script>

    <!-- Scripts de Select2 (antes de tu script.js si lo usas) -->

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var table; // variable global accesible desde otros scripts

        $(document).ready(function () {
            // ==============================
            // Inicializar DataTable
            // ==============================
            table = $('#bienesTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                order: [[0, "asc"]],
                responsive: true,
                orderCellsTop: true,
                fixedHeader: true,

                columnDefs: [
                    { targets: [5, 9], visible: false, searchable: true }
                    // 🔹 5 = "Serie", 9 = "Fecha de compra"
                ],

                initComplete: function () {
                    var api = this.api();

                    // ==============================
                    // Llenar selects dinámicos (marca, modelo, local, departamento)
                    // ==============================
                    api.columns([3, 4, 5, 6]).every(function () {
                        var column = this;
                        var select = $('select', column.header());
                        var dataSet = [];

                        column.data().each(function (d) {
                            if (d && !dataSet.includes(d)) dataSet.push(d);
                        });

                        dataSet.sort();
                        dataSet.forEach(function (d) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });

                        // 🔹 Evento al cambiar un filtro de columna
                        select.on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();

                            // 🔁 Actualizar filtros dependientes tras aplicar filtro
                            actualizarFiltrosDependientes();
                        });
                    });
                }
            });

            // ==============================
            //  Filtros por columna (segunda fila del thead)
            // ==============================
            $('#bienesTable thead tr:eq(1) th').each(function (i) {
                var input = $('input, select', this);
                if (input.length) {
                    $(input).on('keyup change', function () {
                        var val = this.value;
                        table.column(i).search(val).draw();
                        actualizarFiltrosDependientes(); // 🔁 actualiza dependientes también aquí
                    });
                }
            });

            // ==============================
            // Filtro por rango de fechas (externo)
            // ==============================
            $.fn.dataTable.ext.search.push(function (settings, data) {
                var fechaDesde = $('#filterFechaDesde').val();
                var fechaHasta = $('#filterFechaHasta').val();
                var fechaCompra = data[8] || ''; // Columna 8 = Fecha de compra

                if (fechaCompra === '') return true;
                var fecha = new Date(fechaCompra);
                var desde = fechaDesde ? new Date(fechaDesde) : null;
                var hasta = fechaHasta ? new Date(fechaHasta) : null;

                return (desde === null || fecha >= desde) && (hasta === null || fecha <= hasta);
            });

            $('#filterFechaDesde, #filterFechaHasta').on('change', function () {
                table.draw();
            });

            // ==============================
            // Filtros externos adicionales
            // ==============================
            $('#filterDescripcion').on('keyup', function () {
                table.column(2).search(this.value).draw();
                actualizarFiltrosDependientes();
            });

            $('#filterEstado').on('change', function () {
                table.column(7).search(this.value).draw();
                actualizarFiltrosDependientes();
            });

            // ==============================
            // Limpiar filtros
            // ==============================
            $(document).on('click', '#clearFilters', function () {
                console.log('🧹 Limpiando filtros...');

                $('#filterFechaDesde, #filterFechaHasta, #filterDescripcion').val('');
                $('#filterEstado').val('');

                $('#bienesTable thead tr:eq(1) th').each(function () {
                    var $el = $('input, select', this);
                    if ($el.length) {
                        if ($el.is('select')) {
                            $el.prop('selectedIndex', 0).trigger('change');
                        } else {
                            $el.val('').trigger('keyup').trigger('change');
                        }
                    }
                });

                if (typeof table !== 'undefined' && table) {
                    table.search('').columns().search('').draw();
                }

                // 🔁 Recalcular los selects dependientes después de limpiar
                setTimeout(actualizarFiltrosDependientes, 300);
            });

            // ==============================
            // Mostrar / Ocultar filtros externos
            // ==============================
            $('#toggleFilters').on('click', function () {
                $('#filterContainer').toggle();
                $(this).text($('#filterContainer').is(':visible') ? 'Ocultar Filtros' : 'Mostrar Filtros');
            });

            // ==============================
            // Autocompletado descripcion
            // ==============================
            $('#bienesTable thead tr:eq(1) th:eq(2) input').autocomplete({
                minLength: 3,
                source: function (request, response) {
                    $.ajax({
                        url: "<?= base_url('bienes/buscarDescripcion') ?>",
                        dataType: "json",
                        data: { term: request.term },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    table.column(2).search(ui.item.value).draw();
                    actualizarFiltrosDependientes();
                }
            });

            // ==============================
            // Cargar lista de filtros desde backend
            // ==============================
            cargarFiltrosDinamicos();

            // 🔁 Actualiza dependientes tras cada renderizado del DataTable
            table.on('draw', function () {
                actualizarFiltrosDependientes();
            });
        });

        // =========================================================
        // Cargar filtros dinámicos por AJAX
        // =========================================================
        function cargarFiltrosDinamicos() {
            const baseUrl = "<?= base_url() ?>";

            $.getJSON(baseUrl + '/bienes/marcas', function (data) {
                let select = $('#bienesTable thead tr:eq(1) th:eq(3) select');
                select.empty().append('<option value="">Todos</option>');
                data.forEach(item => {
                    if (item.marca) select.append(`<option value="${item.marca}">${item.marca}</option>`);
                });
            });

            $.getJSON(baseUrl + '/bienes/modelos', function (data) {
                let select = $('#bienesTable thead tr:eq(1) th:eq(4) select');
                select.empty().append('<option value="">Todos</option>');
                data.forEach(item => {
                    if (item.modelo) select.append(`<option value="${item.modelo}">${item.modelo}</option>`);
                });
            });

            $.getJSON(baseUrl + '/bienes/locales', function (data) {
                let select = $('#bienesTable thead tr:eq(1) th:eq(5) select');
                select.empty().append('<option value="">Todos</option>');
                data.forEach(local => {
                    select.append(`<option value="${local.nombre}">${local.nombre}</option>`);
                });
            });

            $.getJSON(baseUrl + '/bienes/departamentos', function (data) {
                let select = $('#bienesTable thead tr:eq(1) th:eq(6) select');
                select.empty().append('<option value="">Todos</option>');
                data.forEach(dep => {
                    select.append(`<option value="${dep.nombre}">${dep.nombre}</option>`);
                });
            });
        }

        // ==================================================
        // 🔁 Filtros dependientes (discriminantes)
        // ==================================================
        function actualizarFiltrosDependientes() {
            if (typeof table === 'undefined' || !table.columns) return;

            // Índices reales del DataTable (ajusta si cambia el orden)
            const selectsCols = [3, 4, 6, 7]; // Marca, Modelo, Local, Departamento

            selectsCols.forEach(function (colIdx) {
                const column = table.column(colIdx);

                // Buscar el select correcto (visible o no)
                const visibleIndex = column.index('visible');
                let select = $('#bienesTable thead tr:eq(1) th:visible').eq(visibleIndex).find('select');
                if (!select.length) {
                    select = $('#bienesTable thead tr:eq(1) th').eq(colIdx).find('select');
                }
                if (!select.length) return;

                // Guardar valor seleccionado
                const valActual = select.val();

                // ✅ Tomar datos SOLO de filas visibles (filtradas actualmente)
                const colData = column.data({ search: 'applied' }).toArray();

                // Normalizar (sin nulos ni vacíos)
                const valores = Array.from(new Set(
                    colData.map(v => (v ? v.trim() : '')).filter(v => v !== '')
                )).sort();

                // Rellenar el select
                select.empty().append('<option value="">Todos</option>');
                valores.forEach(v => select.append(`<option value="${v}">${v}</option>`));

                // Restaurar selección si sigue existiendo
                if (valActual && select.find(`option[value="${valActual}"]`).length) {
                    select.val(valActual);
                } else {
                    select.val('');
                }
            });
        }




    </script>


    <!-- ============================== -->
    <!--  Script de perfil (foto y datos) -->
    <!-- ============================== -->
    <script>
        const BASE_URL = "<?= base_url() ?>";

        $(function () {
            // Preview de foto
            $('#inputFoto').on('change', function () {
                var file = this.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Error', 'Archivo demasiado grande (máx 2MB)', 'error');
                    $(this).val('');
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#previewFoto').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            });

            // Subir foto por AJAX
            $('#formFoto').on('submit', function (e) {
                e.preventDefault();
                var fd = new FormData(this);
                $.ajax({
                    url: BASE_URL + '/perfil/foto',
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.ok) {
                            Swal.fire('Listo', resp.msg, 'success');
                            var imgTop = $('img.img-profile');
                            if (imgTop.length) {
                                imgTop.attr('src', resp.url + '?t=' + new Date().getTime());
                            }
                        } else {
                            Swal.fire('Error', resp.msg || 'Error al subir', 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Error al subir (ver consola)', 'error');
                        console.error(xhr);
                    }
                });
            });

            // Guardar datos perfil
            $('#formDatos').on('submit', function (e) {
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    url: BASE_URL + '/perfil/guardar',
                    method: 'POST',
                    data,
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.ok) {
                            Swal.fire('Listo', resp.msg, 'success').then(() => {
                                $('span.user-nombre').text($('input[name="nombre"]').val());
                            });
                        } else {
                            Swal.fire('Error', resp.msg || 'No se pudo actualizar', 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Error al guardar (ver consola)', 'error');
                        console.error(xhr);
                    }
                });
            });
        });
    </script>




    <!-- ============================== -->
    <!--  Script de asignación / retiro (Select2) -->
    <!-- ============================== -->
    <script>
        $(document).ready(function () {
            // Autocompletar usuario
            $('#usuario').on('input', function () {
                $('#usuarioId').val('');
            }).on('keyup', function () {
                let usuario = $(this).val().trim();
                if (usuario.length >= 3) {
                    var url = window.location.pathname.includes('movimientos')
                        ? "<?= base_url('movimientos/getUsuariosSugeridos'); ?>"
                        : "<?= base_url('bienes/getUsuariosSugeridos'); ?>";
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: { usuario },
                        dataType: "json",
                        success: function (response) {
                            $('#usuarioSuggestions').empty().hide();
                            if (response.length > 0) {
                                response.forEach(function (persona) {
                                    $('#usuarioSuggestions').append(
                                        `<li class="list-group-item suggestion-item" data-id="${persona.id}">
                                    ${persona.nombre_completo}
                                </li>`
                                    );
                                });
                                $('#usuarioSuggestions').show();
                            }
                        }
                    });
                } else {
                    $('#usuarioSuggestions').hide();
                }
            });

            $(document).on('click', '.suggestion-item', function () {
                var nombreUsuario = $(this).text();
                var idUsuario = $(this).data('id');
                $('#usuario').val(nombreUsuario);
                $('#usuarioId').val(idUsuario);
                $('#usuarioSuggestions').hide();
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#usuarioSuggestions, #usuario').length) {
                    $('#usuarioSuggestions').hide();
                }
            });

            $('form').on('submit', function (event) {
                if ($('#usuario').val().trim() === '' || $('#usuarioId').val() === '') {
                    alert('Debe seleccionar un usuario válido.');
                    event.preventDefault();
                }
            });

            // Inicializar Select2
            function initSelect2(selector, tipo, listaId, inputName) {
                $(selector).select2({
                    placeholder: 'Buscar bien por código o descripción',
                    ajax: {
                        url: '<?= base_url("movimientos/buscarBienes") ?>',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                tipo,
                                persona: $('#usuarioId').val()
                            };
                        },
                        processResults: function (data) {
                            return data;
                        },
                        templateResult: function (bien) {
                            if (!bien.id) return bien.text;
                            let estado = bien.estado ? `📌 ${bien.estado}` : '';
                            return $(`<span>${bien.text}<br><small>${estado}</small></span>`);
                        }
                    }
                });

                $(selector).on('select2:select', function (e) {
                    let data = e.params.data;
                    if ($('#bien_' + listaId + '_' + data.id).length) return;
                    $('#' + listaId).append(`
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    id="bien_${listaId}_${data.id}">
                    ${data.text}
                    <input type="hidden" name="${inputName}[]" value="${data.id}">
                    <button type="button" class="btn btn-sm btn-danger remove-bien"
                        data-id="${data.id}" data-lista="${listaId}">❌</button>
                </li>
            `);
                });
            }

            initSelect2('#buscador_asignar', 'asignacion', 'lista_asignar', 'bienes_asignar');
            initSelect2('#buscador_prestar', 'prestamo', 'lista_prestar', 'bienes_prestar');
            initSelect2('#buscador_retirar', 'retiro', 'lista_retirar', 'bienes_retirar');

            $(document).on('click', '.remove-bien', function () {
                let id = $(this).data('id');
                let lista = $(this).data('lista');
                $('#bien_' + lista + '_' + id).remove();
            });

            $('#tipo_movimiento').change(function () {
                let tipo = $(this).val();
                $('#contenedor_asignar').toggle(tipo === 'asignacion' || tipo === 'cambio');
                $('#contenedor_prestar').toggle(tipo === 'prestamo' || tipo === 'cambio');
                $('#contenedor_retirar').toggle(tipo === 'retiro' || tipo === 'cambio');
            }).trigger('change');
        });
    </script>

    <?= $this->renderSection('scripts_datatable_ip') ?>
    <?= $this->renderSection('scripts') ?>
</body>

</html>