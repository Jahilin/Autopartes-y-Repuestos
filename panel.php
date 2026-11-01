<?php
//Proteccion de rutas (lo termina el 2do integrante. Mientras este vacio no bloquea)
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/conexion.php';

//Temporal: se remplazara por Repuesto::listarTodos() y Categoria::listar()
$pdo = (new Conexion())->obtener();
$repuestos= $pdo-> query(
    "SELECT r.*, c.nombre AS categoria FROM repuestos r
    INNER JOIN categorias c ON c.id_categoria = r.id_categoria
    ORDER BY r.id_repuesto"
)->fetchAll();
$categorias = $pdo->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre")->fetchAll();
// FIN TEMPORAL

function e($texto){return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');}

// Imagen para repuestos sin fotografia
$sinFoto = 'data:image/svg+xml;utf8,' . rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="56" height="56"><rect width="56" height="56" fill="#e9ecef"/><text x="28" y="33" font-size="10" text-anchor="middle" fill="#6c757d">Sin foto</text></svg>');
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Inventario | Don Pancho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 panel-titulo mb-0">Control de Inventario</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistro">
            + Nuevo repuesto
        </button>
    </div>

    <div id="alertas"></div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered tabla-inventario">
            <thead class="table-dark">
                <tr>
                    <th>Foto</th><th>Codigo</th><th>Nombre</th><th>Categoria</th>
                    <th>Precio</th><th>Existencias</th><th>Destacado</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
            <?php foreach ($repuestos as $r): ?>
                <tr>
                    <td>
                        <img class="miniatura"
                            src="<?= $r['fotografia'] ? 'uploads/' . e($r['fotografia']) : $sinFoto ?>"
                            alt="<?= e($r['nombre']) ?>"
                            onerror="this.onerror=null;this.src='<?= $sinFoto ?>'">
                    </td>
                    <td><?= e($r['codigo']) ?></td>
                    <td><?= e($r['nombre']) ?></td>
                    <td><?= e($r['categoria']) ?></td>
                    <td>L. <?= number_format($r['precio'], 2) ?></td>
                    <td class="<?= $r['stock'] <= 5 ? 'stock-bajo' : '' ?>"><?= (int)$r['stock'] ?></td>
                    <td><?= $r['destacado'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="text-nowrap">
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal" data-bs-target="#modalEdicion"
                                data-id="<?= (int)$r['id_repuesto'] ?>"
                                data-codigo="<?= e($r['codigo']) ?>"
                                data-nombre="<?= e($r['nombre']) ?>"
                                data-categoria="<?= (int)$r['id_categoria'] ?>"
                                data-especificaciones="<?= e($r['especificaciones']) ?>"
                                data-precio="<?= e($r['precio']) ?>"
                                data-stock="<?= (int)$r['stock'] ?>"
                                data-destacado="<?= (int)$r['destacado'] ?>"
                                data-foto="<?= e($r['fotografia']) ?>">Editar</button>
                        <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-id="<?= (int)$r['id_repuesto'] ?>"
                                data-nombre="<?= e($r['nombre']) ?>">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>