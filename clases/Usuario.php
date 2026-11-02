<?php
    declare(strict_types=1);
    require_once __DIR__ . '/../config/conexion.php';
    require_once __DIR__ . '/../includes/helpers.php';
    
class Usuario{

    private PDO $pdo;

    public function __construct(){
        
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }

    public function autenticar(string $email, string $password): bool
    {
        $sql = 'SELECT u.*, r.nombre AS rol_nombre 
                FROM usuarios u 
                INNER JOIN roles r ON r.id_rol = u.id_rol 
                WHERE u.email = :email';
        $stmt = $this->pdo->prepare($sql);    
        $stmt->execute(['email' => $email]);

        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'     => (int) $usuario['id_usuario'],
            'nombre' => (string) $usuario['nombre'],
            'email'  => (string) $usuario['email'],
            'rol'    => strtolower(trim($usuario['rol_nombre'])), // admin | bodega
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

     /**
     * Protección de rutas  para el panel administrativo.
     */

     public static function tieneRol(string ...$roles): bool{
        return self::estaAutenticado() && in_array(self::rol(), $roles, true);
    }

    public static function protegerPanel(string ...$roles): void{
        $roles = $roles ?: ['admin', 'bodeguero'];
        if (!self::tieneRol(...$roles)) {
            flash('warning', 'Debe iniciar sesión con un usuario autorizado para acceder al panel.');
            header('Location: login.php');
            exit;
        }
    }

    /*Funcion de pruebas para ver usuarios

    public function listar(): array{
        $sql = 'SELECT id_usuario, nombre, email, id_rol FROM usuarios ORDER BY id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->fetchAll();
    }
*/
}

?>