<?php
// Procesa crear, editar y eliminar Repuestos - Se responde con JSON
ini_set('display_errors', '0'); //para que un aviso no rompa el JSON
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ .'/includes/auth_check.php';
require_once __DIR__ .'/config/conexion.php';
require_once __DIR__ .'/clases/subidaArchivo.php';

function responder(bool $ok, string $mensaje): void{
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    responder(false, 'Metodo no permitido');
}

$pdo = (new Conexion())->obtener();
$carpetaUploads = __DIR__ . '/uploads/';

// TEMPORAL:  reemplazar por Repuesto::crear / actualizar / eliminar / buscarPorId
function repuestoBuscar(PDO $pdo, int $id): ?array{
    $st = $pdo->prepare('SELECT * FROM repuestos WHERE id_repuesto = ?');
    $st->execute([$id]);
    return $st->fetch() ?:null;
}

function repuestoCrear(PDO $pdo, array $d): void {
    $st= $pdo->prepare('INSERT INTO repuestos (codigo, nombre, especificaciones, 
    precio, stock, fotografia, destacado, id_categoria)
    VALUES (:codigo, :nombre, :especificaciones, :precio, :stock, :fotografia, :destacado, :id_categoria)');
    $st->execute($d);
}
function repuestoActualizar(PDO $pdo, int $id, array $d): void {
    $d['id_repuesto'] = $id;
    $st = $pdo->prepare(
        'UPDATE repuestos SET codigo = :codigo, nombre = :nombre, especificaciones = :especificaciones, precio = :precio, stock = :stock, fotografia = :fotografia,
        destacado = :destacado, id_categoria = :id_categoria
        WHERE id_repuesto = :id_repuesto'
    );
    $st->execute($d);
}
function repuestoEliminar(PDO $pdo, int $id): void {
    $st = $pdo->prepare('DELETE FROM repuestos WHERE id_repuesto = ?');
    $st->execute([$id]);
}
//FIN TEMPORAL


?>