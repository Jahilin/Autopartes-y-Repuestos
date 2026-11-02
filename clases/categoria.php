<?php
require_once 'config/conexion.php';

class Categoria {
    private $pdo;

    public function __construct() {
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }

    public function listar() {
        $sql = 'SELECT c.id_categoria,
                       c.id_categoria AS id,
                       c.nombre,
                       COUNT(r.id_repuesto) AS total_repuestos
                FROM categorias c
                LEFT JOIN repuestos r ON r.id_categoria = c.id_categoria
                GROUP BY c.id_categoria, c.nombre
                ORDER BY c.nombre';
        return $this->pdo->query($sql)->fetchAll();
    }
}