<?php
require_once 'config/conexion.php';

class Repuesto {
    private $pdo;

    // Conexión
    public function __construct() {
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }

    // Traemos todos los repuestos con su respectiva categoría
    public function listarTodos() {
        $sql = 'SELECT r.*, c.nombre AS categoria
                FROM repuestos r
                INNER JOIN categorias c ON r.id_categoria = c.id_categoria
                ORDER BY r.nombre';
        $consulta = $this->pdo->query($sql);
        return $consulta->fetchAll();
    }

    // Traemos los respuestos de una categoría
    public function listarPorCategoria($idCategoria) {
        $sql = 'SELECT * FROM repuestos WHERE id_categoria = ? ORDER BY nombre';
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$idCategoria]);
        return $consulta->fetchAll();
    }

    // Aca traemos los repuestos que dejaremos como destacados en el inicio
    public function listarDestacados() {
        $sql = 'SELECT * FROM repuestos WHERE destacado = 1 LIMIT 5';
        $consulta = $this->pdo->query($sql);
        return $consulta->fetchAll();
    }

    // Aca buscamos un solo repuesto, por su id
    public function buscarPorId($id) {
        $sql = 'SELECT * FROM repuestos WHERE id_repuesto = ?';
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$id]);
        return $consulta->fetch();
    }

    // Crear un repuesto nuevo y devuelve su id
    public function crear($codigo, $nombre, $especificaciones, $precio, $stock, $fotografia, $destacado, $idCategoria) {
        $sql = 'INSERT INTO repuestos (codigo, nombre, especificaciones, precio, stock, fotografia, destacado, id_categoria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$codigo, $nombre, $especificaciones, $precio, $stock, $fotografia, $destacado, $idCategoria]);
        return $this->pdo->lastInsertId();
    }

    // Actualizar cambia los datos de un repuesto que ya existe
    public function actualizar($id, $codigo, $nombre, $especificaciones, $precio, $stock, $fotografia, $destacado, $idCategoria) {
        $sql = 'UPDATE repuestos
                SET codigo = ?, nombre = ?, especificaciones = ?, precio = ?, stock = ?, fotografia = ?, destacado = ?, id_categoria = ?
                WHERE id_repuesto = ?';
        $consulta = $this->pdo->prepare($sql);
        return $consulta->execute([$codigo, $nombre, $especificaciones, $precio, $stock, $fotografia, $destacado, $idCategoria, $id]);
    }

    // Aca podemos eliminar un repuesto
    public function eliminar($id) {
        $sql = 'DELETE FROM repuestos WHERE id_repuesto = ?';
        $consulta = $this->pdo->prepare($sql);
        return $consulta->execute([$id]);
    }
}