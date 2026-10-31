<?php
// Clase para conectarnos a la base de datos con PDO
class Conexion {

    public function obtener() {
        $servidor = 'localhost';
        $base = 'repuestos_don_pancho';
        $usuario = 'root';
        $clave = '';

        try {
            $pdo = new PDO("mysql:host=$servidor;dbname=$base;charset=utf8mb4", $usuario, $clave);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }
}