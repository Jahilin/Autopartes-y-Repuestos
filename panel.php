<?php
require_once __DIR__ . '/includes/auth_check.php';

//Elimnacion del codigo temporal. Union de las clases repuesto.php + categoria.php
require_once __DIR__ . '/clases/Repuesto.php';
require_once __DIR__ . '/clases/Categoria.php';

$repuestos = (new Repuesto())->listarTodos();
$categorias = (new Categoria())->listar();

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

<?php
// opciones de categorias, reutilizadas en con los dos modales
$opcionesCategorias = '';
foreach ($categorias as $c) {$opcionesCategorias .= '<option value="' . (int)$c['id_categoria'] . '">' . e($c['nombre']) . '</option>';}
?>

<!-- MODAL REGISTRO  -->
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content formulario-ajax" enctype="multipart/form-data" data-modal="modalRegistro">
      <input type="hidden" name="accion" value="crear">
      <div class="modal-header">
        <h2 class="modal-title h5">Registrar repuesto</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="alerta-modal"></div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Código</label>
            <input type="text" name="codigo" class="form-control" maxlength="20" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" maxlength="120" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoria</label>
            <select name="id_categoria" class="form-select" required>
                <option value="">Seleccione la categoria...</option><?= $opcionesCategorias ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Precio (L.)</label>
            <input type="number" name="precio" class="form-control" min="0" step="0.01" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Existencias</label>
            <input type="number" name="stock" class="form-control" min="0" step="1" required>
          </div>
          <div class="col-12">
            <label class="form-label">Especificaciones</label>
            <textarea name="especificaciones" class="form-control" rows="3" required></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Destacado</label>
            <select name="destacado" class="form-select">
                <option value="0">No</option><option value="1">Si</option>
            </select>
          </div>
            <div class="col-md-8">
                <label class="form-label">Fotografia (JPG, PNG o WEBP, max. 4 MB)</label>
                <input type="file" name="fotografia" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL DE EDICION -->
<div class="modal fade" id="modalEdicion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content formulario-ajax" enctype="multipart/form-data" data-modal="modalEdicion">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id_repuesto" id="edit_id">
      <div class="modal-header">
        <h2 class="modal-title h5">Editar repuesto</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="alerta-modal"></div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Codigo</label>
            <input type="text" name="codigo" id="edit_codigo" class="form-control" maxlength="20" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" id="edit_nombre" class="form-control" maxlength="120" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoria</label>
            <select name="id_categoria" id="edit_categoria" class="form-select" required>
                <?= $opcionesCategorias ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Precio (L.)</label>
            <input type="number" name="precio" id="edit_precio" class="form-control" min="0" step="0.01" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Existencias</label>
            <input type="number" name="stock" id="edit_stock" class="form-control" min="0" step="1" required>
          </div>
          <div class="col-12">
            <label class="form-label">Especificaciones</label>
            <textarea name="especificaciones" id="edit_especificaciones" class="form-control" rows="3" required></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Destacado</label>
            <select name="destacado" id="edit_destacado" class="form-select">
                <option value="0">No</option><option value="1">Si</option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nueva fotografia (opcional)</label>
            <input type="file" name="fotografia" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text">Si no eliges una, se conservara la actual.</div>
          </div>
          <div class="col-12">
            <span class="form-text d-block mb-1">Foto actual:</span>
            <img id="edit_vista_previa" class="vista-previa-foto" src="<?= $sinFoto ?>" alt="Foto actual">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL DE ELIMINACION-->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content formulario-ajax" data-modal="modalEliminar">
      <input type="hidden" name="accion" value="eliminar">
      <input type="hidden" name="id_repuesto" id="eliminar_id">
      <div class="modal-header">
        <h2 class="modal-title h5">Confirmar eliminacion</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="alerta-modal"></div>
        <p class="mb-0">¿Seguro que deseas eliminar <strong id="eliminar_nombre"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>



<!-- JAVASCRIPT-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const SIN_FOTO = <?= json_encode($sinFoto) ?>;

// Carga los datos del repuesto elegido en el modal de edición
document.getElementById('modalEdicion').addEventListener('show.bs.modal', e => {
    const d = e.relatedTarget.dataset;
    edit_id.value = d.id;
    edit_codigo.value = d.codigo;
    edit_nombre.value = d.nombre;
    edit_categoria.value = d.categoria;
    edit_especificaciones.value = d.especificaciones;
    edit_precio.value = d.precio;
    edit_stock.value = d.stock;
    edit_destacado.value = d.destacado;
    edit_vista_previa.src = d.foto ? 'uploads/' + d.foto : SIN_FOTO;
    edit_vista_previa.onerror = () => { edit_vista_previa.src = SIN_FOTO; };
});

// Muestra el nombre del repuesto en el modal de eliminación
document.getElementById('modalEliminar').addEventListener('show.bs.modal', e => {
    eliminar_id.value = e.relatedTarget.dataset.id;
    eliminar_nombre.textContent = e.relatedTarget.dataset.nombre;
});

function mostrarAlerta(contenedor, tipo, mensaje) {
    contenedor.innerHTML = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        ${mensaje}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
}

// Recarga solo la tabla, sin salir de la pantalla
async function refrescarTabla() {
    const html = await (await fetch(location.href)).text();
    const doc = new DOMParser().parseFromString(html, 'text/html');
    document.getElementById('cuerpoTabla').innerHTML = doc.getElementById('cuerpoTabla').innerHTML;
}

// Envio de los 3 formularios sin recargar la pagina
document.querySelectorAll('.formulario-ajax').forEach(form => {
    form.addEventListener('submit', async ev => {
        ev.preventDefault();
        const alertaModal = form.querySelector('.alerta-modal');
        try {
            const resp = await fetch('panel_acciones.php', { method: 'POST', body: new FormData(form) });
            const json = await resp.json();
            if (json.ok) {
                bootstrap.Modal.getInstance(document.getElementById(form.dataset.modal)).hide();
                form.reset();
                alertaModal.innerHTML = '';
                mostrarAlerta(document.getElementById('alertas'), 'success', json.mensaje);
                await refrescarTabla();
            } else {
                mostrarAlerta(alertaModal, 'danger', json.mensaje);
            }
        } catch (err) {
            mostrarAlerta(alertaModal, 'danger', 'No se pudo comunicar con el servidor.');
        }
    });
});
</script>
</body>