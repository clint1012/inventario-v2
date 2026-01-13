<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login - Sistema de Inventario TC</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body { 
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      font-family: 'Nunito', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .login-container {
      width: 100%;
      max-width: 950px;
    }

    .card-login { 
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      border: none;
      background: white;
    }

    .bg-side { 
      background-image: url('<?= base_url('img/TC.jpeg') ?>');
      background-size: cover;
      background-position: center;
      position: relative;
      min-height: 600px;
    }

    .bg-side::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(139, 21, 56, 0.85) 0%, rgba(196, 30, 58, 0.85) 100%);
    }

    .side-content {
      position: relative;
      z-index: 1;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 3rem;
      color: white;
      text-align: center;
    }

    .side-logo {
      max-width: 300px;
      margin-bottom: 1.5rem;
    }

    .side-title {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      line-height: 1.3;
    }

    .side-subtitle {
      font-size: 1rem;
      opacity: 0.9;
      line-height: 1.6;
    }

    .login-form-side {
      padding: 3rem 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 600px;
    }

    .login-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .login-logo-small {
      max-width: 80px;
      margin-bottom: 1rem;
    }

    .login-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 0.5rem;
    }

    .login-subtitle {
      color: #718096;
      font-size: 0.95rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      font-weight: 600;
      color: #4a5568;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
      display: block;
    }

    .form-control {
      height: 50px;
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #c41e3a;
      box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.1);
      outline: none;
    }

    .input-icon {
      position: relative;
    }

    .input-icon i {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
    }

    .btn-login {
      height: 50px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 1rem;
      background: linear-gradient(135deg, #c41e3a 0%, #8B1538 100%);
      border: none;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(196, 30, 58, 0.3);
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(196, 30, 58, 0.4);
      background: linear-gradient(135deg, #a01830 0%, #6B0F2A 100%);
    }

    .alert {
      border-radius: 10px;
      border: none;
      padding: 1rem;
      font-size: 0.875rem;
    }

    .alert-danger {
      background: #fee;
      color: #c41e3a;
    }

    .system-info {
      text-align: center;
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 1px solid #e2e8f0;
      color: #718096;
      font-size: 0.85rem;
    }

    @media (max-width: 768px) {
      .bg-side {
        min-height: 250px;
      }

      .side-content {
        padding: 2rem;
      }

      .side-title {
        font-size: 1.25rem;
      }

      .side-subtitle {
        font-size: 0.875rem;
      }

      .login-form-side {
        padding: 2rem 1.5rem;
        min-height: auto;
      }

      .login-title {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
<div class="login-container">
  <div class="card card-login">
    <div class="row no-gutters">
      <!-- Lado izquierdo con imagen -->
      <div class="col-md-6 d-none d-md-block">
        <div class="bg-side">
          <div class="side-content">
            <img src="<?= base_url('img/tc_logo_negro.png') ?>" alt="Logo TC" class="side-logo">
            <h2 class="side-title">Sistema de Inventario OTI</h2>
            <p class="side-subtitle">Tribunal Constitucional del Perú</p>
          </div>
        </div>
      </div>

      <!-- Lado derecho con formulario -->
      <div class="col-md-6">
        <div class="login-form-side">
          <div class="login-header">
            <img src="<?= base_url('img/tc_logo_negro.png') ?>" alt="Logo TC" class="login-logo-small d-md-none">
            <h3 class="login-title">Bienvenido</h3>
            <p class="login-subtitle">Ingrese sus credenciales para acceder</p>
          </div>

          <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-circle mr-2"></i>
              <?= session()->getFlashdata('error') ?>
            </div>
          <?php endif; ?>

          <form action="<?= site_url('login/doLogin') ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>
            
            <div class="form-group">
              <label><i class="fas fa-user mr-1"></i> Usuario</label>
              <div class="input-icon">
                <input type="text" name="usuario" class="form-control" value="<?= old('usuario') ?>" 
                       placeholder="Ingrese su usuario" required autofocus>
                <i class="fas fa-user"></i>
              </div>
            </div>

            <div class="form-group">
              <label><i class="fas fa-lock mr-1"></i> Contraseña</label>
              <div class="input-icon">
                <input type="password" name="password" class="form-control" 
                       placeholder="Ingrese su contraseña" required>
                <i class="fas fa-lock"></i>
              </div>
            </div>

            <button type="submit" class="btn btn-login btn-block">
              <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
            </button>
          </form>

          <div class="system-info">
            <i class="fas fa-shield-alt mr-1"></i>
            Sistema de Inventario - Oficina de Tecnologías de la Información
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
