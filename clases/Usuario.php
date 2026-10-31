<?php
    declare(strict_types=1);
    require_once 'config/conexion.php';
    
class Usuario{

    private PDO $pdo;

    public function __construct(){
        
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }

    public function autenticar(string $email, string $password): bool
    {
        $sql = 'SELECT * FROM usuarios WHERE email = :email AND activo = 1 LIMIT 1';
        $stmt = $this->pdo->prepare($sql);    
        $stmt ->execute(['email' => $email]);

        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'     => (int) $usuario['id'],
            'nombre' => (string) $usuario['nombre'],
            'email'  => (string) $usuario['email'],
            'rol'    => (string) $usuario['rol'],
        ];
        $_SESSION['ultimo_acceso'] = time();

        return true;
    }

     public static function cerrarSesion(): void{
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
        }
        session_destroy();
    }

    public static function usuarioActual(): ?array{
        return $_SESSION['usuario'] ?? null;
    }

    public static function estaAutenticado(): bool {
        return !empty($_SESSION['usuario']['id']);
    }

    public static function rol(): ?string {
        return $_SESSION['usuario']['rol'] ?? null;
    }

    public static function esAdmin(): bool {
        return self::rol() === 'admin';
    }

}

?>