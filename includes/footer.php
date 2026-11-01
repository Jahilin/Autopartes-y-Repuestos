<?php $anio = date('Y');?> 
<footer class="footer-dp pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="brand-badge">DP</span>
                    <span class="text-white fw-bold fs-5">Autopartes &amp; Repuestos "Don Pancho"</span>
                </div>
                <p class="small mb-2">
                    Distribuidores de piezas originales y de reemplazo para vehículos japoneses,
                    coreanos y americanos. Expertos en repuestos genuinos y compatibles.
                </p>
                <p class="small mb-0"><i class="bi bi-geo-alt me-1"></i>Sucursal Toncontín,Tegucigalpa</p>
                <p class="small"><i class="bi bi-telephone me-1"></i>+504 2222-0001</p>
                <p class="small mb-0"><i class="bi bi-geo-alt me-1"></i>Sucursal Suyapa,Tegucigalpa</p>
                <p class="small"><i class="bi bi-telephone me-1"></i>+504 2222-0002</p>
            </div>
            
            <div class="col-6 col-lg-3">
                <h6>Marcas aliadas</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li>Toyota</li>
                    <li>Nissan</li>
                    <li>Honda</li>
                    <li>Hyundai</li>
                    <li>Isuzu</li>
                    <li>Ford</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6>Facturación SAR</h6>
                <p class="small mb-2">
                    Emitimos factura digital con RTN y cálculo de ISV del 15%.
                    Descuentos automáticos por volumen y fletes a domicilio.
                </p>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between small">
            <span>&copy; <?= e((string) $anio) ?> Autopartes &amp; Repuestos "Don Pancho". Todos los derechos reservados.</span>
            <span>Precios en Lempiras (L.) &middot; ISV 15% &middot; Factura SAR</span>
        </div>
    </div>
</footer>

<?php require_once __DIR__ . '/scripts.php'; ?>
