<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container mt-4">
    <h2>📋 Nuevo Movimiento de Celular</h2>
    <a href="<?= base_url('celulares/movimientos') ?>" class="btn btn-secondary mb-3">← Volver</a>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('celulares/movimientos/guardar') ?>" method="POST" id="formMovimiento">
                <?= csrf_field() ?>

                <!-- TIPO DE MOVIMIENTO -->
                <div class="form-group">
                    <label>Tipo de Movimiento <span class="text-danger">*</span></label>
                    <select name="tipo_movimiento" id="tipoMovimiento" class="form-control" required>
                        <option value="">-- Seleccione el tipo de movimiento --</option>
                        <option value="entrega">📤 Entrega de Celular</option>
                        <option value="devolucion">📥 Devolución de Celular</option>
                    </select>
                    <small class="form-text text-muted">Seleccione el tipo de movimiento para ver las opciones disponibles</small>
                </div>

                <!-- FECHA -->
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha_movimiento" class="form-control" 
                           value="<?= date('Y-m-d') ?>">
                </div>

                <!-- SECCIÓN ENTREGA -->
                <div id="seccionEntrega" style="display: none;">
                    <hr>
                    <h4 class="text-success">📤 Entrega de Celular</h4>

                    <div class="form-group">
                        <label>Usuario Receptor <span class="text-danger">*</span></label>
                        <select name="id_personas" class="form-control select2">
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($personas as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= strtoupper($p['nombre'] . ' ' . $p['ape_paterno'] . ' ' . $p['ape_materno']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Departamento <span class="text-danger">*</span></label>
                                <select name="id_departamentos" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <?php foreach ($departamentos as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Local <span class="text-danger">*</span></label>
                                <select name="id_locales" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <?php foreach ($locales as $l): ?>
                                        <option value="<?= $l['id'] ?>"><?= $l['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Celulares a Entregar <span class="text-danger">*</span></label>
                        <input type="text" id="buscarEntrega" class="form-control mb-2" placeholder="🔍 Buscar por modelo, IMEI o N/S...">
                        <div class="card">
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <?php if (empty($celulares_disponibles)): ?>
                                    <p class="text-muted">No hay celulares disponibles</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="tablaEntrega">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="50">Sel.</th>
                                                    <th>Modelo</th>
                                                    <th>IMEI</th>
                                                    <th>N/S</th>
                                                    <th>Descripción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($celulares_disponibles as $cel): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" 
                                                                       name="celulares_entrega[]" 
                                                                       id="cel_entrega_<?= $cel['id'] ?>" 
                                                                       value="<?= $cel['id'] ?>">
                                                                <label class="custom-control-label" for="cel_entrega_<?= $cel['id'] ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td><strong><?= esc($cel['modelo']) ?></strong></td>
                                                        <td><code><?= esc($cel['imei']) ?></code></td>
                                                        <td><?= esc($cel['numero_serie'] ?? 'N/A') ?></td>
                                                        <td><small><?= esc(substr($cel['descripcion'] ?? '', 0, 50)) ?><?= strlen($cel['descripcion'] ?? '') > 50 ? '...' : '' ?></small></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <span id="contadorEntrega">0</span> celular(es) seleccionado(s)
                        </small>
                    </div>
                </div>

                <!-- SECCIÓN DEVOLUCIÓN -->
                <div id="seccionDevolucion" style="display: none;">
                    <hr>
                    <h4 class="text-info">📥 Devolución de Celular</h4>

                    <div class="form-group">
                        <label>Celulares a Devolver <span class="text-danger">*</span></label>
                        <input type="text" id="buscarDevolucion" class="form-control mb-2" placeholder="🔍 Buscar por modelo, IMEI o N/S...">
                        <div class="card">
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <?php if (empty($celulares_asignados)): ?>
                                    <p class="text-muted">No hay celulares asignados</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="tablaDevolucion">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="50">Sel.</th>
                                                    <th>Modelo</th>
                                                    <th>IMEI</th>
                                                    <th>N/S</th>
                                                    <th>Descripción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($celulares_asignados as $cel): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" 
                                                                       name="celulares_devolucion[]" 
                                                                       id="cel_devol_<?= $cel['id'] ?>" 
                                                                       value="<?= $cel['id'] ?>">
                                                                <label class="custom-control-label" for="cel_devol_<?= $cel['id'] ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td><strong><?= esc($cel['modelo']) ?></strong></td>
                                                        <td><code><?= esc($cel['imei']) ?></code></td>
                                                        <td><?= esc($cel['numero_serie'] ?? 'N/A') ?></td>
                                                        <td><small><?= esc(substr($cel['descripcion'] ?? '', 0, 50)) ?><?= strlen($cel['descripcion'] ?? '') > 50 ? '...' : '' ?></small></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <span id="contadorDevolucion">0</span> celular(es) seleccionado(s)
                        </small>
                    </div>
                </div>

                <!-- RESPONSABLE -->
                <div class="form-group">
                    <label>Responsable de la Acción <span class="text-danger">*</span></label>
                    <input type="text" name="responsable_nombre" class="form-control" required
                           placeholder="Nombre completo de quien entrega o recibe el celular"
                           value="<?= esc(session()->get('nombre_usuario') ?? '') ?>">
                    <small class="form-text text-muted">Ejemplo: Juan Pérez García - Responsable de Patrimonio</small>
                </div>

                <!-- OBSERVACIONES -->
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3" 
                              placeholder="Observaciones o comentarios adicionales"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Guardar Movimiento</button>
                <a href="<?= base_url('celulares/movimientos') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    console.log('Formulario de movimientos cargado');
    
    // Inicializar Select2 si está disponible
    if (typeof $.fn.select2 !== 'undefined') {
        try {
            $('.select2').select2({
                placeholder: '-- Seleccione --',
                allowClear: true,
                width: '100%'
            });
            console.log('Select2 inicializado');
        } catch(e) {
            console.log('Error al inicializar Select2:', e);
        }
    } else {
        console.log('Select2 no está disponible');
    }

    // Mostrar/ocultar secciones según tipo de movimiento
    $('#tipoMovimiento').on('change', function() {
        const tipo = $(this).val();
        console.log('Tipo de movimiento seleccionado:', tipo);
        
        // Ocultar ambas secciones primero
        $('#seccionEntrega').hide();
        $('#seccionDevolucion').hide();
        
        // Mostrar la sección correspondiente
        if (tipo === 'entrega') {
            $('#seccionEntrega').slideDown();
            console.log('Mostrando sección de entrega');
        } else if (tipo === 'devolucion') {
            $('#seccionDevolucion').slideDown();
            console.log('Mostrando sección de devolución');
        }
    });

    // Validación antes de enviar
    $('#formMovimiento').submit(function(e) {
        const tipo = $('#tipoMovimiento').val();
        const responsable = $('[name="responsable_nombre"]').val().trim();
        
        // Validar que se haya ingresado el responsable
        if (!responsable) {
            e.preventDefault();
            alert('Debe ingresar el nombre del responsable de la acción');
            $('[name="responsable_nombre"]').focus();
            return false;
        }
        
        if (tipo === 'entrega') {
            const persona = $('[name="id_personas"]').val();
            const depto = $('[name="id_departamentos"]').val();
            const local = $('[name="id_locales"]').val();
            const celulares = $('[name="celulares_entrega[]"]:checked').length;
            
            if (!persona || !depto || !local) {
                e.preventDefault();
                alert('Complete todos los campos requeridos para la entrega');
                return false;
            }
            
            if (celulares === 0) {
                e.preventDefault();
                alert('Seleccione al menos un celular para entregar');
                return false;
            }
        } else if (tipo === 'devolucion') {
            const celulares = $('[name="celulares_devolucion[]"]:checked').length;
            
            if (celulares === 0) {
                e.preventDefault();
                alert('Seleccione al menos un celular para devolver');
                return false;
            }
        }
    });

    // Búsqueda en tiempo real para celulares de entrega
    $('#buscarEntrega').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('#tablaEntrega tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });

    // Búsqueda en tiempo real para celulares de devolución
    $('#buscarDevolucion').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('#tablaDevolucion tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });

    // Contador de celulares seleccionados para entrega
    $('[name="celulares_entrega[]"]').on('change', function() {
        const count = $('[name="celulares_entrega[]"]:checked').length;
        $('#contadorEntrega').text(count);
    });

    // Contador de celulares seleccionados para devolución
    $('[name="celulares_devolucion[]"]').on('change', function() {
        const count = $('[name="celulares_devolucion[]"]:checked').length;
        $('#contadorDevolucion').text(count);
    });
});
</script>
<?= $this->endSection(); ?>
