<?php
require_once 'config/conexion.php';

class Categoria {
    private $pdo;

    public function __construct() {
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }

    public function listar() {
        $consulta = $this->pdo->query('SELECT * FROM categorias ORDER BY id_categoria');
        return $consulta->fetchAll();
    }
}