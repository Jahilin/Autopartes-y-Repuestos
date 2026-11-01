<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/clases/Usuario.php';

Usuario::cerrarSesion();

session_start();
flash('info', 'Su sesión se cerró correctamente.');
header('Location:' . base_url('index.php'));
exit;
