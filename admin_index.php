<?php 
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/clases/Usuario.php';

Usuario::protegerPanel('admin', 'bodeguero');

$titulo = 'Panel de Inventario';
require __DIR__ . '/includes/head.php';
?>

<h2>Panel de control de Administracion</h2>

<?php require __DIR__ . '/includes/footer.php'; ?>
