<?php
require_once __DIR__ . '/clases/repuesto.php';


$modeloRepuesto = new Repuesto();
$repuestos      = $modeloRepuesto->listarTodos();

$vehiculos = [
    'sedan'  => ['nombre' => 'Sedán',       'galones' => 1.0, 'detalle' => 'Corolla, Civic, Sentra, Accent'],
    'suv'    => ['nombre' => 'SUV',         'galones' => 1.5, 'detalle' => 'RAV4, CR-V, X-Trail, Tucson'],
    'pickup' => ['nombre' => 'Pick-Up 4x4', 'galones' => 2.0, 'detalle' => 'Hilux, Frontier, D-Max, Ranger'],
];


$codigosAdicionales = ['ELE-003', 'FRE-003', 'FLU-005', 'FLU-002', 'FLU-003'];
$filtrosMarcados = ['FIL-001', 'FIL-002'];

$aceites     = [];  
$filtros     = [];   
$adicionales = [];
foreach ($repuestos as $r) {
    if (preg_match('/^Aceite\s+([^\s\d]+)\s+(\d+W-\d+)\s*(.*)$/u', $r['nombre'], $m)) {
        $tipo = trim(preg_replace('/\s*\(.*\)\s*$/u', '', $m[3]));
        $aceites[] = [
            'id'       => (int) $r['id_repuesto'],
            'marca'    => $m[1],
            'visc'     => $m[2],
            'etiqueta' => trim($m[2] . ' ' . $tipo),
            'precio'   => (float) $r['precio'],
            'stock'    => (int) $r['stock'],
        ];
    } elseif (strpos($r['codigo'], 'FIL-') === 0) {
        $filtros[] = $r;
    } elseif (in_array($r['codigo'], $codigosAdicionales, true)) {
        $adicionales[] = $r;
    }
}


$marcas = array_values(array_unique(array_column($aceites, 'marca')));
sort($marcas);
$viscosidades = [];
foreach ($aceites as $a) {
    if (!isset($viscosidades[$a['visc']])) {
        $viscosidades[$a['visc']] = $a['etiqueta'];
    }
}
uksort($viscosidades, fn($x, $y) => (int) $x <=> (int) $y);

$datosJs = ['vehiculos' => [], 'aceites' => $aceites, 'filtros' => [], 'adicionales' => []];
foreach ($vehiculos as $clave => $v) {
    $datosJs['vehiculos'][] = ['clave' => $clave, 'nombre' => $v['nombre'], 'galones' => $v['galones']];
}
foreach ($filtros as $f) {
    $datosJs['filtros'][] = ['id' => (int) $f['id_repuesto'], 'nombre' => $f['nombre'], 'precio' => (float) $f['precio']];
}
foreach ($adicionales as $a) {
    $datosJs['adicionales'][] = ['id' => (int) $a['id_repuesto'], 'nombre' => $a['nombre'], 'precio' => (float) $a['precio']];
}

$titulo = 'Arma tu Kit Tune-Up';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<style>
    .btn-check:checked + .opcion {
        background-color: var(--carbon);
        border-color: var(--rojo);
        color: white;
    }
    .btn-check:disabled + .opcion { opacity: .5; }
</style>

<header class="pt-5">
    <div class="container">
        <h1 class="titulo-seccion mb-2">Arma tu Kit de Mantenimiento Preventivo / Tune-Up</h1>
        <p class="mb-0">Elija su vehículo, el aceite, los filtros y los extras. El total se recalcula al instante.</p>
    </div>
</header>

<main class="container my-4">
    <form id="formKit" class="row g-4" onsubmit="return false;">
        <div class="col-lg-8">
            <h2 class="titulo-seccion">1. Tipo de vehículo</h2>
            <div class="row g-3 mb-5">
                <?php $primero = true; foreach ($vehiculos as $clave => $v) { ?>
                    <div class="col-md-4">
                        <input type="radio" class="btn-check" name="vehiculo" id="veh-<?php echo $clave; ?>"
                               value="<?php echo $clave; ?>" <?php echo $primero ? 'checked' : ''; ?>>
                        <label class="btn btn-outline-dark opcion w-100 h-100 text-start" for="veh-<?php echo $clave; ?>">
                            <strong class="d-block"><?php echo htmlspecialchars($v['nombre']); ?></strong>
                            <small class="d-block"><?php echo htmlspecialchars($v['detalle']); ?></small>
                            <small class="d-block mt-1"><?php echo rtrim(rtrim(number_format($v['galones'], 1), '0'), '.'); ?> galón(es) de aceite</small>
                        </label>
                    </div>
                <?php $primero = false; } ?>
            </div>

            <h2 class="titulo-seccion">2. Aceite de motor</h2>
            <?php if (empty($aceites)) { ?>
                <div class="alert alert-warning mb-5">
                    No hay aceites con marca en la base de datos. Ejecute <code>tuneup_datos.sql</code>.
                </div>
            <?php } else { ?>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="marcaAceite" class="form-label fw-semibold">Marca</label>
                        <select class="form-select" id="marcaAceite" name="marca">
                            <?php foreach ($marcas as $marca) { ?>
                                <option value="<?php echo htmlspecialchars($marca); ?>"><?php echo htmlspecialchars($marca); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <span class="form-label fw-semibold d-block">Viscosidad</span>
                        <div class="d-flex flex-wrap gap-2">
                            <?php $primero = true; foreach ($viscosidades as $visc => $etiqueta) { ?>
                                <input type="radio" class="btn-check" name="viscosidad" id="vis-<?php echo htmlspecialchars($visc); ?>"
                                       value="<?php echo htmlspecialchars($visc); ?>" <?php echo $primero ? 'checked' : ''; ?>>
                                <label class="btn btn-outline-dark opcion btn-sm" for="vis-<?php echo htmlspecialchars($visc); ?>">
                                    <?php echo htmlspecialchars($etiqueta); ?>
                                </label>
                            <?php $primero = false; } ?>
                        </div>
                    </div>
                </div>
                <p class="small text-muted mb-5" id="notaAceite"></p>
            <?php } ?>

            <h2 class="titulo-seccion">3. Filtros</h2>
            <?php if (empty($filtros)) { ?>
                <div class="alert alert-warning mb-5">No hay filtros en la base de datos.</div>
            <?php } else { ?>
                <div class="row g-2 mb-5">
                    <?php foreach ($filtros as $f) { ?>
                        <?php $sinStock = (int) $f['stock'] === 0; ?>
                        <div class="col-md-6">
                            <input type="checkbox" class="btn-check" name="filtros[]"
                                   id="fil-<?php echo (int) $f['id_repuesto']; ?>"
                                   value="<?php echo (int) $f['id_repuesto']; ?>"
                                   <?php echo $sinStock ? 'disabled' : ''; ?>
                                   <?php echo (!$sinStock && in_array($f['codigo'], $filtrosMarcados, true)) ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-dark opcion w-100 h-100 text-start" for="fil-<?php echo (int) $f['id_repuesto']; ?>">
                                <span class="d-block"><?php echo htmlspecialchars($f['nombre']); ?></span>
                                <small class="d-block">
                                    + L. <?php echo number_format((float) $f['precio'], 2); ?>
                                    <?php echo $sinStock ? ' · Agotado' : ''; ?>
                                </small>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <h2 class="titulo-seccion">4. Repuestos adicionales</h2>
            <?php if (empty($adicionales)) { ?>
                <div class="alert alert-warning">No hay repuestos adicionales en la base de datos.</div>
            <?php } else { ?>
                <div class="row g-2">
                    <?php foreach ($adicionales as $a) { ?>
                        <?php $sinStock = (int) $a['stock'] === 0; ?>
                        <div class="col-md-6">
                            <input type="checkbox" class="btn-check" name="adicionales[]"
                                   id="adi-<?php echo (int) $a['id_repuesto']; ?>"
                                   value="<?php echo (int) $a['id_repuesto']; ?>"
                                   <?php echo $sinStock ? 'disabled' : ''; ?>>
                            <label class="btn btn-outline-dark opcion w-100 h-100 text-start" for="adi-<?php echo (int) $a['id_repuesto']; ?>">
                                <span class="d-block"><?php echo htmlspecialchars($a['nombre']); ?></span>
                                <small class="d-block">
                                    + L. <?php echo number_format((float) $a['precio'], 2); ?>
                                    <?php echo $sinStock ? ' · Agotado' : ''; ?>
                                </small>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div class="col-lg-4">
            <aside class="card sticky-top" style="top: 8rem;">
                <div class="card-header barra-oscura text-white">
                    <strong>Su kit Tune-Up</strong>
                </div>
                <div class="card-body">
                    <p class="mb-2" id="vehiculoElegido"></p>
                    <ul class="list-unstyled mb-3" id="lineasKit"></ul>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span>Total del kit</span>
                        <strong class="precio fs-4" id="totalKit">L. 0.00</strong>
                    </div>
                    <p class="small text-muted mt-2">Precios del inventario, sin ISV. El ISV 15% se aplica al facturar.</p>
                    <a href="#" class="btn btn-principal w-100 mb-2 disabled" id="facturarKit" aria-disabled="true">Facturar este kit</a>
                    <button type="button" class="btn btn-outline-dark w-100" id="reiniciarKit">Reiniciar selección</button>
                </div>
            </aside>
        </div>
    </form>
</main>

<script>
(function () {
    'use strict';

    const D = <?php echo json_encode($datosJs, JSON_UNESCAPED_UNICODE); ?>;
    const form = document.getElementById('formKit');
    const formato = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const lempiras = (v) => 'L. ' + formato.format(v);
    const redondear = (v) => Math.round(v * 100) / 100;

    function linea(texto, valor) {
        const li = document.createElement('li');
        li.className = 'd-flex justify-content-between gap-2 mb-1';
        const nombre = document.createElement('span');
        nombre.textContent = texto;
        const monto = document.createElement('span');
        monto.className = 'text-nowrap';
        monto.textContent = lempiras(valor);
        li.append(nombre, monto);
        return li;
    }

    function recalcular() {
        const claveVeh = form.querySelector('input[name="vehiculo"]:checked').value;
        const vehiculo = D.vehiculos.find((v) => v.clave === claveVeh);

        const lineas = document.getElementById('lineasKit');
        lineas.replaceChildren();
        let total = 0;
        const items = [];
        const nota = document.getElementById('notaAceite');
        const selMarca = form.querySelector('#marcaAceite');
        const selVisc = form.querySelector('input[name="viscosidad"]:checked');
        if (selMarca && selVisc) {
            const aceite = D.aceites.find((a) => a.marca === selMarca.value && a.visc === selVisc.value);
            if (!aceite) {
                nota.textContent = 'Esa combinación de marca y viscosidad no está disponible.';
            } else {
                const galones = vehiculo.galones;
                const costo = redondear(aceite.precio * galones);
                lineas.append(linea('Aceite ' + aceite.marca + ' ' + aceite.etiqueta + ' × ' + galones + ' gal', costo));
                total += costo;
                items.push({ id: aceite.id, cantidad: galones });
                nota.textContent = 'Precio por galón: ' + lempiras(aceite.precio) + ' · Su ' + vehiculo.nombre +
                    ' necesita ' + galones + ' galón(es): ' + lempiras(costo) +
                    (aceite.stock < galones ? ' · Atención: stock insuficiente (' + aceite.stock + ').' : '');
            }
        }

        form.querySelectorAll('input[name="filtros[]"]:checked').forEach((chk) => {
            const filtro = D.filtros.find((f) => String(f.id) === chk.value);
            lineas.append(linea(filtro.nombre, filtro.precio));
            total += filtro.precio;
            items.push({ id: filtro.id, cantidad: 1 });
        });

        form.querySelectorAll('input[name="adicionales[]"]:checked').forEach((chk) => {
            const extra = D.adicionales.find((a) => String(a.id) === chk.value);
            lineas.append(linea(extra.nombre, extra.precio));
            total += extra.precio;
            items.push({ id: extra.id, cantidad: 1 });
        });

        document.getElementById('vehiculoElegido').textContent = 'Vehículo: ' + vehiculo.nombre;
        document.getElementById('totalKit').textContent = lempiras(redondear(total));
        const btnFacturar = document.getElementById('facturarKit');
        if (items.length > 0) {
            btnFacturar.href = 'facturacion.php?kit=' + items.map((i) => i.id + ':' + i.cantidad).join(',');
            btnFacturar.classList.remove('disabled');
            btnFacturar.removeAttribute('aria-disabled');
        } else {
            btnFacturar.href = '#';
            btnFacturar.classList.add('disabled');
            btnFacturar.setAttribute('aria-disabled', 'true');
        }
    }

    form.addEventListener('change', recalcular);
    document.getElementById('reiniciarKit').addEventListener('click', () => {
        form.reset();
        recalcular();
    });

    recalcular();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>