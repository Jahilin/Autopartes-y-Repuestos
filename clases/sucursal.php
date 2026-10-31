<?php
require_once 'config/conexion.php';

class Sucursal {
    private $pdo;

    public function __construct() {
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }

    public function listar() {
        $consulta = $this->pdo->query('SELECT * FROM sucursales ORDER BY id_sucursal');
        return $consulta->fetchAll();
    }
}