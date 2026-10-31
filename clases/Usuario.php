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
}

?>