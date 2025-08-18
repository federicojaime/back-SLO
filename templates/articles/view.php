<?php
$title = htmlspecialchars($article['title']) . ' - San Luis Opina';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/back-SLO/public/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/back-SLO/public/articles">Artículos</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($article['title']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="mb-2"><?= htmlspecialchars($article['title']) ?></h2>
                        <div class="d-flex flex-wrap gap-3 align-items-center text-muted">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user me-2"></i>
                                <span><?= htmlspecialchars($article['author']) ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar me-2"></i>
                                <span><?= date('d/m/Y H:i', strtotime($article['created_at'])) ?></span>
                            </div>
                            <?php if ($article['category_name']): ?>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag me-2"></i>
                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1"><?= htmlspecialchars($article['category_name']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-eye me-2"></i>
                                <span><?= number_format($article['views']) ?> vistas</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/back-SLO/public/articles/edit/<?= $article['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/back-SLO/public/articles/edit/<?= $article['id'] ?>"><i class="fas fa-edit me-2"></i>Editar</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="/back-SLO/public/articles/delete/<?= $article['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar este artículo?')"><i class="fas fa-trash me-2"></i>Eliminar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if ($article['excerpt']): ?>
                    <div class="alert alert-light border-start border-primary border-4 mb-4">
                        <h6 class="mb-2">Extracto:</h6>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($article['excerpt']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="article-content">
                    <?= $article['content'] ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Estado del artículo -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Artículo</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Estado:</span>
                            <?php
                            $statusConfig = [
                                'published' => ['class' => 'success', 'icon' => 'fas fa-globe', 'text' => 'PUBLICADO'],
                                'draft' => ['class' => 'warning', 'icon' => 'fas fa-edit', 'text' => 'BORRADOR'],
                                'archived' => ['class' => 'secondary', 'icon' => 'fas fa-archive', 'text' => 'ARCHIVADO']
                            ];
                            $config = $statusConfig[$article['status']] ?? ['class' => 'secondary', 'icon' => 'fas fa-question', 'text' => strtoupper($article['status'])];
                            ?>
                            <span class="badge bg-<?= $config['class'] ?> bg-opacity-10 text-<?= $config['class'] ?> px-3 py-2">
                                <i class="<?= $config['icon'] ?> me-1"></i>
                                <?= $config['text'] ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($article['featured']): ?>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Destacado:</span>
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                    <i class="fas fa-star me-1"></i>SÍ
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">ID:</span>
                            <code>#<?= $article['id'] ?></code>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Slug:</span>
                            <code><?= htmlspecialchars($article['slug']) ?></code>
                        </div>
                    </div>

                    <?php if ($article['published_at']): ?>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Publicado:</span>
                                <span><?= date('d/m/Y H:i', strtotime($article['published_at'])) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Última actualización:</span>
                            <span><?= date('d/m/Y H:i', strtotime($article['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Autor -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Autor</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3"
                        style="width: 50px; height: 50px;">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h6 class="mb-1"><?= htmlspecialchars($article['author']) ?></h6>
                        <p class="text-muted mb-0 small"><?= htmlspecialchars($article['author_email']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h6>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="metric-icon primary d-inline-flex mb-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="metric-value" style="font-size: 20px;"><?= number_format($article['views']) ?></div>
                        <div class="metric-label">Vistas</div>
                    </div>
                    <div class="col-6">
                        <div class="metric-icon success d-inline-flex mb-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-file-text"></i>
                        </div>
                        <div class="metric-value" style="font-size: 20px;"><?= str_word_count(strip_tags($article['content'])) ?></div>
                        <div class="metric-label">Palabras</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .article-content {
        font-size: 16px;
        line-height: 1.8;
        color: #2d3748;
    }

    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5,
    .article-content h6 {
        color: #1a202c;
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .article-content p {
        margin-bottom: 1.5rem;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1.5rem 0;
    }

    .article-content blockquote {
        border-left: 4px solid var(--primary);
        padding-left: 1.5rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #4a5568;
        background: #f7fafc;
        padding: 1rem 1.5rem;
        border-radius: 0 8px 8px 0;
    }

    .article-content ul,
    .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }

    .article-content li {
        margin-bottom: 0.5rem;
    }

    .article-content code {
        background: #f1f5f9;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-size: 0.9em;
        color: #e53e3e;
    }

    .article-content pre {
        background: #1a202c;
        color: #e2e8f0;
        padding: 1.5rem;
        border-radius: 8px;
        overflow-x: auto;
        margin: 1.5rem 0;
    }

    .article-content a {
        color: var(--primary);
        text-decoration: none;
    }

    .article-content a:hover {
        text-decoration: underline;
    }
</style>

<?php
$content = ob_get_clean();
include '../base.php';
?>