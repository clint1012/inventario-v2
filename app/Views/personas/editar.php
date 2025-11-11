<?= $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<div class="container-fluid form-container mt-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-user-edit"></i> Editar Persona</h4>
            <a href="<?= base_url('personas') ?>" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i>
                Volver</a>
        </div>

        <div class="card-body">
            <?php if (session('error')): ?>
                <div class="alert alert-danger"><?= session('error') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('personas/update/' . $persona['id']) ?>" method="post">
                <div class="row">
                    <!-- Columna 1 -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="form-section">
                            <div class="form-group">
                                <label for="dni">DNI</label>
                                <input type="text" class="form-control" name="dni" id="dni"
                                    value="<?= old('dni', $persona['dni']) ?>" required maxlength="8">
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombres</label>
                                <input type="text" class="form-control" name="nombre" id="nombre"
                                    value="<?= old('nombre', $persona['nombre']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="ape_paterno">Apellido Paterno</label>
                                <input type="text" class="form-control" name="ape_paterno" id="ape_paterno"
                                    value="<?= old('ape_paterno', $persona['ape_paterno']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="ape_materno">Apellido Materno</label>
                                <input type="text" class="form-control" name="ape_materno" id="ape_materno"
                                    value="<?= old('ape_materno', $persona['ape_materno']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="form-section">
                            <div class="form-group">
                                <label for="id_regimen_laboral">Régimen Laboral</label>
                                <select name="id_regimen_laboral" id="id_regimen_laboral" class="form-control" required>
                                    <option value="">Seleccione un régimen</option>
                                    <?php foreach ($regimenes as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= (old('id_regimen_laboral', $persona['id_regimen_laboral']) == $r['id']) ? 'selected' : '' ?>>
                                            <?= esc($r['regimen_laboral']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Inicio Contrato</label>
                                <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                                    value="<?= old('fecha_inicio', $persona['fecha_inicio']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="fecha_fin">Fecha Fin Contrato</label>
                                <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                                    value="<?= old('fecha_fin', $persona['fecha_fin']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="modalidad">Modalidad</label>
                                <select name="modalidad" id="modalidad" class="form-control">
                                    <option value="">Selecciona una modalidad</option>
                                    <option value="teletrabajo_total" <?= (old('modalidad', $persona['modalidad']) == 'teletrabajo_total') ? 'selected' : '' ?>>Teletrabajo
                                        Total</option>
                                    <option value="teletrabajo_parcial" <?= (old('modalidad', $persona['modalidad']) == 'teletrabajo_parcial') ? 'selected' : '' ?>>Teletrabajo
                                        Parcial</option>
                                    <option value="presencial" <?= (old('modalidad', $persona['modalidad']) == 'presencial') ? 'selected' : '' ?>>Presencial</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 3 -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="form-section">
                            <div class="form-group">
                                <label for="correo">Correo</label>
                                <input type="email" class="form-control" name="correo" id="correo"
                                    value="<?= old('correo', $persona['correo']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" id="telefono"
                                    value="<?= old('telefono', $persona['telefono']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="direccion_domiciliaria">Dirección Domiciliaria</label>
                                <input type="text" class="form-control" name="direccion_domiciliaria"
                                    id="direccion_domiciliaria"
                                    value="<?= old('direccion_domiciliaria', $persona['direccion_domiciliaria']) ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="id_locales">Local</label>
                                <select name="id_locales" id="id_locales" class="form-control" required>
                                    <option value="">Seleccione un local</option>
                                    <?php foreach ($locales as $l): ?>
                                        <option value="<?= $l['id'] ?>" <?= (old('id_locales', $persona['id_locales']) == $l['id']) ? 'selected' : '' ?>>
                                            <?= esc($l['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 mb-2">
                    <button type="submit" class="btn btn-warning btn-lg px-5"><i class="fas fa-save"></i>
                        Actualizar</button>
                    <a href="<?= base_url('personas') ?>" class="btn btn-secondary btn-lg px-5"><i
                            class="fas fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>