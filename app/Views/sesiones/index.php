<?= $this->extend('plantilla') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid">
    <!-- Título -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users-cog"></i> <?= esc($titulo) ?>
        </h1>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Sesiones Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-activas">
                                <?= $estadisticas['activas_ahora'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Usuarios Conectados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-usuarios">
                                <?= $estadisticas['usuarios_conectados'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Logins Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-logins-hoy">
                                <?= $estadisticas['sesiones_hoy'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-in-alt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Múltiples Sesiones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-multiples">
                                <?= $estadisticas['multiples_sesiones'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="row mb-3">
        <div class="col-12">
            <button class="btn btn-primary" id="btnRefrescar">
                <i class="fas fa-sync-alt"></i> Refrescar
            </button>
            <a href="<?= base_url('sesiones/historial') ?>" class="btn btn-info">
                <i class="fas fa-history"></i> Ver Historial
            </a>
            <button class="btn btn-warning" id="btnLimpiarExpiradas">
                <i class="fas fa-broom"></i> Limpiar Expiradas
            </button>
        </div>
    </div>

    <!-- Tabla de sesiones activas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Sesiones Activas
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tablaSesiones" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>IP</th>
                            <th>Navegador</th>
                            <th>SO</th>
                            <th>Inicio</th>
                            <th>Última Actividad</th>
                            <th>Duración</th>
                            <th>Acciones</th>
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

    // Función para formatear fecha (sin moment.js)
    function formatearFecha(fecha) {
        if (!fecha) return '-';
        const d = new Date(fecha);
        return d.toLocaleDateString('es-PE') + ' ' + d.toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'});
    }

    // Función para formatear hora
    function formatearHora(fecha) {
        if (!fecha) return '-';
        const d = new Date(fecha);
        return d.toLocaleTimeString('es-PE');
    }

    // Inicializar DataTable
    function inicializarTabla() {
        if (tabla) {
            tabla.destroy();
        }

        console.log('=== INICIANDO CARGA DE SESIONES ===');
        console.log('URL:', '<?= base_url('sesiones/listarSesiones') ?>');

        tabla = $('#tablaSesiones').DataTable({
            ajax: {
                url: '<?= base_url('sesiones/listarSesiones') ?>',
                dataSrc: function(json) {
                    console.log('=== RESPUESTA RECIBIDA ===');
                    console.log('JSON completo:', json);
                    console.log('Success:', json.success);
                    console.log('Total registros:', json.total);
                    console.log('Data:', json.data);
                    
                    if (json.success && json.data) {
                        console.log('✅ Retornando', json.data.length, 'sesiones');
                        return json.data;
                    }
                    console.error('❌ Formato de respuesta inválido');
                    return [];
                },
                error: function(xhr, error, code) {
                    console.error('Error AJAX:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al cargar sesiones',
                        html: 'Código: ' + xhr.status + '<br>Verifica la consola (F12) para más detalles',
                        footer: '<a href="<?= base_url('sesiones/listarSesiones') ?>" target="_blank">Ver respuesta API</a>'
                    });
                }
            },
            columns: [
                { data: 'usuario' },
                { data: 'nombre' },
                { data: 'ip_address' },
                { data: 'navegador' },
                { data: 'sistema_operativo' },
                { 
                    data: 'fecha_inicio',
                    render: function(data) {
                        return formatearFecha(data);
                    }
                },
                { 
                    data: 'ultima_actividad',
                    render: function(data, type, row) {
                        if (!data) return '-';
                        const tiempo = row.tiempo_inactivo || '0s';
                        const badge = tiempo.includes('h') ? 'warning' : 'success';
                        return formatearHora(data) + 
                               ` <span class="badge badge-${badge}">${tiempo}</span>`;
                    }
                },
                { 
                    data: 'duracion_sesion',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        if (row.es_mi_sesion) {
                            return '<span class="badge badge-success"><i class="fas fa-check"></i> Tu sesión</span>';
                        } else {
                            return `
                                <button class="btn btn-sm btn-danger btnCerrarSesion" 
                                        data-id="${row.id}" 
                                        data-usuario="${row.usuario}">
                                    <i class="fas fa-times"></i> Cerrar
                                </button>
                            `;
                        }
                    }
                }
            ],
            order: [[6, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 25
        });
    }

    // Inicializar
    inicializarTabla();

    // Auto-refresh cada 30 segundos
    setInterval(function() {
        tabla.ajax.reload(null, false);
        actualizarEstadisticas();
    }, 30000);

    // Botón refrescar
    $('#btnRefrescar').on('click', function() {
        tabla.ajax.reload();
        actualizarEstadisticas();
        Swal.fire({
            icon: 'success',
            title: 'Actualizado',
            text: 'Datos actualizados correctamente',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Cerrar sesión individual
    $('#tablaSesiones').on('click', '.btnCerrarSesion', function() {
        const id = $(this).data('id');
        const usuario = $(this).data('usuario');

        Swal.fire({
            title: '¿Cerrar sesión?',
            text: `¿Estás seguro de cerrar la sesión de ${usuario}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cerrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                cerrarSesion(id);
            }
        });
    });

    // Limpiar sesiones expiradas
    $('#btnLimpiarExpiradas').on('click', function() {
        Swal.fire({
            title: 'Limpiando...',
            text: 'Cerrando sesiones inactivas',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        tabla.ajax.reload(function() {
            Swal.fire({
                icon: 'success',
                title: 'Limpieza completada',
                text: 'Sesiones expiradas cerradas',
                timer: 2000
            });
            actualizarEstadisticas();
        });
    });

    // Función para cerrar sesión
    function cerrarSesion(id) {
        $.ajax({
            url: '<?= base_url('sesiones/cerrarSesion') ?>',
            type: 'POST',
            data: {
                sesion_id: id,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sesión cerrada',
                        text: response.message,
                        timer: 2000
                    });
                    tabla.ajax.reload();
                    actualizarEstadisticas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cerrar la sesión'
                });
            }
        });
    }

    // Actualizar estadísticas
    function actualizarEstadisticas() {
        $.get('<?= base_url('sesiones/estadisticas') ?>', function(response) {
            if (response.success) {
                const stats = response.data.estadisticas;
                $('#stat-activas').text(stats.activas_ahora);
                $('#stat-usuarios').text(stats.usuarios_conectados);
                $('#stat-logins-hoy').text(stats.sesiones_hoy);
                $('#stat-multiples').text(stats.multiples_sesiones);
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
