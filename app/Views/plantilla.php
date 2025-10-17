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
                        <a class="collapse-item" href="<?= base_url("index.php/bienes") ?>">Bienes</a>
                        <a class="collapse-item" href="<?= base_url("index.php/personas") ?>">Personal</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Inventario</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">

                        <a class="collapse-item" href="<?= base_url("index.php/inventario2025") ?>">Inventario</a>
                        <a class="collapse-item" href="<?= base_url("asignacion") ?>">Movimientos</a>
                        <a class="collapse-item" href="<?= base_url("index.php/baja") ?>">Baja</a>
                        <a class="collapse-item" href="<?= base_url("index.php/ip") ?>"><i class="fas fa-network-wired mr-1"></i> IPs</a>

                    </div>
                </div>
            </li>

            <!-- Divider -->
            <!--<hr class="sidebar-divider">-->

            <!-- Heading -->
            <!--<div class="sidebar-heading">
                Reportes
            </div>-->

            <!-- Nav Item - Pages Collapse Menu -->
            <!--<li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="sb2/login.html">Login</a>
                        <a class="collapse-item" href="sb2/register.html">Register</a>
                        <a class="collapse-item" href="sb2/forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="sb2/404.html">404 Page</a>
                        <a class="collapse-item" href="sb2/blank.html">Blank Page</a>
                    </div>
                </div>
            </li>-->

            <!-- Nav Item - Charts -->
            <!--<li class="nav-item">
                <a class="nav-link" href="sb2/charts.html">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>-->

            <!-- Nav Item - Tables -->
            <!--<li class="nav-item">
                <a class="nav-link" href="sb2/tables.html">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables</span></a>
            </li>-->

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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
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
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>



    <!-- Cargar jQuery localmente (asegúrate de que sea la versión correcta) -->
    <script src="<?= base_url("./sb2/vendor/jquery/jquery.min.js") ?>"></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- Cargar Bootstrap después de jQuery -->
    <script src="<?= base_url("./sb2/vendor/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>

    

    <!-- Cargar DataTables JavaScript después de jQuery -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>


    <!-- Cargar otros scripts de plugins -->
    <script src="<?= base_url("./sb2/vendor/jquery-easing/jquery.easing.min.js") ?>"></script>
    <script src="<?= base_url("./sb2/js/sb-admin-2.min.js") ?>"></script>
    <script src="<?= base_url("./sb2/vendor/chart.js/Chart.min.js") ?>"></script>
    <script src="<?= base_url("./sb2/js/demo/chart-area-demo.js") ?>"></script>
    <script src="<?= base_url("./sb2/js/demo/chart-pie-demo.js") ?>"></script>

    <!-- Scripts de Select2 (antes de tu script.js si lo usas) -->

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // ==============================
    // 📌 Inicializar DataTables
    // ==============================
    var table = $('#bienesTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[5, "desc"]],
        "responsive": true
    });

    // Mostrar / Ocultar filtros
    $('#toggleFilters').on('click', function() {
        $('#filterContainer').toggle();
        $(this).text($('#filterContainer').is(':visible') ? 'Ocultar Filtros' : 'Mostrar Filtros');
    });

    // Filtro por Rango de Fechas
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var fechaDesde = $('#filterFechaDesde').val();
        var fechaHasta = $('#filterFechaHasta').val();
        var fechaAdquisicion = data[6] || '';

        if (fechaAdquisicion === '') return true;

        var fechaAdq = new Date(fechaAdquisicion);
        var desde = fechaDesde ? new Date(fechaDesde) : null;
        var hasta = fechaHasta ? new Date(fechaHasta) : null;

        return (desde === null || fechaAdq >= desde) && (hasta === null || fechaAdq <= hasta);
    });

    $('#filterFechaDesde, #filterFechaHasta').on('change', function() {
        table.draw();
    });

    $('#filterDescripcion').on('keyup', function() {
        table.column(1).search(this.value).draw();
    });

    $('#filterEstado').on('change', function() {
        table.column(5).search(this.value).draw();
    });

    $('#clearFilters').on('click', function() {
        $('#filterFechaDesde, #filterFechaHasta, #filterDescripcion').val('');
        $('#filterEstado').val('');
        table.search('').columns().search('').draw();
    });

    // ==============================
    // 📌 Autocompletar usuario
    // ==============================
    $('#usuario').on('input', function() {
        $('#usuarioId').val('');
    }).on('keyup', function() {
        let usuario = $(this).val().trim();

        if (usuario.length >= 3) {
            var url = window.location.pathname.includes('movimientos') ?
                "<?= base_url('movimientos/getUsuariosSugeridos'); ?>" :
                "<?= base_url('bienes/getUsuariosSugeridos'); ?>";

            $.ajax({
                url: url,
                method: "GET",
                data: { usuario: usuario },
                dataType: "json",
                success: function(response) {
                    $('#usuarioSuggestions').empty().hide();

                    if (response.length > 0) {
                        response.forEach(function(persona) {
                            $('#usuarioSuggestions').append(
                                `<li class="list-group-item suggestion-item" data-id="${persona.id}">${persona.nombre_completo}</li>`
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

    $(document).on('click', '.suggestion-item', function() {
        var nombreUsuario = $(this).text();
        var idUsuario = $(this).data('id');
        $('#usuario').val(nombreUsuario);
        $('#usuarioId').val(idUsuario);
        $('#usuarioSuggestions').hide();
    });

    $('form').on('submit', function(event) {
        if ($('#usuario').val().trim() === '' || $('#usuarioId').val() === '') {
            alert('Debe seleccionar un usuario válido.');
            event.preventDefault();
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#usuarioSuggestions, #usuario').length) {
            $('#usuarioSuggestions').hide();
        }
    });

    // ==============================
    // 📌 Bienes con Select2 (asignar y retirar)
    // ==============================
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
                        tipo: tipo,
                        persona: $('#usuarioId').val() // 👈 filtra por usuario seleccionado
                    };
                },
                processResults: function (data) {
                    return data;
                }
            },
            templateResult: function (bien) {
                if (!bien.id) return bien.text;
                let estado = bien.estado ? `📌 ${bien.estado}` : '';
                return $(`<span>${bien.text}<br><small>${estado}</small></span>`);
            }
        });

        // Añadir a la lista
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

    // Inicializar
    initSelect2('#buscador_asignar', 'asignacion', 'lista_asignar', 'bienes_asignar');
    initSelect2('#buscador_retirar', 'retiro', 'lista_retirar', 'bienes_retirar');

    // Eliminar item
    $(document).on('click', '.remove-bien', function () {
        let id = $(this).data('id');
        let lista = $(this).data('lista');
        $('#bien_' + lista + '_' + id).remove();
    });

    // Mostrar/ocultar según tipo
    $('#tipo_movimiento').change(function () {
        let tipo = $(this).val();
        $('#contenedor_asignar').toggle(tipo === 'asignacion' || tipo === 'cambio');
        $('#contenedor_retirar').toggle(tipo === 'retiro' || tipo === 'cambio');
    }).trigger('change');
});
</script>





<?= $this->renderSection('scripts') ?>
</body>

</html>