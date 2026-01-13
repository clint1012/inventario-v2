<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<style>
    .form-card {
        border-radius: 8px;
        transition: box-shadow 0.3s ease;
    }
    .form-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card-header {
        font-weight: 600;
        border-radius: 8px 8px 0 0 !important;
        padding: 0.875rem 1.25rem;
    }
    .card-header h5 {
        margin: 0;
        font-size: 1rem;
    }
    .badge-type {
        font-size: 0.85rem;
        padding: 0.4rem 0.7rem;
        font-weight: 500;
    }
    .btn-action-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="fas fa-file-signature text-primary"></i> Actas de Instalación por Usuario
        </h4>
        <p class="text-muted mb-0 small">Gestión de movimientos de bienes informáticos</p>
    </div>
    <a href="<?= base_url('movimientos/new') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Movimiento
    </a>
</div>

<!-- Tabla de movimientos -->
<div class="card shadow-sm form-card">
    <div class="card-body">
        <table id="tablaMovimientos" class="table table-hover table-striped">
            <thead class="table-light">
                <tr>
                    <th><i class="fas fa-user me-1"></i>Usuario</th>
                    <th><i class="fas fa-calendar me-1"></i>Fecha</th>
                    <th><i class="fas fa-tag me-1"></i>Tipo</th>
                    <th class="text-center"><i class="fas fa-cog me-1"></i>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $mov): ?>
                    <tr data-lote="<?= $mov['lote'] ?>"
                        data-departamento="<?= $mov['id_departamentos'] ?? $mov['id_departamento'] ?? '' ?>"
                        data-local="<?= $mov['id_locales'] ?? $mov['id_local'] ?? '' ?>">

                        <!-- USUARIO -->
                        <td>
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            <strong>
                                <?php
                                if ($mov['tipo_movimiento'] === 'retiro') {
                                    // Mostrar el usuario ANTERIOR (a quien se retiró)
                                    echo strtoupper(
                                        trim(
                                            $mov['nombre_anterior'] . ' ' .
                                            $mov['apep_anterior'] . ' ' .
                                            $mov['apem_anterior']
                                        )
                                    );
                                } else {
                                    // Mostrar el usuario DESTINO (a quien se asignó)
                                    echo strtoupper(
                                        trim(
                                            $mov['nombre_destino'] . ' ' .
                                            $mov['apep_destino'] . ' ' .
                                            $mov['apem_destino']
                                        )
                                    );
                                }
                                ?>
                            </strong>
                        </td>

                        <!-- FECHA -->
                        <td data-order="<?= strtotime($mov['fecha_movimiento']) ?>">
                            <i class="far fa-clock text-muted me-2"></i>
                            <?= date('d-m-Y H:i', strtotime($mov['fecha_movimiento'])) ?>
                        </td>

                        <!-- TIPO DE MOVIMIENTO -->
                        <td>
                            <?php
                            $tipoIcons = [
                                'asignacion' => ['icon' => 'fas fa-arrow-right', 'color' => 'success'],
                                'prestamo' => ['icon' => 'fas fa-hand-holding', 'color' => 'info'],
                                'retiro' => ['icon' => 'fas fa-arrow-left', 'color' => 'danger'],
                                'cambio' => ['icon' => 'fas fa-exchange-alt', 'color' => 'warning'],
                                'devolución' => ['icon' => 'fas fa-undo', 'color' => 'secondary']
                            ];
                            $tipo = strtolower($mov['tipo_movimiento']);
                            $config = $tipoIcons[$tipo] ?? ['icon' => 'fas fa-circle', 'color' => 'secondary'];
                            ?>
                            <span class="badge bg-<?= $config['color'] ?> badge-type">
                                <i class="<?= $config['icon'] ?> me-1"></i><?= ucfirst($tipo) ?>
                            </span>
                        </td>

                        <!-- ACCIONES -->
                        <td class="text-center">
                            <div class="btn-action-group justify-content-center">
                                <a href="<?= base_url('movimientos/descargarCargoLote/' . $mov['lote']) ?>"
                                   class="btn btn-sm btn-primary" target="_blank" title="Descargar PDF">
                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                </a>

                                <button class="btn btn-sm btn-danger btnAnular" 
                                        data-id="<?= $mov['id'] ?? '' ?>"
                                        data-lote="<?= $mov['lote'] ?>"
                                        title="Anular movimiento">
                                    <i class="fas fa-times-circle me-1"></i>Anular
                                </button>

                                <?php if (!empty($mov['tipo_movimiento']) && $mov['tipo_movimiento'] === 'prestamo'): ?>
                                    <button class="btn btn-sm btn-success btnDevolver" 
                                            data-lote="<?= $mov['lote'] ?>"
                                            title="Devolver préstamo">
                                        <i class="fas fa-check-circle me-1"></i>Devolver
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL DE ANULACIÓN -->
<div class="modal fade" id="modalAnular" tabindex="-1" aria-labelledby="modalAnularLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalAnularLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Anulación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle me-2 fs-5"></i>
                    <div>
                        <strong>Atención:</strong> Al anular este movimiento, los bienes quedarán liberados y disponibles para nueva asignación.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="motivoAnulacion" class="form-label fw-bold">
                        <i class="fas fa-comment-dots me-1"></i>Motivo de Anulación <span class="text-danger">*</span>
                    </label>
                    <textarea id="motivoAnulacion" class="form-control" rows="4"
                        placeholder="Ej: Error de asignación, cambio de usuario, etc." required></textarea>
                    <small class="text-muted">Por favor describa el motivo de la anulación</small>
                </div>

                <input type="hidden" id="idMovimiento">
                <input type="hidden" id="loteMovimiento">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarAnulacion">
                    <i class="fas fa-check me-1"></i>Confirmar Anulación
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // Inicializar DataTable
        const table = $('#tablaMovimientos').DataTable({
            responsive: true,
            language: { 
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" 
            },
            order: [[1, 'desc']], // Ordenar por fecha descendente
            pageLength: 20,
            lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        });

        // Evento para abrir modal de anulación
        $(document).on('click', '.btnAnular', function () {
            const id = $(this).data('id');
            const lote = $(this).data('lote');
            
            $('#idMovimiento').val(id);
            $('#loteMovimiento').val(lote);
            $('#motivoAnulacion').val('');
            
            // Bootstrap 5 modal
            const modal = new bootstrap.Modal(document.getElementById('modalAnular'));
            modal.show();
        });

        // Confirmar anulación
        $('#confirmarAnulacion').on('click', function () {
            const lote = $('#loteMovimiento').val();
            const motivo = $('#motivoAnulacion').val().trim();

            if (motivo === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo Requerido',
                    text: 'Por favor ingrese un motivo de anulación.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Deshabilitar botón mientras se procesa
            $('#confirmarAnulacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Procesando...');

            $.ajax({
                url: "<?= base_url('movimientos/anular/') ?>" + lote,
                type: 'POST',
                data: { motivo_anulacion: motivo },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Remover fila de la tabla
                        const row = $('tr[data-lote="' + lote + '"]');
                        table.row(row).remove().draw(false);

                        // Cerrar modal
                        bootstrap.Modal.getInstance(document.getElementById('modalAnular')).hide();

                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: '¡Anulado!',
                            text: response.message || 'El movimiento fue anulado correctamente.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Ocurrió un error al anular el movimiento.'
                        });
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo conectar con el servidor. Por favor intente nuevamente.'
                    });
                },
                complete: function() {
                    // Rehabilitar botón
                    $('#confirmarAnulacion').prop('disabled', false).html('<i class="fas fa-check me-1"></i>Confirmar Anulación');
                }
            });
        });

        // Evento para devolver préstamo
        $(document).on('click', '.btnDevolver', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const lote = $btn.data('lote');
            
            if (!lote) {
                Swal.fire('Error', 'Lote no especificado.', 'error');
                return;
            }

            Swal.fire({
                title: '¿Confirmar Devolución?',
                html: '¿Confirma devolver este préstamo?<br>Los bienes regresarán al área OTI.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check me-1"></i>Sí, Devolver',
                cancelButtonText: '<i class="fas fa-times me-1"></i>Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = "<?= base_url('movimientos/devolverPrestamo') ?>/" + lote;

                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Registrando devolución',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.post(url, {}, function (resp) {
                        if (resp && resp.status === 'success') {
                            // Actualizar la fila: quitar nombre de usuario, actualizar departamento/local y tipo
                            const $tr = $('tr[data-lote="' + lote + '"]');
                            if ($tr.length) {
                                // USUARIO -> vacío
                                $tr.find('td').eq(0).html('<i class="fas fa-minus-circle text-muted me-2"></i><span class="text-muted">-</span>');

                                // TIPO -> Devolución con badge
                                $tr.find('td').eq(2).html('<span class="badge bg-secondary badge-type"><i class="fas fa-undo me-1"></i>Devolución</span>');

                                // Quitar botón Devolver
                                $tr.find('.btnDevolver').remove();
                            }

                            Swal.fire({
                                icon: 'success',
                                title: '¡Devolución Exitosa!',
                                text: resp.message || 'El préstamo ha sido devuelto correctamente.',
                                timer: 2500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resp.message || 'Error al devolver el préstamo.'
                            });
                        }
                    }, 'json').fail(function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de Servidor',
                            text: 'No se pudo procesar la devolución. Intente nuevamente.'
                        });
                    });
                }
            });
        });
    });
</script>

<?php if (session('pdf_lote')): ?>
<script>
    window.open('<?= base_url('movimientos/descargarCargoLote/' . session('pdf_lote')) ?>', '_blank');
</script>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->endSection(); ?>