<?php
require_once 'config/conexion.php';

class Historial {
    private $pdo;

    public function __construct() {
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }
    
    public function listar() {
        $consulta = $this->pdo->query('SELECT * FROM facturas ORDER BY fecha DESC');
        $facturas = $consulta->fetchAll();

        foreach ($facturas as $i => $factura) {
            $sql = 'SELECT * FROM detalle_factura WHERE id_factura = ?';
            $detalle = $this->pdo->prepare($sql);
            $detalle->execute([$factura['id_factura']]);
            $facturas[$i]['detalle'] = $detalle->fetchAll();
        }

        return $facturas;
    }
}