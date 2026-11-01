<?php
require_once 'clases/repuesto.php';
require_once 'clases/factura.php';

$repuestos = [];
foreach ((new Repuesto())->listarTodos() as $r) {
    $repuestos[(int) $r['id_repuesto']] = $r;
}

$lempiras = fn($valor) => 'L. ' . number_format($valor, 2);

$errores  = [];
$factura  = null;
$cliente  = ['nombre' => '', 'rtn' => '', 'telefono' => ''];
$flete    = '';
$cantidad = [];

// Viene del botón "Facturar este repuesto" del catálogo
if (isset($_GET['agregar']) && isset($repuestos[(int) $_GET['agregar']])) {
    $cantidad[(int) $_GET['agregar']] = 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente['nombre']   = trim($_POST['nombre'] ?? '');
    $cliente['rtn']      = trim($_POST['rtn'] ?? '');
    $cliente['telefono'] = trim($_POST['telefono'] ?? '');
    $flete               = $_POST['flete'] ?? '';
    $cantidad            = is_array($_POST['cantidad'] ?? null) ? $_POST['cantidad'] : [];

    if ($cliente['nombre'] === '') {
        $errores[] = 'Escribe el nombre del cliente.';
    }
    if (!preg_match('/^\d{14}$/', $cliente['rtn'])) {
        $errores[] = 'El RTN debe tener 14 dígitos numéricos.';
    }
    if (!preg_match('/^\d{8}$/', $cliente['telefono'])) {
        $errores[] = 'El teléfono debe tener 8 dígitos.';
    }
    if (!isset(Factura::FLETES[$flete])) {
        $errores[] = 'Elige una modalidad de flete.';
    }

    $lineas = [];
    foreach ($cantidad as $id => $cant) {
        if (isset($repuestos[$id]) && ctype_digit((string) $cant) && (int) $cant > 0 && (int) $cant <= 999) {
            $lineas[] = [
                'id'       => (int) $id,
                'nombre'   => $repuestos[$id]['nombre'],
                'cantidad' => (int) $cant,
                'precio'   => (float) $repuestos[$id]['precio'],
            ];
        }
    }
    if (!$lineas) {
        $errores[] = 'Agrega al menos un repuesto con cantidad mayor que cero.';
    }

    if (!$errores) {
        try {
            $factura = (new Factura())->guardar($cliente, $lineas, $flete);
        } catch (Exception $e) {
            $errores[] = 'No se pudo guardar la factura. Intenta de nuevo.';
        }
    }
}

$titulo = 'Facturación';
$css    = 'facturacion';
include __DIR__ . '/includes/head.php';
?>

    <main class="container py-4">
        <h1 class="mb-4">Facturación</h1>

        <?php foreach ($errores as $error) { ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <?php if ($factura) { ?>
            <section class="factura card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap mb-3">
                        <h2 class="titulo-seccion mb-0">Factura <?php echo $factura['numero']; ?></h2>
                        <span class="etiqueta"><?php echo $factura['fecha']; ?></span>
                    </div>
                    <p class="mb-3">
                        <strong>Cliente:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?><br>
                        <strong>RTN:</strong> <?php echo $cliente['rtn']; ?> &nbsp;
                        <strong>Teléfono:</strong> <?php echo $cliente['telefono']; ?>
                    </p>

                    <table class="table table-sm">
                        <thead>
                        <tr><th>Repuesto</th><th class="text-end">Cant.</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($lineas as $l) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($l['nombre']); ?></td>
                                <td class="text-end"><?php echo $l['cantidad']; ?></td>
                                <td class="text-end"><?php echo $lempiras($l['precio']); ?></td>
                                <td class="text-end"><?php echo $lempiras($l['precio'] * $l['cantidad']); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                    <table class="table table-sm w-auto ms-auto">
                        <tr><td>Subtotal</td><td class="text-end"><?php echo $lempiras($factura['subtotal']); ?></td></tr>
                        <tr><td>Descuentos y rebajas otorgados (<?php echo (int) $factura['porcentaje']; ?>%)</td><td class="text-end">- <?php echo $lempiras($factura['descuento']); ?></td></tr>
                        <tr><td>Importe gravado 15%</td><td class="text-end"><?php echo $lempiras($factura['gravado']); ?></td></tr>
                        <tr><td>ISV 15%</td><td class="text-end"><?php echo $lempiras($factura['isv']); ?></td></tr>
                        <tr><td>Flete (<?php echo $factura['modalidad']; ?>)</td><td class="text-end"><?php echo $lempiras($factura['flete']); ?></td></tr>
                        <tr><th>Total a pagar</th><th class="text-end precio"><?php echo $lempiras($factura['total']); ?></th></tr>
                    </table>
                </div>
            </section>
            <a href="facturacion.php" class="btn btn-principal mt-3 d-print-none">Nueva factura</a>

        <?php } else { ?>
            <form method="post" action="facturacion.php">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label" for="nombre">Nombre del cliente</label>
                        <input class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="rtn">RTN (14 dígitos)</label>
                        <input class="form-control" id="rtn" name="rtn" maxlength="14" value="<?php echo htmlspecialchars($cliente['rtn']); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="telefono">Teléfono (8 dígitos)</label>
                        <input class="form-control" id="telefono" name="telefono" maxlength="8" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
                    </div>
                </div>

                <h2 class="titulo-seccion">Repuestos</h2>
                <table class="table table-sm align-middle mb-4">
                    <thead>
                    <tr><th>Repuesto</th><th class="text-end">Precio</th><th style="width:110px">Cantidad</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($repuestos as $id => $r) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                            <td class="text-end"><?php echo $lempiras($r['precio']); ?></td>
                            <td>
                                <input type="number" min="0" max="999" class="form-control form-control-sm"
                                       name="cantidad[<?php echo $id; ?>]" value="<?php echo (int) ($cantidad[$id] ?? 0); ?>">
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <h2 class="titulo-seccion">Flete</h2>
                <div class="mb-4">
                    <?php foreach (Factura::FLETES as $clave => $f) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flete" id="flete-<?php echo $clave; ?>"
                                   value="<?php echo $clave; ?>" <?php echo $flete === $clave ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="flete-<?php echo $clave; ?>">
                                <?php echo $f[0] . ' (' . $lempiras($f[1]) . ')'; ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <button type="submit" class="btn btn-principal">Generar factura</button>
            </form>
        <?php } ?>
    </main>

<?php include __DIR__ . '/includes/scripts.php'; ?>