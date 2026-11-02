<?php

function e(?string $valor): string{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

//Revisa si el metodo del formulario es POST
function metodo_post(): bool{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

//Funciones para enviar mensajes de inicio de sesion
function flash(string $tipo, string $mensaje): void{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function etiqueta_rol(?string $rol): string{
    return match ($rol) {
        'admin'     => 'Admin',
        'bodeguero' => 'Bodega',
        default     => ucfirst((string) $rol),
    };
}


function obtener_flash(): array{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}

// Tokens crsf para mantener al usuario extraido de MDN https://developer.mozilla.org/en-US/docs/Web/Security/Attacks/CSRF
function csrf_token(): string{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_campo(): string{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verificar(?string $token): bool{
    return !empty($_SESSION['csrf_token']) && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Para acceder a las rutas mas facil, limpia la ruta y busca a partir de donde ya esta el archivo

function base_url(string $ruta = ''): string
{
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $raiz = preg_replace('#/(admin)$#', '', $script);
    $raiz = rtrim($raiz, '/');
    return $raiz . '/' . ltrim($ruta, '/');
}

function dinero(float $valor): string{
    return 'L. ' . number_format($valor, 2, '.', ',');
}
