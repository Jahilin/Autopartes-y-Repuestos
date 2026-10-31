<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/clases/Usuario.php';


if (Usuario::estaAutenticado()) {
    $rol = Usuario::rol();
    if ($rol === 'admin' || $rol === 'bodeguero') {
        header('Location: admin_index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}


$error = null;
$email = '';

if (metodo_post()) {
    $email = trim(($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Ingrese su correo y contraseña.';
    } else {
        $usuarioModel = new Usuario();
        if ($usuarioModel->autenticar($email, $password)) {
            flash('success', 'Bienvenido de nuevo, ' . Usuario::usuarioActual()['nombre'] . '!');
            $rol = Usuario::rol();
            if ($rol === 'admin' || $rol === 'bodeguero') {
                header('Location: admin_index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
}

$titulo = 'Iniciar Sesión';
require __DIR__ . '/includes/head.php';
?>


<div class="container auth-wrap py-5">
    <div class="row justify-content-center w-100">
        <div class="col-lg-9 col-xl-8">
            <div class="card auth-card">
                <div class="row g-0">
                    <div class="col-md-5 auth-side d-flex flex-column justify-content-center">
                        <span class="brand-badge mb-3">DP</span>
                        <h4 class="fw-bold">Panel Administrativo</h4>
                        <p class="small text-white-50 mb-3">
                            Área privada para la gestión del inventario, control de roles y bodega de repuestos.
                        </p>
                        <ul class="list-unstyled small d-grid gap-2 mb-0">
                            <li><i class="bi bi-shield-lock text-warning me-2"></i>Acceso por roles</li>
                            <li><i class="bi bi-box-seam text-warning me-2"></i>CRUD de inventario</li>
                            <li><i class="bi bi-image text-warning me-2"></i>Subida segura de fotos</li>
                        </ul>
                    </div>
                    <div class="col-md-7">
                        <div class="p-4 p-md-5">
                            <h5 class="fw-bold mb-1">Iniciar Sesión</h5>
                            <p class="text-muted small mb-4">Ingrese sus credenciales para continuar.</p>

                            <?php if ($error): ?>
                                <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?></div>
                            <?php endif; ?>

                            <form method="post" action="login.php">
                                <?= csrf_campo() ?>
                                <div class="mb-3">
                                    <label class="form-label">Correo electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" value="<?= e($email) ?>" required autofocus>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dp w-100">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Entrar al panel
                                </button>
                            </form>

                            <div class="alert alert-light border small mt-4 mb-0 text-center">
                                <strong>Usuarios de prueba: (invisibles)</strong><br><br>
                                <div class="text-white">
                                    Admin: admin@donpancho.hn / Admin123 <br> Bodega: bodega@donpancho.hn / Bodega123
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
