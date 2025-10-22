<?php echo $this->extend('plantilla'); ?>
<?= $this->section('contenido'); ?>

<!-- Título + botón crear -->
<div class="d-flex justify-content-between mb-3">
    <h4>Usuarios</h4>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalCrear">
        <i class="fa fa-plus"></i> Crear Usuario
    </button>
</div>

<!-- Mensajes flash (solo si vienes de una acción no-AJAX) -->
<?php if(session()->getFlashdata('success')): ?>
  <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
  <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- Tabla -->
<table id="tblUsuarios" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Estado</th>
            <th>Roles</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= esc($u['usuario']) ?></td>
            <td><?= esc($u['nombre']) ?></td>
            <td><?= esc($u['correo']) ?></td>
            <td>
                <?php if ($u['estado'] === 'activo'): ?>
                    <span class="badge badge-success">Activo</span>
                <?php else: ?>
                    <span class="badge badge-danger">Inactivo</span>
                <?php endif; ?>
            </td>
            <td>
                <?php
                  $db = \Config\Database::connect();
                  $query = $db->query("SELECT r.nombre FROM roles r JOIN usuarios_roles ur ON ur.rol_id=r.id WHERE ur.usuario_id=?", [$u['id']]);
                  $rs = $query->getResultArray();
                  foreach($rs as $r) echo "<span class='badge badge-secondary mr-1'>".esc($r['nombre'])."</span>";
                ?>
            </td>
            <td>
                <button class="btn btn-sm btn-info btn-edit" data-id="<?= $u['id'] ?>">
                    <i class="fa fa-edit"></i> Editar
                </button>

                <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#modalRoles" data-id="<?= $u['id'] ?>">
                    <i class="fa fa-user-shield"></i> Roles
                </button>

                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalPermisos" data-id="<?= $u['id'] ?>">
                    <i class="fa fa-key"></i> Permisos
                </button>

                <button class="btn btn-sm <?= $u['estado'] === 'activo' ? 'btn-danger' : 'btn-success' ?> btn-toggle" data-id="<?= $u['id'] ?>">
                    <i class="fa <?= $u['estado'] === 'activo' ? 'fa-toggle-off' : 'fa-toggle-on' ?>"></i>
                    <?= $u['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- MODALES (igual que antes) -->
<!-- MODAL: Crear Usuario -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCrear">
        <div class="modal-header">
          <h5 class="modal-title">Crear usuario</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Usuario</label>
                <input name="usuario" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nombre</label>
                <input name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input name="correo" type="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input name="password" type="password" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: Editar Usuario -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEdit">
        <div class="modal-header">
          <h5 class="modal-title">Editar usuario</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Usuario</label>
                <input id="edit_usuario" name="usuario" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nombre</label>
                <input id="edit_nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input id="edit_correo" name="correo" type="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Nueva contraseña (opcional)</label>
                <input name="password" type="password" class="form-control" placeholder="Dejar vacío para no cambiar">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: Roles (asignar) -->
<div class="modal fade" id="modalRoles" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formRoles">
        <div class="modal-header">
          <h5 class="modal-title">Asignar roles</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="rolesBody">
          <div class="text-center">Cargando...</div>
        </div>
        <input type="hidden" name="usuario_id" id="roles_usuario_id">
        <?= csrf_field() ?>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Guardar roles</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: Permisos (editable) -->
<div class="modal fade" id="modalPermisos" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formPermisos">
        <div class="modal-header">
          <h5 class="modal-title">Permisos del Usuario</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body" id="permisosBody">
          <div class="text-center">Cargando...</div>
        </div>

        <input type="hidden" name="usuario_id" id="perm_usuario_id">
        <?= csrf_field() ?>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-save"></i> Guardar permisos
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Dependencias CSS/JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){

    // Inicializar DataTable
    $('#tblUsuarios').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
    });

    // ---------- Obtener datos para Editar ----------
    $(document).on('click', '.btn-edit', function(){
        var id = $(this).data('id');

        $.ajax({
            url: BASE_URL + '/usuarios/get/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(resp){
                if (!resp.ok) {
                    Swal.fire('Error', resp.msg || 'No se encontró usuario', 'error');
                    return;
                }
                var u = resp.data;
                $('#edit_id').val(u.id);
                $('#edit_usuario').val(u.usuario);
                $('#edit_nombre').val(u.nombre);
                $('#edit_correo').val(u.correo);
                $('#formEdit').attr('action', BASE_URL + '/usuarios/update/' + u.id);
                $('#modalEdit').modal('show');
            },
            error: function(xhr){
                Swal.fire('Error','No se pudo obtener el usuario','error');
                console.error(xhr);
            }
        });
    });

    // ---------- Crear usuario (AJAX) ----------
    $('#formCrear').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            url: BASE_URL + '/usuarios/store',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(resp){
                if (resp.ok) {
                    $('#modalCrear').modal('hide');
                    Swal.fire('Hecho', resp.msg, 'success').then(()=> location.reload());
                } else {
                    Swal.fire('Error', resp.msg || 'No se pudo crear', 'error');
                }
            },
            error: function(xhr){
                Swal.fire('Error','No se pudo crear (ver consola)','error');
                console.error(xhr);
            }
        });
    });

    // ---------- Editar usuario (AJAX) ----------
    $('#formEdit').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        var action = $(this).attr('action');

        $.ajax({
            url: action,
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(resp){
                if (resp.ok) {
                    $('#modalEdit').modal('hide');
                    Swal.fire('Hecho', resp.msg, 'success').then(()=> location.reload());
                } else {
                    Swal.fire('Error', resp.msg || 'No se pudo actualizar', 'error');
                }
            },
            error: function(xhr){
                Swal.fire('Error','No se pudo actualizar (ver consola)','error');
                console.error(xhr);
            }
        });
    });

    // ---------- Modal Roles ----------
    $('#modalRoles').on('show.bs.modal', function(e){
        var uid = $(e.relatedTarget).data('id');
        $('#roles_usuario_id').val(uid);
        $('#rolesBody').html('<div class="text-center">Cargando...</div>');

        $.get(BASE_URL + '/usuarios/roles/' + uid, function(resp){
            if (!resp.ok) {
                $('#rolesBody').html('<div class="text-danger">No se pudo cargar roles</div>');
                return;
            }
            var html = '';
            resp.roles.forEach(function(r){
                var checked = resp.assigned.includes(r.id) ? 'checked' : '';
                html += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="${r.id}" ${checked}>
                        <label class="form-check-label">${r.nombre}</label>
                    </div>`;
            });
            $('#rolesBody').html(html);

        }, 'json').fail(function(){
            $('#rolesBody').html('<div class="text-danger">Error al cargar roles</div>');
        });
    });

    // Guardar roles
    $('#formRoles').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: BASE_URL + '/usuarios/roles/save',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if (resp.ok) {
                    $('#modalRoles').modal('hide');
                    Swal.fire('Hecho', resp.msg, 'success').then(()=> location.reload());
                } else {
                    Swal.fire('Error', resp.msg || 'No se pudo asignar roles', 'error');
                }
            },
            error: function(xhr){
                Swal.fire('Error','No se pudo guardar roles','error');
                console.error(xhr);
            }
        });
    });

    // Guardar permisos
    $('#formPermisos').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: BASE_URL + '/usuarios/savePermisos',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if (resp.ok) {
                    $('#modalPermisos').modal('hide');
                    Swal.fire('Hecho', resp.msg, 'success');
                } else {
                    Swal.fire('Error', resp.msg || 'No se pudo guardar permisos', 'error');
                }
            },
            error: function(xhr){
                Swal.fire('Error','No se pudo guardar permisos','error');
                console.error(xhr);
            }
        });
    });


    // ---------- Modal Permisos ----------
    $('#modalPermisos').on('show.bs.modal', function(e){
    var uid = $(e.relatedTarget).data('id');
    $('#perm_usuario_id').val(uid);
    $('#permisosBody').html('<div class="text-center">Cargando...</div>');

    $.get(BASE_URL + '/usuarios/permisos/' + uid, function(resp){
        if (!resp.ok) {
            $('#permisosBody').html('<div class="text-danger">No se pudo cargar permisos</div>');
            return;
        }
        var html = '';
        resp.permisos.forEach(function(p){
            var checked = p.permitido == 1 ? 'checked' : '';
            html += `
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="permisos[]" value="${p.id}" ${checked}>
                <label class="form-check-label">${p.descripcion}</label>
            </div>`;
        });
        $('#permisosBody').html(html);

    }, 'json');
    });


    // ---------- Activar/Inactivar ----------
    $(document).on('click', '.btn-toggle', function(){
        var id = $(this).data('id');

        Swal.fire({
            title: '¿Confirmar?',
            text: 'Cambiar estado del usuario',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + '/usuarios/toggle/' + id,
                    method: 'POST',
                    dataType: 'json',
                    success: function(resp){
                        if (resp.ok) {
                            Swal.fire('Listo', resp.msg, 'success').then(()=> location.reload());
                        } else {
                            Swal.fire('Error', resp.msg || 'No se pudo actualizar', 'error');
                        }
                    },
                    error: function(xhr){
                        Swal.fire('Error','No se pudo actualizar (ver consola)','error');
                        console.error(xhr);
                    }
                });
            }
        });
    });

});
</script>


<?= $this->endSection(); ?>
