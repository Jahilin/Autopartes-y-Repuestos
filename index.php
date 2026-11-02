<?php 
$titulo = 'Inicio ';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/clases/Usuario.php';
require_once __DIR__ . '/clases/repuesto.php';
require_once __DIR__ . '/clases/categoria.php';

$repuestoModel = new Repuesto();
$destacados = $repuestoModel->listarDestacados();
$categoriaModel = new Categoria();
$categorias = $categoriaModel->listar();
?>

<section class="hero">
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="hero-badge"><i class="bi bi-patch-check-fill"></i> Distribuidores autorizados</span>
                <h1 class="mt-3 mb-3">Expertos en Repuestos Genuinos y Compatibles</h1>
                <p class="lead mb-4">
                    Piezas originales y de reemplazo para vehículos japoneses, coreanos y americanos.
                    Mantenimiento preventivo, tune-up y facturación SAR en un solo lugar.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= e(base_url('catalogo.php')) ?>" class="btn btn-dp btn-lg px-4">
                        <i class="bi bi-grid me-2"></i>Ver Catálogo
                    </a>
                    <a href="<?= e(base_url('tuneup.php')) ?>" class="btn btn-outline-dp btn-lg px-4">
                        <i class="bi bi-tools me-2"></i>Arma tu Kit
                    </a>
                </div>
                <div class="row mt-4 g-3">
                    <div class="col-4 hero-stat"><strong>6</strong><span class="small">Marcas aliadas</span></div>
                    <div class="col-4 hero-stat"><strong>4</strong><span class="small">Sucursales</span></div>
                    <div class="col-4 hero-stat"><strong>15%</strong><span class="small">ISV facturado</span></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-white bg-opacity-10 border border-warning border-opacity-25 rounded-4 p-4">
                    <h5 class="text-warning fw-bold mb-3"><i class="bi bi-lightning-charge-fill me-1"></i>Tune-Up Express</h5>
                    <p class="small text-white-50 mb-3">
                        Selecciona el tipo de vehículo, marca, viscosidad de aceite, filtros y repuestos
                        adicionales. Calculamos tu kit al instante.
                    </p>
                    <ul class="list-unstyled small mb-0 d-grid gap-2">
                        <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Descuento automático desde 3 repuestos</li>
                        <li><i class="bi bi-check-circle-fill text-warning me-2"></i>10% para compras mayoristas (10+)</li>
                        <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Flete en moto o camioncito</li>
                        <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Factura digital correlativa SAR</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2">
        <h2 class="section-title mb-0">Repuestos insignia</h2>
        <a href="<?= e(base_url('catalogo.php')) ?>" class="btn btn-sm btn-outline-secondary">Ver todo <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4 mt-1">
        <?php if (!$destacados): ?>
            <div class="col-12"><div class="empty-state">No hay repuestos destacados registrados.</div></div>
        <?php endif; ?>
        <?php foreach ($destacados as $repuesto): ?>
            <div class="col-sm-6 col-lg-4 col-xl">
                <article class="card-repuesto d-flex flex-column">
                    <div class="card-img-wrap">
                        <img src="./uploads/<?= $repuesto['fotografia'] ?>" alt="<?= e($repuesto['nombre']) ?>">
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <span class="codigo mb-1"><?= e($repuesto['codigo']) ?></span>
                        <h6 class="fw-bold mb-1"><?= e($repuesto['nombre']) ?></h6>
                        <span class="badge badge-categoria align-self-start mb-2"><?= e($repuesto['categoria']) ?></span>
                        <p class="small text-muted mb-3"><?= e($repuesto['marca_vehiculo']) ?></p>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="precio"><?= e(dinero((float) $repuesto['precio'])) ?></span>
                            <span class="small text-muted">Stock: <?= e((string) $repuesto['stock']) ?></span>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container pb-5">
    <div class="row g-4">
        <?php foreach ($categorias as $categoria): ?>
            <div class="col-sm-6 col-lg-3">
                <a class="text-decoration-none" href="<?= e(base_url('catalogo.php?categoria=' . (int) $categoria['id'])) ?>">
                    <div class="kit-card h-100 d-flex align-items-center gap-3">
                        <span class="feature-icon"><i class="bi bi-gear-wide-connected"></i></span>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><?= e($categoria['nombre']) ?></h6>
                            <small class="text-muted"><?= e((string) $categoria['total_repuestos']) ?> repuestos</small>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<section class="container pb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="kit-card h-100">
                <span class="feature-icon mb-3"><i class="bi bi-shield-check"></i></span>
                <h5 class="fw-bold">Repuestos genuinos</h5>
                <p class="text-muted small mb-0">Piezas originales y de reemplazo con garantía para motores japoneses, coreanos y americanos.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kit-card h-100">
                <span class="feature-icon mb-3"><i class="bi bi-truck"></i></span>
                <h5 class="fw-bold">Entrega flexible</h5>
                <p class="text-muted small mb-0">Retiro en mostrador, moto express o flete en camioncito a cualquier punto de Tegucigalpa.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kit-card h-100">
                <span class="feature-icon mb-3"><i class="bi bi-receipt"></i></span>
                <h5 class="fw-bold">Factura SAR</h5>
                <p class="text-muted small mb-0">Factura digital correlativa con RTN, cálculo de ISV del 15% y descuentos por volumen.</p>
            </div>
        </div>
    </div>
</section>

<?php 
require_once __DIR__ . '/includes/footer.php' ?>