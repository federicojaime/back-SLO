<?php
$title = 'Perfil: ' . htmlspecialchars($user['full_name']) . ' - San Luis Opina';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/back-SLO/public/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/back-SLO/public/users">Usuarios</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($user['full_name']) ?></li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-user text-primary"></i> Perfil de Usuario</h1>
                <p class="text-muted mb-0">
                    Vista detallada y estadísticas del usuario
                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                        <span class="badge bg-info ms-2">Tu perfil</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="/back-SLO/public/users/edit/<?= $user['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Usuario
                </a>
                <a href="/back-SLO/public/users" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <!-- Perfil del usuario -->
        <div class="card">
            <div class="card-body text-center">
                <div class="rounded-circle bg-<?= $user['status'] === 'active' ? 'primary' : 'secondary' ?> d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 120px; height: 120px;">
                    <i class="fas fa-user text-white fa-4x"></i>
                </div>

                <h3 class="mb-2"><?= htmlspecialchars($user['full_name']) ?></h3>
                <p class="text-muted mb-3">@<?= htmlspecialchars($user['username']) ?></p>

                <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
                    <?php
                    $roleConfig = [
                        'admin' => ['class' => 'danger', 'icon' => 'fas fa-shield-alt', 'text' => 'ADMIN'],
                        'editor' => ['class' => 'warning', 'icon' => 'fas fa-edit', 'text' => 'EDITOR'],
                        'author' => ['class' => 'info', 'icon' => 'fas fa-pen', 'text' => 'AUTOR']
                    ];
                    $config = $roleConfig[$user['role']] ?? ['class' => 'secondary', 'icon' => 'fas fa-user', 'text' => strtoupper($user['role'])];
                    ?>
                    <span class="badge bg-<?= $config['class'] ?> bg-opacity-10 text-<?= $config['class'] ?> rounded-pill px-3 py-2">
                        <i class="<?= $config['icon'] ?> me-1"></i>
                        <?= $config['text'] ?>
                    </span>

                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?> rounded-pill px-3 py-2">
                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                        <?= $user['status'] === 'active' ? 'ACTIVO' : 'INACTIVO' ?>
                    </span>

                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                            <i class="fas fa-crown me-1"></i>TÚ
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($user['bio']): ?>
                    <div class="border-top pt-3 mb-3">
                        <h6 class="text-muted small text-uppercase mb-2">Biografía</h6>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <a href="/back-SLO/public/users/edit/<?= $user['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar Usuario
                    </a>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-<?= $user['status'] === 'active' ? 'warning' : 'success' ?> flex-fill"
                                onclick="toggleUserStatus(<?= $user['id'] ?>)">
                                <i class="fas fa-toggle-<?= $user['status'] === 'active' ? 'off' : 'on' ?> me-2"></i>
                                <?= $user['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Usuario</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Email:</span>
                            <div class="text-end">
                                <div class="fw-medium"><?= htmlspecialchars($user['email']) ?></div>
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>Verificado
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">ID de usuario:</span>
                            <code>#<?= $user['id'] ?></code>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Fecha de registro:</span>
                            <div class="text-end">
                                <div class="fw-medium"><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($user['created_at'])) ?> hs</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Último login:</span>
                            <div class="text-end">
                                <?php if ($user['last_login']): ?>
                                    <div class="fw-medium"><?= date('d/m/Y', strtotime($user['last_login'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($user['last_login'])) ?> hs</small>
                                <?php else: ?>
                                    <span class="text-muted">Nunca</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Actualizado:</span>
                            <div class="text-end">
                                <div class="fw-medium"><?= date('d/m/Y', strtotime($user['updated_at'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($user['updated_at'])) ?> hs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos y capacidades -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-key me-2"></i>Permisos</h6>
            </div>
            <div class="card-body">
                <?php
                $permissions = [];
                switch ($user['role']) {
                    case 'admin':
                        $permissions = [
                            'Gestionar usuarios' => 'fas fa-users',
                            'Configurar sistema' => 'fas fa-cog',
                            'Gestionar categorías' => 'fas fa-tags',
                            'Crear/editar artículos' => 'fas fa-edit',
                            'Eliminar contenido' => 'fas fa-trash',
                            'Ver estadísticas' => 'fas fa-chart-bar'
                        ];
                        break;
                    case 'editor':
                        $permissions = [
                            'Gestionar categorías' => 'fas fa-tags',
                            'Crear/editar artículos' => 'fas fa-edit',
                            'Publicar contenido' => 'fas fa-globe',
                            'Ver estadísticas' => 'fas fa-chart-bar'
                        ];
                        break;
                    case 'author':
                        $permissions = [
                            'Crear artículos' => 'fas fa-pen',
                            'Editar mis artículos' => 'fas fa-edit',
                            'Ver mis estadísticas' => 'fas fa-chart-line'
                        ];
                        break;
                }
                ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($permissions as $permission => $icon): ?>
                        <div class="list-group-item border-0 px-0 py-2">
                            <i class="<?= $icon ?> text-success me-2"></i>
                            <span class="small"><?= $permission ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Estadísticas del usuario -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-label">Total Artículos</div>
                            <div class="metric-value"><?= $stats['total_articles'] ?? 0 ?></div>
                        </div>
                        <div class="metric-icon primary">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-label">Publicados</div>
                            <div class="metric-value"><?= $stats['published_articles'] ?? 0 ?></div>
                        </div>
                        <div class="metric-icon success">
                            <i class="fas fa-globe"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-label">Borradores</div>
                            <div class="metric-value">
                                <?php
                                $drafts = ($stats['total_articles'] ?? 0) - ($stats['published_articles'] ?? 0);
                                echo $drafts;
                                ?>
                            </div>
                        </div>
                        <div class="metric-icon warning">
                            <i class="fas fa-edit"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="metric-label">Total Vistas</div>
                            <div class="metric-value"><?= number_format($stats['total_views'] ?? 0) ?></div>
                        </div>
                        <div class="metric-icon info">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de productividad (simulado) -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Productividad</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="progress mb-2" style="height: 8px;">
                            <?php
                            $total_articles = $stats['total_articles'] ?? 0;
                            $published_articles = $stats['published_articles'] ?? 0;
                            $percentage = $total_articles > 0 ? ($published_articles / $total_articles) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-success" style="width: <?= $percentage ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <?= round($percentage) ?>% artículos publicados
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div class="progress mb-2" style="height: 8px;">
                            <?php
                            $avg_views = $total_articles > 0 ? ($stats['total_views'] ?? 0) / $total_articles : 0;
                            $views_percentage = min(100, $avg_views / 10); // Escala aproximada
                            ?>
                            <div class="progress-bar bg-info" style="width: <?= $views_percentage ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <?= round($avg_views) ?> vistas promedio por artículo
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artículos del usuario -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-newspaper me-2"></i>
                        Artículos de <?= htmlspecialchars($user['full_name']) ?>
                    </h5>
                    <div class="d-flex gap-2">
                        <?php if (($stats['total_articles'] ?? 0) > 0): ?>
                            <a href="/back-SLO/public/articles?author=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list me-2"></i>Ver todos (<?= $stats['total_articles'] ?>)
                            </a>
                        <?php endif; ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterArticles('all')">Todos</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterArticles('published')">Publicados</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterArticles('draft')">Borradores</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterArticles('archived')">Archivados</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php
                // Obtener artículos recientes del usuario
                global $pdo;
                $stmt = $pdo->prepare("
                    SELECT a.id, a.title, a.status, a.views, a.created_at, a.published_at, c.name as category_name
                    FROM articles a
                    LEFT JOIN categories c ON a.category_id = c.id
                    WHERE a.author_id = ?
                    ORDER BY a.created_at DESC
                    LIMIT 10
                ");
                $stmt->execute([$user['id']]);
                $user_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (empty($user_articles)): ?>
                    <div class="text-center py-4">
                        <div class="metric-icon primary d-inline-flex mb-3">
                            <i class="fas fa-newspaper fa-3x"></i>
                        </div>
                        <h6 class="text-dark mb-2">Sin artículos</h6>
                        <p class="text-muted mb-4">Este usuario no ha creado artículos aún</p>
                        <a href="/back-SLO/public/articles/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Crear primer artículo
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="articlesTable">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th>Vistas</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_articles as $article): ?>
                                    <tr data-status="<?= $article['status'] ?>">
                                        <td>
                                            <div>
                                                <h6 class="mb-1 fw-semibold">
                                                    <a href="/back-SLO/public/articles/view/<?= $article['id'] ?>"
                                                        class="text-decoration-none text-dark">
                                                        <?= htmlspecialchars($article['title']) ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted">ID: <?= $article['id'] ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($article['category_name']): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1">
                                                    <i class="fas fa-tag me-1"></i>
                                                    <?= htmlspecialchars($article['category_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1">
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
                                            <span class="badge bg-<?= $config['class'] ?> bg-opacity-10 text-<?= $config['class'] ?> rounded-pill px-2 py-1">
                                                <i class="<?= $config['icon'] ?> me-1"></i>
                                                <?= $config['text'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-eye text-muted me-2"></i>
                                                <span class="fw-medium"><?= number_format($article['views']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-medium"><?= date('d/m/Y', strtotime($article['created_at'])) ?></div>
                                                <small class="text-muted"><?= date('H:i', strtotime($article['created_at'])) ?></small>
                                                <?php if ($article['published_at']): ?>
                                                    <br><small class="text-success">
                                                        <i class="fas fa-globe me-1"></i>
                                                        Publicado: <?= date('d/m/Y', strtotime($article['published_at'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="/back-SLO/public/articles/view/<?= $article['id'] ?>"
                                                    class="btn btn-sm btn-outline-info" title="Ver artículo">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/back-SLO/public/articles/edit/<?= $article['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary" title="Editar artículo">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
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
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <?php if ($article['status'] === 'draft'): ?>
                                                            <li><a class="dropdown-item" href="#"><i class="fas fa-globe me-2"></i>Publicar</a></li>
                                                        <?php endif; ?>
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

                    <?php if (count($user_articles) >= 10): ?>
                        <div class="card-footer bg-light border-0 text-center">
                            <a href="/back-SLO/public/articles?author=<?= $user['id'] ?>" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Ver todos los artículos (<?= $stats['total_articles'] ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Actividad Reciente</h6>
            </div>
            <div class="card-body">
                <?php
                // Obtener actividad reciente del usuario (múltiples tipos)
                $activities = [];

                // Artículos creados
                $stmt = $pdo->prepare("
                    SELECT 'article_created' as type, title, created_at, id, 'Creó el artículo' as action_text
                    FROM articles 
                    WHERE author_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 5
                ");
                $stmt->execute([$user['id']]);
                $article_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Agregar actividades de artículos
                foreach ($article_activities as $activity) {
                    $activities[] = $activity;
                }

                // Ordenar por fecha
                usort($activities, function ($a, $b) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                });

                $activities = array_slice($activities, 0, 5);
                ?>

                <?php if (empty($activities)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-clock text-muted fa-2x mb-2"></i>
                        <p class="text-muted mb-0">Sin actividad reciente</p>
                    </div>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($activities as $activity): ?>
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3"
                                    style="width: 32px; height: 32px; min-width: 32px;">
                                    <i class="fas fa-plus text-white" style="font-size: 12px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium"><?= $activity['action_text'] ?></div>
                                    <a href="/back-SLO/public/articles/view/<?= $activity['id'] ?>"
                                        class="text-decoration-none">
                                        "<?= htmlspecialchars($activity['title']) ?>"
                                    </a>
                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?>
                                        <span class="ms-2">
                                            (<?php
                                                $diff = time() - strtotime($activity['created_at']);
                                                if ($diff < 3600) {
                                                    echo 'hace ' . floor($diff / 60) . ' min';
                                                } elseif ($diff < 86400) {
                                                    echo 'hace ' . floor($diff / 3600) . ' h';
                                                } else {
                                                    echo 'hace ' . floor($diff / 86400) . ' días';
                                                }
                                                ?>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleUserStatus(userId) {
        if (confirm('¿Confirmas el cambio de estado de este usuario?')) {
            window.location.href = `/back-SLO/public/users/toggle/${userId}`;
        }
    }

    function filterArticles(status) {
        const rows = document.querySelectorAll('#articlesTable tbody tr');

        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Habilitar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Animar las métricas
        const metricValues = document.querySelectorAll('.metric-value');
        metricValues.forEach(metric => {
            const finalValue = parseInt(metric.textContent);
            let currentValue = 0;
            const increment = Math.ceil(finalValue / 20);

            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                metric.textContent = currentValue.toLocaleString();
            }, 50);
        });
    });
</script>

<style>
    .timeline {
        position: relative;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 16px;
        top: 40px;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .metric-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-weight: 600;
        font-size: 11px;
    }

    .progress {
        background: #f1f5f9;
    }

    .table tbody tr:hover {
        background: rgba(99, 102, 241, 0.02);
    }

    .list-group-item {
        border: none !important;
        padding: 8px 0;
    }

    @media (max-width: 768px) {
        .metric-card {
            margin-bottom: 1rem;
        }

        .badge {
            font-size: 10px;
            padding: 4px 8px;
        }
    }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../base.php';
?>