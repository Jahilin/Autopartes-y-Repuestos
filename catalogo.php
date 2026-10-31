<?php
require_once __DIR__ . '/clases/categoria.php';
require_once __DIR__ . '/clases/repuesto.php';

$modeloRepuesto = new Repuesto();
$categorias     = (new Categoria())->listar();

$catalogo = [];
foreach ($categorias as $categoria) {
    $catalogo[] = [
        'categoria' => $categoria,
        'repuestos' => $modeloRepuesto->listarPorCategoria($categoria['id_categoria']),
    ];
}

$titulo = 'Catálogo';
include __DIR__ . '/includes/head.php';
?>

<div class="container py-4">
    <h1 class="mb-2">Catálogo de repuestos</h1>
    <p class="mb-0">Precios en lempiras. Descuento automático desde 3 piezas.</p>
</div>


<nav class="py-2" aria-label="Categorías del catálogo">
    <div class="container d-flex flex-wrap gap-2">
        <?php foreach ($catalogo as $bloque) { ?>
            <a class="btn btn-sm btn-acento" href="#cat-<?php echo (int) $bloque['categoria']['id_categoria']; ?>">
                <?php echo htmlspecialchars($bloque['categoria']['nombre']); ?>
                (<?php echo count($bloque['repuestos']); ?>)
            </a>
        <?php } ?>
    </div>
</nav>

<main class="container my-4">
    <?php foreach ($catalogo as $bloque) { ?>
        <section class="mb-5" id="cat-<?php echo (int) $bloque['categoria']['id_categoria']; ?>">
            <h2 class="titulo-seccion"><?php echo htmlspecialchars($bloque['categoria']['nombre']); ?></h2>

            <?php if (empty($bloque['repuestos'])) { ?>
                <p class="text-muted">No hay repuestos en esta categoría por el momento.</p>
            <?php } else { ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    <?php foreach ($bloque['repuestos'] as $r) { ?>
                        <?php $stock = (int) $r['stock']; ?>
                        <div class="col">
                            <article class="card tarjeta-repuesto h-100 shadow-sm">
                                <?php if (!empty($r['fotografia'])) { ?>
                                    <img src="uploads/<?php echo htmlspecialchars($r['fotografia']); ?>"
                                         alt="<?php echo htmlspecialchars($r['nombre']); ?>"
                                         class="card-img-top" style="height:180px; object-fit:cover;"
                                         loading="lazy" onerror="this.style.display='none'">
                                <?php } else { ?>
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center"
                                         style="height:180px;">Sin imagen</div>
                                <?php } ?>

                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="etiqueta"><?php echo htmlspecialchars($r['codigo']); ?></span>
                                        <?php if ((int) $r['destacado'] === 1) { ?>
                                            <span class="badge bg-dark ms-1">Destacado</span>
                                        <?php } ?>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($r['nombre']); ?></h5>
                                    <p class="card-text small text-muted flex-grow-1">
                                        <?php echo htmlspecialchars($r['especificaciones']); ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="precio fs-5">L. <?php echo number_format((float) $r['precio'], 2); ?></span>
                                        <?php if ($stock === 0) { ?>
                                            <span class="badge bg-danger">Agotado</span>
                                        <?php } elseif ($stock <= 5) { ?>
                                            <span class="badge bg-warning text-dark">Últimas <?php echo $stock; ?></span>
                                        <?php } else { ?>
                                            <span class="badge bg-success">Disponible</span>
                                        <?php } ?>
                                    </div>

                                    <?php if ($stock > 0) { ?>
                                        <a href="facturacion.php?agregar=<?php echo (int) $r['id_repuesto']; ?>"
                                           class="btn btn-principal btn-sm w-100 mt-3">Facturar este repuesto</a>
                                    <?php } ?>
                                </div>
                            </article>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>
    <?php } ?>
</main>

<?php include __DIR__ . '/includes/scripts.php'; ?>