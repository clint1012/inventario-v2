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
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .badge-bien {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    .btn-remove-bien {
        padding: 0.2rem 0.5rem;
        font-size: 0.8rem;
    }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="fas fa-plus-circle text-primary"></i> Nuevo Movimiento
        </h4>
        <p class="text-muted mb-0 small">Registrar asignación, préstamo, retiro o cambio de bienes</p>
    </div>
    <a href="<?= base_url('movimientos') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<!-- Alertas -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('movimientos') ?>" method="post" autocomplete="off">
    <?= csrf_field() ?>
    <div class="row g-3">

    <div class="row g-3">
        
        <!-- Card: Información del Movimiento -->
        <div class="col-12">
            <div class="card shadow-sm form-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Movimiento</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Tipo de movimiento -->
                        <div class="col-md-6">
                            <label for="tipo_movimiento" class="form-label">
                                <i class="fas fa-exchange-alt me-1"></i>Tipo de Movimiento <span class="text-danger">*</span>
                            </label>
                            <select name="tipo_movimiento" id="tipo_movimiento" class="form-select" required>
                                <option value="asignacion">📝 Asignación</option>
                                <option value="prestamo">🤝 Préstamo</option>
                                <option value="retiro">↩️ Retiro</option>
                                <option value="cambio">🔄 Cambio</option>
                            </select>
                            <small class="text-muted">Seleccione el tipo de movimiento a realizar</small>
                        </div>

                        <!-- Fecha -->
                        <div class="col-md-6">
                            <label for="fecha_movimiento" class="form-label">
                                <i class="fas fa-calendar-alt me-1"></i>Fecha del Movimiento <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="fecha_movimiento" id="fecha_movimiento" 
                                   class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>

                        <!-- Fecha límite del préstamo (solo para préstamos) -->
                        <div id="contenedor_fecha_prestamo" class="col-md-6" style="display: none;">
                            <label for="fecha_limite" class="form-label">
                                <i class="fas fa-hourglass-end me-1"></i>Fecha Límite del Préstamo <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha_limite" id="fecha_limite" class="form-control">
                            <small class="text-muted">Fecha en que debe devolverse el bien</small>
                        </div>

                        <!-- Observaciones -->
                        <div class="col-12">
                            <label for="observaciones" class="form-label">
                                <i class="fas fa-comment-dots me-1"></i>Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="3"
                                      placeholder="Ingrese observaciones adicionales (opcional)"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Datos de Destino -->
        <div class="col-12">
            <div class="card shadow-sm form-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Datos de Destino</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Persona -->
                        <div class="col-md-4">
                            <label for="persona_nombre" class="form-label">
                                <i class="fas fa-user me-1"></i>Persona <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="persona_nombre" 
                                   placeholder="Escriba al menos 3 letras..." autocomplete="off">
                            <input type="hidden" id="id_personas" name="id_personas" required>
                            <ul id="personaSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000; max-height: 200px; overflow-y: auto; width: calc(100% - 30px);"></ul>
                            <small class="text-muted">Escriba al menos 3 letras para ver sugerencias</small>
                        </div>

                        <!-- Departamento -->
                        <div class="col-md-4">
                            <label for="departamento_nombre" class="form-label">
                                <i class="fas fa-sitemap me-1"></i>Departamento <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="departamento_nombre" 
                                   placeholder="Escriba al menos 3 letras..." autocomplete="off">
                            <input type="hidden" id="id_departamentos" name="id_departamentos" required>
                            <ul id="departamentoSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000; max-height: 200px; overflow-y: auto; width: calc(100% - 30px);"></ul>
                            <small class="text-muted">Escriba al menos 3 letras para ver sugerencias</small>
                        </div>

                        <!-- Local -->
                        <div class="col-md-4">
                            <label for="id_locales" class="form-label">
                                <i class="fas fa-building me-1"></i>Local/Sede <span class="text-danger">*</span>
                            </label>
                            <select name="id_locales" id="id_locales" class="form-select" required>
                                <option value="">-- Seleccione un local --</option>
                                <?php foreach ($locales as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= $l['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Selección de Bienes -->
        <div class="col-12">
            <div class="card shadow-sm form-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-laptop me-2"></i>Selección de Bienes</h5>
                </div>
                <div class="card-body">
                    <!-- Contenedor para Asignación -->
                    <div id="contenedor_asignar" class="mb-4">
                        <label for="buscador_asignar" class="form-label">
                            <i class="fas fa-search me-1"></i>Buscar bienes para asignar
                        </label>
                        <select id="buscador_asignar" class="form-control" style="width: 100%;"></select>
                        <small class="text-muted d-block mb-2">Busque y seleccione los bienes disponibles para asignar</small>
                        <ul id="lista_asignar" class="list-group mt-2"></ul>
                    </div>

                    <!-- Contenedor para Préstamo -->
                    <div id="contenedor_prestar" class="mb-4">
                        <label for="buscador_prestar" class="form-label">
                            <i class="fas fa-search me-1"></i>Buscar bienes para prestar
                        </label>
                        <select id="buscador_prestar" class="form-control" style="width: 100%;"></select>
                        <small class="text-muted d-block mb-2">Busque y seleccione los bienes disponibles para préstamo</small>
                        <ul id="lista_prestar" class="list-group mt-2"></ul>
                    </div>

                    <!-- Contenedor para Retiro -->
                    <div id="contenedor_retirar" class="mb-4">
                        <label for="buscador_retirar" class="form-label">
                            <i class="fas fa-search me-1"></i>Buscar bienes para retirar
                        </label>
                        <select id="buscador_retirar" class="form-control" style="width: 100%;"></select>
                        <small class="text-muted d-block mb-2">Busque y seleccione los bienes asignados a retirar</small>
                        <ul id="lista_retirar" class="list-group mt-2"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <a href="<?= base_url('movimientos') ?>" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Guardar Movimiento
            </button>
        </div>
    </div>
</form>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Autocompletado para Persona
    let timeoutPersona;
    $('#persona_nombre').on('input', function() {
        $('#id_personas').val('');
        const query = $(this).val().trim();
        
        clearTimeout(timeoutPersona);
        
        if (query.length >= 3) {
            timeoutPersona = setTimeout(function() {
                $.ajax({
                    url: '<?= base_url('movimientos/getPersonas') ?>',
                    method: 'GET',
                    data: { q: query },
                    dataType: 'json',
                    success: function(response) {
                        $('#personaSuggestions').empty();
                        if (response.length > 0) {
                            response.forEach(function(persona) {
                                $('#personaSuggestions').append(
                                    `<li class="list-group-item list-group-item-action" style="cursor: pointer;" 
                                        data-id="${persona.id}" data-nombre="${persona.nombre_completo}">
                                        <i class="fas fa-user text-primary me-2"></i>${persona.nombre_completo}
                                    </li>`
                                );
                            });
                            $('#personaSuggestions').show();
                        } else {
                            $('#personaSuggestions').hide();
                        }
                    }
                });
            }, 300);
        } else {
            $('#personaSuggestions').hide();
        }
    });

    // Autocompletado para Departamento
    let timeoutDepartamento;
    $('#departamento_nombre').on('input', function() {
        $('#id_departamentos').val('');
        const query = $(this).val().trim();
        
        clearTimeout(timeoutDepartamento);
        
        if (query.length >= 3) {
            timeoutDepartamento = setTimeout(function() {
                $.ajax({
                    url: '<?= base_url('movimientos/getDepartamentos') ?>',
                    method: 'GET',
                    data: { q: query },
                    dataType: 'json',
                    success: function(response) {
                        $('#departamentoSuggestions').empty();
                        if (response.length > 0) {
                            response.forEach(function(departamento) {
                                $('#departamentoSuggestions').append(
                                    `<li class="list-group-item list-group-item-action" style="cursor: pointer;" 
                                        data-id="${departamento.id}" data-nombre="${departamento.nombre}">
                                        <i class="fas fa-sitemap text-info me-2"></i>${departamento.nombre}
                                    </li>`
                                );
                            });
                            $('#departamentoSuggestions').show();
                        } else {
                            $('#departamentoSuggestions').hide();
                        }
                    }
                });
            }, 300);
        } else {
            $('#departamentoSuggestions').hide();
        }
    });

    // Selección de persona
    $(document).on('click', '#personaSuggestions li', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        $('#persona_nombre').val(nombre);
        $('#id_personas').val(id);
        $('#personaSuggestions').hide();
    });

    // Selección de departamento
    $(document).on('click', '#departamentoSuggestions li', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        $('#departamento_nombre').val(nombre);
        $('#id_departamentos').val(id);
        $('#departamentoSuggestions').hide();
    });

    // Ocultar sugerencias al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#persona_nombre, #personaSuggestions').length) {
            $('#personaSuggestions').hide();
        }
        if (!$(e.target).closest('#departamento_nombre, #departamentoSuggestions').length) {
            $('#departamentoSuggestions').hide();
        }
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection(); ?>