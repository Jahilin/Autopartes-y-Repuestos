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

//Borra una foto de uploads/
function borrarFoto(string $carpeta, ?string $nombre): void{
    if(!$nombre) return;
    $ruta = $carpeta . basename($nombre);
    if(is_file($ruta)) unlink($ruta);
}

//Valida y limpia el formulario
function leerDatos(): array{
    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $especificaciones = trim($_POST['especificaciones'] ?? '');
    $idCategoria = (int)($_POST['id_categoria'] ?? 0);
    $precio = $_POST['precio'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $destacado = ($_POST['destacado'] ?? '0') === '1' ? 1:0;

    if($codigo === '' || strlen($codigo)>20) responder(false, 'Codigo obligatorio (maximo 20 caracteres).');
    if($nombre === '' || strlen($nombre)>120) responder(false, 'nombre obligatorio (maximo 120 caracteres).');
    if($especificaciones === '') responder(false, 'las especificaciones son obligatorias');
    if($idCategoria <= 0) responder(false, 'Seleccione una categoria');
    if(!is_numeric($precio) || (float)$precio < 0) responder(false, 'El precio debe ser un numero mayor o igual a 0');
    if(filter_var($stock, FILTER_VALIDATE_INT) === false || (int)$stock < 0) responder(false, 'las existencias deben ser mayor o iguales a 0');

    return [
    'codigo' => $codigo,
    'nombre' => $nombre,
    'especificaciones' => $especificaciones,
    'precio' => round((float)$precio, 2),
    'stock' => (int)$stock,
    'destacado' => $destacado,
    'id_categoria' => $idCategoria,
    ];
}

//El usuario elije una imagen
function hayFotoNueva(): bool{
return isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] !== UPLOAD_ERR_NO_FILE;
}

$accion = $_POST['accion'] ?? '';
try{
    switch($accion){
        case 'crear':
            $datos = leerDatos();
            $datos['fotografia'] = null;
            
            if(hayFotoNueva()){
                $subida = new subidaArchivo($carpetaUploads);
                if (!$subida->subir('fotografia')){
                    responder (false, $subida->getMensajeError());
                }
                $datos['fotografia'] = $subida->getNombreGuardado();
            }
            try {
                repuestoCrear($pdo, $datos);
            } catch (PDOException $e){
                borrarFoto($carpetaUploads, $datos['fotografia']); // no deja fotos huerfanas 
                throw $e;
            }
            responder(true, 'Repuesto registrado correctamente.');
        
        case 'editar':
            $id = (int)($_POST['id_repuesto'] ?? 0);
            $actual = repuestoBuscar($pdo, $id);
            if (!$actual) responder(false, 'El repuesto no existe.');

            $datos = leerDatos();
            $datos['fotografia'] = $actual['fotografia']; // se conserva si no hay foto nueva

            if (hayFotoNueva()) {
                $subida = new SubidaArchivo($carpetaUploads);
                if (!$subida->subir('fotografia')) {
                    responder(false, $subida->getMensajeError());
                }
                $datos['fotografia'] = $subida->getNombreGuardado();
            }
            try {
                repuestoActualizar($pdo, $id, $datos);
            } catch (PDOException $e) {
                if ($datos['fotografia'] !== $actual['fotografia']) {
                    borrarFoto($carpetaUploads, $datos['fotografia']);
                }
                throw $e;
            }

             // Si se cambio la foto, se borra la anterior
            if ($datos['fotografia'] !== $actual['fotografia']) {
                borrarFoto($carpetaUploads, $actual['fotografia']);
            }
            responder(true, 'Repuesto actualizado correctamente.');

        case 'eliminar':
            $id = (int)($_POST['id_repuesto'] ?? 0);
            $actual = repuestoBuscar($pdo, $id);
            if (!$actual) responder(false, 'El repuesto no existe.');

            repuestoEliminar($pdo, $id);
            borrarFoto($carpetaUploads, $actual['fotografia']);
            responder(true, 'Repuesto "' . $actual['nombre'] . '" eliminado correctamente.');

        default:
            responder(false, 'Acción no valida.');
    }
} catch (PDOException $e){
    if($e->getCode() === '23000'){
        responder(false, 'Ya existe un repuesto con ese codigo o la categoria no es valida.');
    }
    responder(false, 'Error en la base de datos. Intente de nuevo');
}
?>