<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login - Inventario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f3f5f7; }
    .card-login { border-radius: 10px; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,.08); }
    .bg-side { background-image: url('https://picsum.photos/800/800'); background-size: cover; background-position: center; }
  </style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center" style="margin-top:6%;">
    <div class="col-md-9">
      <div class="card card-login">
        <div class="row no-gutters">
          <div class="col-md-6 bg-side d-none d-md-block"></div>
          <div class="col-md-6 p-4">
            <h4 class="mb-4">Bienvenido</h4>

            <?php if(session()->getFlashdata('error')): ?>
              <div class="alert alert-danger small"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form action="<?= site_url('login/doLogin') ?>" method="post" autocomplete="off">
              <?= csrf_field() ?>
              <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" class="form-control" value="<?= old('usuario') ?>" required autofocus>
              </div>
              <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-primary btn-block">Ingresar</button>
            </form>

            <div class="mt-3 small text-muted">Sistema de Inventario</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
