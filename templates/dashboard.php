<?php 
$title = 'Dashboard - San Luis Opina';
ob_start();
?>

<!-- Métricas principales -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label">Total Artículos</div>
                    <div class="metric-value"><?= $stats['articles'] ?></div>
                </div>
                <div class="metric-icon primary">
                    <i class="fas fa-file-text"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label">Publicados</div>
                    <div class="metric-value"><?= $stats['published'] ?></div>
                </div>
                <div class="metric-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label">Categorías</div>
                    <div class="metric-value"><?= $stats['categories'] ?></div>
                </div>
                <div class="metric-icon info">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="metric-label">Usuarios</div>
                    <div class="metric-value"><?= $stats['users'] ?></div>
                </div>
                <div class="metric-icon warning">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Actividad reciente -->
    <div class="col-xl-8 col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Actividad Reciente</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-light active">Todos</button>
                        <button class="btn btn-light">Destacados</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent_articles)): ?>
                    <div class="text-center py-5">
                        <div class="metric-icon primary d-inline-flex mb-3">
                            <i class="fas fa-plus-circle fa-2x"></i>
                        </div>
                        <h6 class="text-dark mb-2">Sin contenido aún</h6>
                        <p class="text-muted mb-4">Crea tu primer artículo para comenzar</p>
                        <a href="/back-SLO/public/articles/create" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus me-2"></i>Crear Artículo
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Título</th>
                                    <th>Autor</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_articles as $article): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="metric-icon primary me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-file-text"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($article['title']) ?></h6>
                                                <small class="text-muted">ID: <?= $article['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-medium"><?= htmlspecialchars($article['author']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusConfig = [
                                            'published' => ['class' => 'success', 'text' => 'PUBLICADO'],
                                            'draft' => ['class' => 'warning', 'text' => 'BORRADOR'],
                                            'archived' => ['class' => 'secondary', 'text' => 'ARCHIVADO']
                                        ];
                                        $config = $statusConfig[$article['status']] ?? ['class' => 'secondary', 'text' => strtoupper($article['status'])];
                                        ?>
                                        <span class="badge bg-<?= $config['class'] ?> bg-opacity-10 text-<?= $config['class'] ?> rounded-pill px-3 py-2">
                                            <?= $config['text'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= date('d/m/Y', strtotime($article['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($article['created_at'])) ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer bg-light border-0">
                        <div class="text-center">
                            <a href="/back-SLO/public/articles" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fas fa-arrow-right me-2"></i>Ver todos los artículos
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar con información -->
    <div class="col-xl-4 col-lg-5">
        <!-- Acciones rápidas -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="/back-SLO/public/articles/create" class="btn btn-primary rounded-pill">
                        <i class="fas fa-plus-circle me-2"></i>
                        Nuevo Artículo
                    </a>
                    <a href="/back-SLO/public/categories" class="btn btn-outline-primary rounded-pill">
                        <i class="fas fa-tags me-2"></i>
                        Gestionar Categorías
                    </a>
                    <a href="/back-SLO/public/articles" class="btn btn-outline-secondary rounded-pill">
                        <i class="fas fa-list me-2"></i>
                        Ver Artículos
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas del usuario -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Estadísticas</h6>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center mb-4">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <div class="metric-value text-dark" style="font-size: 24px;"><?= $stats['articles'] - $stats['published'] ?></div>
                            <div class="metric-label">Borradores</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <div class="metric-value text-success" style="font-size: 24px;"><?= $stats['published'] ?></div>
                            <div class="metric-label">Publicados</div>
                        </div>
                    </div>
                </div>
                
                <div class="progress mb-3" style="height: 8px;">
                    <?php $percentage = $stats['articles'] > 0 ? ($stats['published'] / $stats['articles']) * 100 : 0; ?>
                    <div class="progress-bar bg-success" style="width: <?= $percentage ?>%"></div>
                </div>
                <small class="text-muted d-block text-center"><?= round($percentage) ?>% del contenido publicado</small>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include 'base.php'; 
?>