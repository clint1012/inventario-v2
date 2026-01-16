<?= $this->extend('plantilla') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid">
    <!-- Título -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history"></i> <?= esc($titulo) ?>
        </h1>
        <a href="<?= base_url('sesiones') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Sesiones
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtros
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>Acción</label>
                    <select class="form-control" id="filtroAccion">
                        <option value="">Todas</option>
                        <option value="LOGIN">Login</option>
                        <option value="LOGOUT">Logout</option>
                        <option value="SESION_CERRADA">Sesión Cerrada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Desde</label>
                    <input type="date" class="form-control" id="filtroFechaInicio">
                </div>
                <div class="col-md-3">
                    <label>Hasta</label>
                    <input type="date" class="form-control" id="filtroFechaFin">
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <button class="btn btn-primary btn-block" id="btnFiltrar">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de historial -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Historial de Accesos
            </h6>
            <button class="btn btn-sm btn-success" id="btnExportarExcel">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaHistorial" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Acción</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>IP</th>
                            <th>Navegador</th>
                            <th>SO</th>
                            <th>Duración</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llena con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let tabla;

    // Función para formatear fecha sin moment.js
    function formatearFecha(fecha) {
        if (!fecha) return '-';
        const d = new Date(fecha);
        return d.toLocaleDateString('es-PE') + ' ' + d.toLocaleTimeString('es-PE');
    }

    // Inicializar DataTable
    function inicializarTabla(filtros = {}) {
        if (tabla) {
            tabla.destroy();
        }

        let params = new URLSearchParams(filtros);

        tabla = $('#tablaHistorial').DataTable({
            ajax: {
                url: '<?= base_url('sesiones/listarHistorial') ?>?' + params.toString(),
                dataSrc: 'data'
            },
            columns: [
                { 
                    data: 'fecha',
                    render: function(data) {
                        return formatearFecha(data);
                    }
                },
                { 
                    data: 'accion',
                    render: function(data) {
                        let badge = 'secondary';
                        let icon = 'fa-question';
                        
                        if (data === 'LOGIN') {
                            badge = 'success';
                            icon = 'fa-sign-in-alt';
                        } else if (data === 'LOGOUT') {
                            badge = 'info';
                            icon = 'fa-sign-out-alt';
                        } else if (data === 'SESION_CERRADA') {
                            badge = 'warning';
                            icon = 'fa-times-circle';
                        }
                        
                        return `<span class="badge badge-${badge}">
                                    <i class="fas ${icon}"></i> ${data}
                                </span>`;
                    }
                },
                { data: 'usuario' },
                { data: 'nombre' },
                { data: 'ip_address' },
                { data: 'navegador' },
                { data: 'sistema_operativo' },
                { 
                    data: 'duracion_segundos',
                    render: function(data) {
                        if (!data) {
                            return '<span class="text-muted">-</span>';
                        }
                        
                        const horas = Math.floor(data / 3600);
                        const minutos = Math.floor((data % 3600) / 60);
                        const segundos = data % 60;
                        
                        if (horas > 0) {
                            return `${horas}h ${minutos}m`;
                        } else if (minutos > 0) {
                            return `${minutos}m ${segundos}s`;
                        } else {
                            return `${segundos}s`;
                        }
                    }
                }
            ],
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 50,
            responsive: true
        });
    }

    // Inicializar con fecha de hoy
    const hoy = new Date().toISOString().split('T')[0];
    $('#filtroFechaInicio').val(hoy);
    $('#filtroFechaFin').val(hoy);

    inicializarTabla({
        fecha_inicio: hoy + ' 00:00:00',
        fecha_fin: hoy + ' 23:59:59'
    });

    // Aplicar filtros
    $('#btnFiltrar').on('click', function() {
        const filtros = {};
        
        const accion = $('#filtroAccion').val();
        if (accion) filtros.accion = accion;
        
        const fechaInicio = $('#filtroFechaInicio').val();
        if (fechaInicio) filtros.fecha_inicio = fechaInicio + ' 00:00:00';
        
        const fechaFin = $('#filtroFechaFin').val();
        if (fechaFin) filtros.fecha_fin = fechaFin + ' 23:59:59';
        
        inicializarTabla(filtros);
    });

    // Exportar a Excel (funcionalidad básica)
    $('#btnExportarExcel').on('click', function() {
        Swal.fire({
            icon: 'info',
            title: 'Exportar',
            text: 'Funcionalidad de exportación disponible próximamente',
            timer: 2000
        });
    });

    // Enter en filtros
    $('#filtroAccion, #filtroFechaInicio, #filtroFechaFin').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btnFiltrar').click();
        }
    });
});
</script>

<?= $this->endSection() ?>
