<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="card shadow-sm my-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Editar Inventario</h4>
            <a href="<?= base_url('inventario/listado'); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session('success') ?></div>
        <?php endif; ?>

        <form id="formInventario" action="<?= base_url('inventario/actualizar/' . $inventario['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="usuario_id" value="<?= $inventario['usuario_id'] ?>">

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="anioInventario">Año</label>
                    <input type="number" class="form-control" id="anioInventario" name="anio"
                        value="<?= esc($inventario['anio']) ?>" min="2000" max="2100" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="mesInventario">Mes</label>
                    <select class="form-control" id="mesInventario" name="mes" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $meses = [
                            'Enero',
                            'Febrero',
                            'Marzo',
                            'Abril',
                            'Mayo',
                            'Junio',
                            'Julio',
                            'Agosto',
                            'Septiembre',
                            'Octubre',
                            'Noviembre',
                            'Diciembre'
                        ];
                        foreach ($meses as $mes): ?>
                            <option value="<?= $mes ?>" <?= $inventario['mes'] === $mes ? 'selected' : '' ?>><?= $mes ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="usuarioBuscador">Usuario</label>
                    <input type="text" class="form-control" id="usuarioBuscador"
                        value="<?= esc($inventario['usuario']) ?>" readonly>
                    <input type="hidden" name="usuario_id" id="usuarioIdHidden"
                        value="<?= $inventario['usuario_id'] ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="regimenUsuario">Régimen Laboral</label>
                    <input type="text" class="form-control" id="regimenUsuario" readonly
                        value="<?= esc($inventario['regimen'] ?? 'Sin régimen') ?>">
                </div>
            </div>

            <div class="form-row" id="jefeContainer"
                style="<?= !empty($inventario['jefe_id']) ? 'display:block;' : 'display:none;' ?>">
                <div class="form-group col-md-12">
                    <label for="jefeBuscador">Jefe Responsable</label>
                    <input type="text" class="form-control" id="jefeBuscador"
                        placeholder="Digita 3 letras para buscar..." autocomplete="off"
                        value="<?= esc($inventario['jefe'] ?? '') ?>">
                    <input type="hidden" name="jefe_id" id="jefeIdHidden" value="<?= $inventario['jefe_id'] ?? '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="observacion">Observaciones</label>
                <textarea class="form-control" id="observacion" name="observacion"
                    rows="3"><?= esc($inventario['observacion'] ?? '') ?></textarea>
            </div>

            <div id="equiposAsignados" class="mt-4">
                <h5 class="mb-3">Bienes del inventario</h5>
                <?php if (!empty($detalles)): ?>
                    <div class="list-group">
                        <?php foreach ($detalles as $detalle): ?>
                            <label class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <strong><?= esc($detalle['descripcion'] ?? 'Equipo') ?></strong>
                                        <small class="d-block text-muted"><?= esc($detalle['cod_patrimonial'] ?? 'Sin SBN') ?> ·
                                            <?= esc($detalle['marca'] ?? '') ?></small>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block mb-1">¿Verificado?</small>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input"
                                                id="check_si_<?= $detalle['bien_id'] ?>"
                                                name="equipos[<?= $detalle['bien_id'] ?>][verificado]" value="1"
                                                <?= $detalle['verificado'] ? 'checked' : '' ?>>
                                            <label class="custom-control-label"
                                                for="check_si_<?= $detalle['bien_id'] ?>">Sí</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input"
                                                id="check_no_<?= $detalle['bien_id'] ?>"
                                                name="equipos[<?= $detalle['bien_id'] ?>][verificado]" value="0"
                                                <?= !$detalle['verificado'] ? 'checked' : '' ?>>
                                            <label class="custom-control-label"
                                                for="check_no_<?= $detalle['bien_id'] ?>">No</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted">Comentario</label>
                                        <input type="text" class="form-control form-control-sm"
                                            name="equipos[<?= $detalle['bien_id'] ?>][comentario]"
                                            value="<?= esc($detalle['comentario'] ?? '') ?>" placeholder="Observación">
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">No hay bienes asignados en este inventario.</div>
                <?php endif; ?>
            </div>

            <div class="text-right mt-4">
                <a href="<?= base_url('inventario/listado'); ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar inventario
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
    const buscarJefesUrl = "<?= base_url('inventario/buscar-jefes'); ?>";

    $('#jefeBuscador').autocomplete({
        minLength: 3,
        source: buscarJefesUrl,
        autoFocus: true,
        select: function (event, ui) {
            $('#jefeBuscador').val(ui.item.label);
            $('#jefeIdHidden').val(ui.item.id);
            return false;
        }
    }).on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const menu = $(this).autocomplete('widget');
            if (menu.is(':visible')) {
                const firstItem = menu.find('.ui-menu-item:first');
                if (firstItem.length) {
                    $(this).autocomplete('instance')._trigger('select', 'autocompleteselect', {
                        item: firstItem.data('ui-autocomplete-item')
                    });
                }
            }
        }
    });

    $('#formInventario').on('keydown', function (e) {
        if (e.key === 'Enter' && e.target.id === 'jefeBuscador') {
            e.preventDefault();
            return false;
        }
    });
</script>
<?= $this->endSection(); ?>