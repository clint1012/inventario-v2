<?= $this->extend('plantilla') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-database text-primary"></i> Gestión de Backups
        </h1>
        <div>
            <button class="btn btn-success btn-sm" onclick="crearBackup()">
                <i class="fas fa-plus-circle"></i> Crear Backup
            </button>
            <button class="btn btn-warning btn-sm" onclick="limpiarAntiguos()">
                <i class="fas fa-broom"></i> Limpiar Antiguos
            </button>
            <a href="<?= base_url('notificaciones/probar-email') ?>" class="btn btn-info btn-sm">
                <i class="fas fa-envelope"></i> Probar Email
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Info Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Backups</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($backups) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Último Backup</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                <?= !empty($backups) ? date('d/m/Y H:i', strtotime($backups[0]['fecha'])) : 'Ninguno' ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ruta de Backups</div>
                            <div class="small text-gray-800"><?= $backup_path ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Backups -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Lista de Backups
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($backups)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <p class="mb-0">No hay backups disponibles. Crea tu primer backup usando el botón "Crear Backup".</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fas fa-file"></i> Archivo</th>
                                <th><i class="fas fa-hdd"></i> Tamaño</th>
                                <th><i class="fas fa-calendar"></i> Fecha de Creación</th>
                                <th class="text-center"><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-file-archive text-primary"></i>
                                        <strong><?= esc($backup['nombre']) ?></strong>
                                    </td>
                                    <td><?= esc($backup['tamaño']) ?></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($backup['fecha'])) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('backup/descargar/' . $backup['nombre']) ?>" 
                                           class="btn btn-sm btn-info" 
                                           title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="restaurarBackup('<?= esc($backup['nombre']) ?>')" 
                                                title="Restaurar">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="eliminarBackup('<?= esc($backup['nombre']) ?>')" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle"></i> Información Importante
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-check-circle"></i> Buenas Prácticas:</h6>
                    <ul>
                        <li>Crear backups antes de actualizaciones importantes</li>
                        <li>Mantener backups en ubicaciones externas seguras</li>
                        <li>Realizar backups periódicos (diario, semanal)</li>
                        <li>Verificar que los backups se puedan restaurar</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Advertencias:</h6>
                    <ul>
                        <li><strong>Restaurar un backup sobrescribirá todos los datos actuales</strong></li>
                        <li>Asegúrate de tener un backup reciente antes de restaurar</li>
                        <li>Los backups antiguos se limpian automáticamente después de 30 días</li>
                        <li>Se recomienda cerrar sesión después de restaurar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[2, 'desc']] // Ordenar por fecha descendente
    });
});

function crearBackup() {
    Swal.fire({
        title: '¿Crear nuevo backup?',
        text: 'Esto puede tomar algunos minutos dependiendo del tamaño de la base de datos',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Sí, crear',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '<?= base_url('backup/crear') ?>',
                method: 'POST',
                dataType: 'json',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                }
            }).fail(function() {
                Swal.showValidationMessage('Error en la comunicación con el servidor');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Backup creado',
                    html: `<strong>Archivo:</strong> ${result.value.archivo}<br><strong>Tamaño:</strong> ${result.value.tamaño}`,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', result.value.message, 'error');
            }
        }
    });
}

function restaurarBackup(archivo) {
    Swal.fire({
        title: '⚠️ ¡ADVERTENCIA!',
        html: `<p class="text-danger"><strong>Esta acción sobrescribirá TODOS los datos actuales de la base de datos.</strong></p>
               <p>¿Estás seguro de restaurar el backup:<br><code>${archivo}</code>?</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-exclamation-triangle"></i> Sí, restaurar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '<?= base_url('backup/restaurar') ?>',
                method: 'POST',
                data: { 
                    archivo: archivo,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json'
            }).fail(function() {
                Swal.showValidationMessage('Error en la comunicación con el servidor');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Restauración Exitosa',
                    text: result.value.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.href = '<?= base_url('logout') ?>';
                });
            } else {
                Swal.fire('Error', result.value.message, 'error');
            }
        }
    });
}

function eliminarBackup(archivo) {
    Swal.fire({
        title: '¿Eliminar backup?',
        text: `Archivo: ${archivo}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('backup/eliminar') ?>',
                method: 'POST',
                data: { 
                    archivo: archivo,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar el backup', 'error');
                }
            });
        }
    });
}

function limpiarAntiguos() {
    Swal.fire({
        title: '¿Limpiar backups antiguos?',
        text: 'Se eliminarán los backups de más de 30 días',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-broom"></i> Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('backup/limpiarAntiguos') ?>',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Limpieza Completada',
                            text: response.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo realizar la limpieza', 'error');
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
