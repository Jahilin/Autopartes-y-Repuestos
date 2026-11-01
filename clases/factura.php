<?php
require_once 'config/conexion.php';

class Factura {
    // clave => [modalidad, costo en lempiras]
    const FLETES = [
        'mostrador'  => ['Retiro en mostrador', 0],
        'moto'       => ['Moto express', 50],
        'camioncito' => ['Flete en camioncito', 180],
    ];

    private $pdo;

    public function __construct() {
        $conexion = new Conexion();
        $this->pdo = $conexion->obtener();
    }

    public static function calcular($lineas, $flete) {
        $piezas   = array_sum(array_column($lineas, 'cantidad'));
        $subtotal = 0;
        foreach ($lineas as $l) {
            $subtotal += $l['precio'] * $l['cantidad'];
        }

        $pct       = $piezas >= 10 ? 10 : ($piezas >= 3 ? 5 : 0);
        $descuento = round($subtotal * $pct / 100, 2);
        $gravado   = $subtotal - $descuento;
        $isv       = round($gravado * 0.15, 2);
        $costo     = self::FLETES[$flete][1];

        return [
            'subtotal'       => $subtotal,
            'porcentaje'     => $pct,
            'descuento'      => $descuento,
            'gravado'        => $gravado,
            'isv'            => $isv,
            'modalidad'      => self::FLETES[$flete][0],
            'flete'          => $costo,
            'total'          => $gravado + $isv + $costo,
        ];
    }

    // Guarda la factura y su detalle; devuelve la factura completa
    public function guardar($cliente, $lineas, $flete) {
        $f = self::calcular($lineas, $flete);

        $this->pdo->beginTransaction();
        try {
            $ultimo = $this->pdo->query(
                'SELECT COALESCE(MAX(CAST(RIGHT(numero_factura, 8) AS UNSIGNED)), 0) FROM facturas FOR UPDATE'
            )->fetchColumn();
            $numero = sprintf('000-001-01-%08d', $ultimo + 1);

            $sql = 'INSERT INTO facturas (numero_factura, cliente_nombre, rtn, telefono, modalidad_flete,
                        costo_flete, subtotal, porcentaje_descuento, monto_descuento, isv, total)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $this->pdo->prepare($sql)->execute([
                $numero, $cliente['nombre'], $cliente['rtn'], $cliente['telefono'], $f['modalidad'],
                $f['flete'], $f['subtotal'], $f['porcentaje'], $f['descuento'], $f['isv'], $f['total'],
            ]);
            $idFactura = $this->pdo->lastInsertId();

            $det = $this->pdo->prepare(
                'INSERT INTO detalle_factura (id_factura, id_repuesto, descripcion, cantidad, precio_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            foreach ($lineas as $l) {
                $det->execute([$idFactura, $l['id'], $l['nombre'], $l['cantidad'], $l['precio'], $l['precio'] * $l['cantidad']]);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        $f['numero'] = $numero;
        $f['fecha']  = date('d/m/Y H:i');
        return $f;
    }
}