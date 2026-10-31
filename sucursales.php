<?php
require_once 'clases/sucursal.php';
require_once 'clases/historial.php';

// Pedimos los datos a las clases
$sucursal = new Sucursal();
$sucursales = $sucursal->listar();

$historial = new Historial();
$facturas = $historial->listar();

$titulo = 'Sucursales';
include 'includes/head.php';
include 'includes/navbar.php';
?>

<div class="container my-4">
    <h1 class="titulo-seccion">Nuestras sucursales</h1>

    <div class="row">
        <?php foreach ($sucursales as $s) { ?>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="etiqueta"><?php echo $s['ciudad']; ?></span>
                        <h5 class="card-title mt-2"><?php echo $s['nombre']; ?></h5>
                        <p class="card-text">
                            <?php echo $s['direccion']; ?><br>
                            Tel: <?php echo $s['telefono']; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <h2 class="titulo-seccion mt-5">Historial de compras</h2>

    <?php foreach ($facturas as $f) { ?>
        <div class="card mb-4">
            <div class="card-header barra-oscura text-white">
                <strong>Factura <?php echo $f['numero_factura']; ?></strong>
                - <?php echo $f['fecha']; ?>
                - <?php echo htmlspecialchars($f['cliente_nombre']); ?>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($f['detalle'] as $d) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d['descripcion']); ?></td>
                                <td><?php echo $d['cantidad']; ?></td>
                                <td>L. <?php echo number_format($d['precio_unitario'], 2); ?></td>
                                <td>L. <?php echo number_format($d['subtotal'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <p class="mb-1">Subtotal: L. <?php echo number_format($f['subtotal'], 2); ?></p>
                <p class="mb-1">Descuento (<?php echo $f['porcentaje_descuento']; ?>%): L. <?php echo number_format($f['monto_descuento'], 2); ?></p>
                <p class="mb-1">Subtotal gravable: L. <?php echo number_format($f['subtotal'] - $f['monto_descuento'], 2); ?></p>
                <p class="mb-1">ISV 15%: L. <?php echo number_format($f['isv'], 2); ?></p>
                <p class="mb-1">Flete (<?php echo $f['modalidad_flete']; ?>): L. <?php echo number_format($f['costo_flete'], 2); ?></p>
                <p class="precio mb-0">Total: L. <?php echo number_format($f['total'], 2); ?></p>
            </div>
        </div>
    <?php } ?>
</div>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>