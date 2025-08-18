<?php 
$title = 'Gestión de Artículos - San Luis Opina';
ob_start();
?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-light mb-1">Gestión de Artículos</h2>
                <p class="text-muted small mb-0">Administra todo el contenido del portal</p>
            </div>
            <a href="/back-SLO/public/articles/create" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>
                Nuevo Artículo
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-newspaper me-2"></i>
                Lista de Artículos
            </h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>Todos los estados</option>
                    <option>Publicados</option>
                    <option>Borradores</option>
                    <option>Archivados</option>
                </select>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" class="form-control" placeholder="Buscar artículos...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($articles)): ?>
            <div class="text-center py-5">
                <div class="metric-icon primary d-inline-flex mb-3">
                    <i class="fas fa-newspaper fa-3x"></i>
                </div>
                <h5 class="text-dark mb-2">No hay artículos</h5>
                <p class="text-muted mb-4">Comienza creando tu primer artículo para el portal</p>
                <a href="/back-SLO/public/articles/create" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>Crear Primer Artículo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Artículo</th>
                            <th>Autor</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Visitas</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon primary me-3" style="width: 45px; height: 45px;">
                                        <i class="fas fa-file-text"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold">
                                            <a href="/back-SLO/public/articles/view/<?= $article['id'] ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($article['title']) ?>
                                            </a>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted">ID: <?= $article['id'] ?></small>
                                            <?php if ($article['featured'] ?? false): ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1" style="font-size: 10px;">
                                                    <i class="fas fa-star me-1"></i>DESTACADO
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?= htmlspecialchars($article['author']) ?></div>
                                        <small class="text-muted">
                                            <?= $_SESSION['role'] === 'admin' ? '@' . $article['author'] : '' ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($article['category_name'] ?? null): ?>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                        <i class="fas fa-tag me-1"></i>
                                        <?= htmlspecialchars($article['category_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2">
                                        <i class="fas fa-minus me-1"></i>Sin categoría
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $statusConfig = [
                                    'published' => ['class' => 'success', 'icon' => 'fas fa-globe', 'text' => 'PUBLICADO'],
                                    'draft' => ['class' => 'warning', 'icon' => 'fas fa-edit', 'text' => 'BORRADOR'],
                                    'archived' => ['class' => 'secondary', 'icon' => 'fas fa-archive', 'text' => 'ARCHIVADO']
                                ];
                                $config = $statusConfig[$article['status']] ?? ['class' => 'secondary', 'icon' => 'fas fa-question', 'text' => strtoupper($article['status'])];
                                ?>
                                <span class="badge bg-<?= $config['class'] ?> bg-opacity-10 text-<?= $config['class'] ?> rounded-pill px-3 py-2">
                                    <i class="<?= $config['icon'] ?> me-1"></i>
                                    <?= $config['text'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-eye text-muted me-2"></i>
                                    <span class="fw-medium"><?= number_format($article['views'] ?? 0) ?></span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-medium"><?= date('d/m/Y', strtotime($article['created_at'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($article['created_at'])) ?> hs</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/back-SLO/public/articles/view/<?= $article['id'] ?>" 
                                       class="btn btn-sm btn-outline-info" title="Ver" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/back-SLO/public/articles/edit/<?= $article['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="/back-SLO/public/articles/view/<?= $article['id'] ?>">
                                                    <i class="fas fa-eye me-2"></i>Ver artículo
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="/back-SLO/public/articles/edit/<?= $article['id'] ?>">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <?php if ($article['status'] === 'draft'): ?>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-globe me-2"></i>Publicar</a></li>
                                            <?php elseif ($article['status'] === 'published'): ?>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Mover a borrador</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-star me-2"></i>Destacar</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" 
                                                   href="/back-SLO/public/articles/delete/<?= $article['id'] ?>"
                                                   onclick="return confirm('¿Estás seguro de eliminar este artículo?')">
                                                    <i class="fas fa-trash me-2"></i>Eliminar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if (($total_pages ?? 1) > 1): ?>
            <div class="card-footer bg-light border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando <?= count($articles) ?> de <?= $total ?? count($articles) ?> artículos
                    </small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if (($page ?? 1) > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= ($page ?? 1) - 1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php 
                            $currentPage = $page ?? 1;
                            $totalPages = $total_pages ?? 1;
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row g-4 mt-4">
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon primary me-3">
                    <i class="fas fa-file-text"></i>
                </div>
                <div>
                    <div class="metric-label">Total Artículos</div>
                    <div class="metric-value" style="font-size: 24px;"><?= $total ?? count($articles ?? []) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon success me-3">
                    <i class="fas fa-globe"></i>
                </div>
                <div>
                    <div class="metric-label">Publicados</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($articles ?? [], function($a) { return $a['status'] === 'published'; })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon warning me-3">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <div class="metric-label">Borradores</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($articles ?? [], function($a) { return $a['status'] === 'draft'; })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon info me-3">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <div class="metric-label">Total Visitas</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= number_format(array_sum(array_column($articles ?? [], 'views'))) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Habilitar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php 
$content = ob_get_clean();
include '../base.php'; 
?>