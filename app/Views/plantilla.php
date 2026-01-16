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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ===== MEJORAS MODERNAS PARA LA PLANTILLA ===== */
        
        /* Sidebar mejorado */
        .sidebar {
            background: linear-gradient(180deg, #8B1538 0%, #6B0F2A 100%) !important;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            padding: 1.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand-text {
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .sidebar .nav-item .nav-link {
            padding: 0.85rem 1rem;
            margin: 0.2rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar .nav-item .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(3px);
        }

        .sidebar .nav-item .nav-link.active {
            background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);
            box-shadow: 0 4px 15px rgba(196, 30, 58, 0.4);
        }

        .sidebar .nav-item .nav-link i {
            width: 24px;
            font-size: 1rem;
        }

        .sidebar-heading {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 1.25rem 1rem 0.5rem;
        }

        .sidebar-divider {
            border-color: rgba(255, 255, 255, 0.1) !important;
            margin: 1rem 0;
        }

        /* Collapse items */
        .sidebar .collapse-inner {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            margin: 0.5rem 0.75rem;
            padding: 0.5rem 0;
        }

        .sidebar .collapse-item {
            padding: 0.6rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border-radius: 0.35rem;
            margin: 0.2rem 0.5rem;
        }

        .sidebar .collapse-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding-left: 1.25rem;
        }

        /* Topbar mejorado */
        .topbar {
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08) !important;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%) !important;
        }

        .topbar .nav-item .nav-link {
            padding: 0.75rem 1rem;
            color: #5a5c69;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            position: relative;
        }

        .topbar .nav-item .nav-link:hover {
            background: rgba(196, 30, 58, 0.1);
            color: #c41e3a;
        }

        .topbar .nav-link .badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.3rem 0.5rem;
            position: absolute;
            top: 8px;
            right: 8px;
        }

        /* User dropdown mejorado */
        .topbar .nav-item.dropdown .dropdown-menu {
            border: none;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);
            color: white !important;
            padding-left: 1.75rem;
        }

        .dropdown-item i {
            width: 20px;
            opacity: 0.7;
        }

        .dropdown-item:hover i {
            opacity: 1;
        }

        /* Profile image */
        .img-profile {
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .img-profile:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        }

        /* Footer mejorado */
        .sticky-footer {
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%) !important;
            border-top: 1px solid #e3e6f0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .sticky-footer .copyright {
            color: #858796;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Scroll to top button */
        .scroll-to-top {
            background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);
            box-shadow: 0 4px 15px rgba(196, 30, 58, 0.4);
            transition: all 0.3s ease;
        }

        .scroll-to-top:hover {
            transform: scale(1.1) rotate(360deg);
            box-shadow: 0 6px 20px rgba(196, 30, 58, 0.6);
        }

        /* Modal mejorado */
        .modal-header {
            background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .modal-header .modal-title {
            font-weight: 600;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
            text-shadow: none;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-content {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-body {
            padding: 1.5rem;
        }

        /* Notification badge */
        .badge-danger {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Container fluid */
        .container-fluid {
            padding: 1.5rem;
            background: #f8f9fc;
        }

        /* Sidebar toggler */
        #sidebarToggle {
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        #sidebarToggle:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(180deg);
        }

        /* Topbar divider */
        .topbar-divider {
            width: 0;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            height: 2rem;
            margin: auto 1rem;
        }

        /* Notification dropdown */
        #listaNotificaciones .alert {
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        /* User name in topbar */
        .user-nombre {
            font-weight: 600;
        }

        /* Sidebar logo */
        .sidebar-brand-icon img {
            transition: all 0.3s ease;
        }

        .sidebar-brand:hover .sidebar-brand-icon img {
            transform: scale(1.05);
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex flex-column align-items-center justify-content-center" href="<?= base_url('inicio') ?>">
                <div class="sidebar-brand-icon">
                    <img src="<?= base_url('sb2/img/tc_logo_superior.png') ?>" alt="Logo TC" style="max-width: 200px; height: auto;">
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?= base_url() ?>">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Inicio</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Administración
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Configuración</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">General</h6>

                        <a class="collapse-item" href="<?= base_url("index.php/personas") ?>">
                            <i class="fas fa-users mr-2"></i> Personal
                        </a>
                        <a class="collapse-item" href="<?= base_url("index.php/proveedor") ?>">
                            <i class="fas fa-truck mr-2"></i> Proveedores
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - bienes -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url("index.php/bienes"); ?>">
                    <i class="fas fa-laptop"></i>
                    <span>Bienes</span>
                </a>
            </li>

            <!-- Nav Item - Licencias -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url("index.php/licencias"); ?>">
                    <i class="fas fa-key"></i>
                    <span>Licencias</span>
                </a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" aria-controls="collapseThree">
                    <i class="fas fa-fw fa-tools"></i>
                    <span>Soporte Técnico</span>
                </a>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="<?= base_url("index.php/mantenimiento") ?>">
                            <i class="fas fa-wrench mr-2"></i> Mantenimiento
                        </a>
                        <a class="collapse-item" href="<?= base_url("index.php/optimizacion") ?>">
                            <i class="fas fa-tachometer-alt mr-2"></i> Optimización
                        </a>
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
                        <a class="collapse-item" href="<?= base_url("index.php/inventario") ?>">
                            <i class="fas fa-clipboard-list mr-2"></i> Inventario
                        </a>
                        <a class="collapse-item" href="<?= base_url("index.php/inventario/listado") ?>">
                            <i class="fas fa-list mr-2"></i> Listado de Inventarios
                        </a>
                        <a class="collapse-item" href="<?= base_url('movimientos') ?>">
                            <i class="fas fa-exchange-alt mr-2"></i> Movimientos
                        </a>
                        <a class="collapse-item" href="<?= base_url('celulares/movimientos') ?>">
                            <i class="fas fa-mobile-alt mr-2"></i> Celulares
                        </a>
                        <a class="collapse-item" href="<?= base_url("index.php/baja") ?>">
                            <i class="fas fa-trash-alt mr-2"></i> Baja
                        </a>
                        <a class="collapse-item" href="<?= base_url("index.php/ip") ?>">
                            <i class="fas fa-network-wired mr-2"></i> IPs
                        </a>
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

            <!-- Nav Item - Auditoría -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('auditoria'); ?>">
                    <i class="fas fa-history"></i>
                    <span>Auditoría</span>
                </a>
            </li>

            <!-- Nav Item - Backup -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('backup'); ?>">
                    <i class="fas fa-database"></i>
                    <span>Backup</span>
                </a>
            </li>

            <!-- Nav Item - Sesiones Activas -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('sesiones'); ?>">
                    <i class="fas fa-users-cog"></i>
                    <span>Sesiones Activas</span>
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
                    <!-- <form
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
                    </form> -->

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
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="notificacionesBtn" role="button" data-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span id="contadorNotificaciones" class="badge badge-danger"
                                    style="display:none;">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificacionesBtn">
                                <h6 class="dropdown-header">Notificaciones</h6>
                                <div id="listaNotificaciones" class="px-3">
                                    <small>No hay alertas</small>
                                </div>
                            </div>
                        </li>
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
                                    <label for="inputFoto">Cambiar foto (JPG/PNG, máx 2MB)</label>
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
                                    <label for="perfilUsuario">Usuario</label>
                                    <input id="perfilUsuario" type="text" class="form-control" value="<?= esc(session('usuario')) ?>"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="perfilNombre">Nombre</label>
                                    <input id="perfilNombre" name="nombre" type="text" class="form-control"
                                        value="<?= esc(session('nombre')) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="perfilCorreo">Correo</label>
                                    <input id="perfilCorreo" name="correo" type="email" class="form-control"
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
    
    <!-- Chart.js para gráficas del dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Scripts de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Scripts modulares de la aplicación -->
    <script src="<?= base_url('js/core/app.js') ?>"></script>
    <script>
        // Inicializar configuración global después de cargar app.js
        APP.init("<?= base_url() ?>");
    </script>
    <script src="<?= base_url('js/modules/notificaciones.js') ?>"></script>
    <script src="<?= base_url('js/modules/perfil.js') ?>"></script>
    <script src="<?= base_url('js/modules/movimientos.js') ?>"></script>
    <script src="<?= base_url('js/modules/bienes-datatable.js') ?>"></script>

    <?= $this->renderSection('scripts_datatable_ip') ?>
    <?= $this->renderSection('scripts') ?>
</body>

</html>