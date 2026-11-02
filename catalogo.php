<?php
require_once __DIR__ . '/clases/categoria.php';
require_once __DIR__ . '/clases/repuesto.php';

$modeloRepuesto = new Repuesto();
$categorias     = (new Categoria())->listar();

$catalogo = [];
foreach ($categorias as $categoria) {
    $tarjetas = [];
    $grupos   = [];

    foreach ($modeloRepuesto->listarPorCategoria($categoria['id_categoria']) as $r) {
        // Aceites ("Aceite [Marca] 5W-30 tipo (galón)"): se agrupan por viscosidad y tipo.
        if (preg_match('/^Aceite\s+(?:([^\s\d]+)\s+)?(\d+W-\d+)\s*(.*)$/u', $r['nombre'], $m)) {
            $tipo  = trim(preg_replace('/\s*\(.*\)\s*$/u', '', $m[3]));
            $clave = trim($m[2] . ' ' . $tipo);

            if (!isset($grupos[$clave])) {
                $grupos[$clave] = [
                    'titulo'           => 'Aceite ' . $clave . ' (galón)',
                    'especificaciones' => $r['especificaciones'],
                    'destacado'        => 0,
                    'opciones'         => [],
                ];
            }
            if ((int) $r['destacado'] === 1) {
                $grupos[$clave]['destacado'] = 1;
            }
            $grupos[$clave]['opciones'][] = [
                'id'     => (int) $r['id_repuesto'],
                'marca'  => $m[1] !== '' ? $m[1] : 'Genérico',
                'precio' => (float) $r['precio'],
                'stock'  => (int) $r['stock'],
                'foto'   => (string) ($r['fotografia'] ?? ''),
            ];
        } else {
            $tarjetas[] = ['grupo' => false, 'r' => $r];
        }
    }

    // Los grupos de aceite van al final, ordenados por viscosidad (5W-30, 10W-40, 15W-40).
    uksort($grupos, fn($a, $b) => (int) $a <=> (int) $b);
    foreach ($grupos as $g) {
        $tarjetas[] = ['grupo' => true, 'g' => $g];
    }

    $catalogo[] = [
        'categoria' => $categoria,
        'tarjetas'  => $tarjetas,
    ];
}

$lempiras = fn($valor) => 'L. ' . number_format($valor, 2);

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
                (<?php echo count($bloque['tarjetas']); ?>)
            </a>
        <?php } ?>
    </div>
</nav>

<main class="container my-4">
    <?php foreach ($catalogo as $bloque) { ?>
        <section class="mb-5" id="cat-<?php echo (int) $bloque['categoria']['id_categoria']; ?>">
            <h2 class="titulo-seccion"><?php echo htmlspecialchars($bloque['categoria']['nombre']); ?></h2>

            <?php if (empty($bloque['tarjetas'])) { ?>
                <p class="text-muted">No hay repuestos en esta categoría por el momento.</p>
            <?php } else { ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    <?php foreach ($bloque['tarjetas'] as $t) { ?>
                        <?php
                        if ($t['grupo']) {
                            $g = $t['g'];
                            $disponibles = array_values(array_filter($g['opciones'], fn($o) => $o['stock'] > 0));
                            usort($disponibles, fn($a, $b) => $a['precio'] <=> $b['precio']);
                            $base    = $disponibles ?: $g['opciones'];
                            $precios = array_column($base, 'precio');
                            $minimo  = min($precios);
                            $maximo  = max($precios);

                            $nombre       = $g['titulo'];
                            $especific    = $g['especificaciones'];
                            $foto         = $base[0]['foto']; 
                            $destacado    = $g['destacado'] === 1;
                            $etiqueta     = count($g['opciones']) . ' opciones';
                            $precioTexto  = ($minimo === $maximo)
                                ? $lempiras($minimo)
                                : $lempiras($minimo) . ' – ' . number_format($maximo, 2);
                            $stock        = array_sum(array_column($disponibles, 'stock'));
                        } else {
                            $r = $t['r'];
                            $nombre       = $r['nombre'];
                            $especific    = $r['especificaciones'];
                            $foto         = $r['fotografia'];
                            $destacado    = (int) $r['destacado'] === 1;
                            $etiqueta     = $r['codigo'];
                            $precioTexto  = $lempiras((float) $r['precio']);
                            $stock        = (int) $r['stock'];
                        }
                        ?>
                        <div class="col">
                            <article class="card tarjeta-repuesto h-100 shadow-sm">
                                <div class="foto-tarjeta">
                                    <?php if (!empty($foto)) { ?>
                                        <img src="uploads/<?php echo htmlspecialchars($foto); ?>"
                                             alt="<?php echo htmlspecialchars($nombre); ?>"
                                             class="card-img-top" style="height:180px; object-fit:cover;"
                                             loading="lazy"
                                             onerror="this.outerHTML='<div class=\'bg-light text-muted d-flex align-items-center justify-content-center\' style=\'height:180px;\'>Sin imagen</div>'">
                                    <?php } else { ?>
                                        <div class="bg-light text-muted d-flex align-items-center justify-content-center"
                                             style="height:180px;">Sin imagen</div>
                                    <?php } ?>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="etiqueta"><?php echo htmlspecialchars($etiqueta); ?></span>
                                        <?php if ($destacado) { ?>
                                            <span class="badge bg-dark ms-1">Destacado</span>
                                        <?php } ?>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($nombre); ?></h5>
                                    <p class="card-text small text-muted flex-grow-1">
                                        <?php echo htmlspecialchars($especific); ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center mt-2 gap-2">
                                        <span class="precio <?php echo $t['grupo'] ? 'fs-6' : 'fs-5'; ?>"><?php echo $precioTexto; ?></span>
                                        <?php if ($stock === 0) { ?>
                                            <span class="badge bg-danger">Agotado</span>
                                        <?php } elseif ($stock <= 5) { ?>
                                            <span class="badge bg-warning text-dark">Últimas <?php echo $stock; ?></span>
                                        <?php } else { ?>
                                            <span class="badge bg-success">Disponible</span>
                                        <?php } ?>
                                    </div>

                                    <?php if ($stock > 0) { ?>
                                        <?php if ($t['grupo']) { ?>
                                            <div class="mt-3">
                                                <select class="form-select form-select-sm selector-marca" aria-label="Marca">
                                                    <?php foreach ($disponibles as $o) { ?>
                                                        <option value="<?php echo $o['id']; ?>"
                                                                data-foto="<?php echo htmlspecialchars($o['foto']); ?>">
                                                            <?php echo htmlspecialchars($o['marca']) . ' — ' . $lempiras($o['precio']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <a href="facturacion.php?agregar=<?php echo $disponibles[0]['id']; ?>"
                                                   class="btn btn-principal btn-sm w-100 mt-2 boton-facturar">Facturar este repuesto</a>
                                            </div>
                                        <?php } else { ?>
                                            <a href="facturacion.php?agregar=<?php echo (int) $r['id_repuesto']; ?>"
                                               class="btn btn-principal btn-sm w-100 mt-3">Facturar este repuesto</a>
                                        <?php } ?>
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

<script>
function crearSinImagen() {
    var div = document.createElement('div');
    div.className = 'bg-light text-muted d-flex align-items-center justify-content-center';
    div.style.height = '180px';
    div.textContent = 'Sin imagen';
    return div;
}

function mostrarFoto(contenedor, archivo, texto) {
    contenedor.replaceChildren();
    if (!archivo) {
        contenedor.append(crearSinImagen());
        return;
    }
    var img = document.createElement('img');
    img.src = 'uploads/' + encodeURIComponent(archivo);
    img.alt = texto;
    img.className = 'card-img-top';
    img.style.height = '180px';
    img.style.objectFit = 'cover';
    img.onerror = function () { contenedor.replaceChildren(crearSinImagen()); };
    contenedor.append(img);
}

document.querySelectorAll('.selector-marca').forEach(function (selector) {
    var tarjeta = selector.closest('article');
    var boton = selector.parentElement.querySelector('.boton-facturar');
    var contenedorFoto = tarjeta.querySelector('.foto-tarjeta');
    var titulo = tarjeta.querySelector('.card-title').textContent.trim();

    function actualizar() {
        var opcion = selector.options[selector.selectedIndex];
        boton.href = 'facturacion.php?agregar=' + encodeURIComponent(selector.value);
        mostrarFoto(contenedorFoto, opcion.dataset.foto, titulo);
    }
    selector.addEventListener('change', actualizar);
    actualizar();
});
</script>

<?php include __DIR__ . '/includes/scripts.php'; ?>