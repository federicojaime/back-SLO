<?php 
$title = 'Página no encontrada - San Luis Opina';
ob_start();
?>

<div class="text-center py-5">
    <div class="metric-icon primary d-inline-flex mb-4" style="width: 80px; height: 80px;">
        <i class="fas fa-exclamation-triangle fa-3x"></i>
    </div>
    
    <h1 class="display-1 fw-bold text-primary mb-3">404</h1>
    <h3 class="mb-3">Página no encontrada</h3>
    <p class="text-muted mb-4 lead">La página que buscas no existe o ha sido movida.</p>
    
    <div class="d-flex justify-content-center gap-3">
        <a href="/back-SLO/public/dashboard" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-home me-2"></i>Ir al Dashboard
        </a>
        <a href="/back-SLO/public/articles" class="btn btn-outline-primary rounded-pill px-4">
            <i class="fas fa-newspaper me-2"></i>Ver Artículos
        </a>
    </div>
</div>

<?php 
$content = ob_get_clean();
include __DIR__ . '/base.php'; 
?>