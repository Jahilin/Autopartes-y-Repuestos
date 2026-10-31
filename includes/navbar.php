<?php 
require_once __DIR__ . '/../clases/Usuario.php';

$usuarioActual = Usuario::usuarioActual();
$enAdmin = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/');
?>

<nav class="navbar navbar-expand-lg navbar-dp sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= e(base_url('index.php')) ?>">
            <span class="brand-badge text-white">DP</span>
            <span>Autopartes <span>Don Pancho</span></span>
        </a>
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navPrincipal">
            <i class="bi bi-list fs-2 text-white"></i>
        </button>
        <div class="collapse navbar-collapse" id="navPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="">Catálogo</a></li>
                <li class="nav-item"><a class="nav-link" href="">Arma tu Kit</a></li>
                <li class="nav-item"><a class="nav-link" href="">Sucursales</a></li>
                <?php if ($usuarioActual): ?>
                    <li class="nav-item"><a class="nav-link <?= $enAdmin ? 'active' : '' ?>" href="<?= e(base_url('admin/index.php')) ?>"><i class="bi bi-speedometer2 me-1"></i>Panel</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <?php if ($usuarioActual): ?>
                    <span class="text-white small">
                        <i class="bi bi-person-circle me-1"></i><?= e($usuarioActual['nombre']) ?>
                        <span class="badge bg-warning text-dark ms-1"><?= e(etiqueta_rol($usuarioActual['rol'])) ?></span>
                    </span>
                    <a class="btn btn-sm btn-outline-light" href="<?= e(base_url('logout.php')) ?>">
                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                    </a>
                <?php else: ?>
                    <a class="btn btn-sm btn-dp" href="<?= e(base_url('login.php')) ?>">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>