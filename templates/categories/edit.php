<?php
$title = 'Editar: ' . htmlspecialchars($category['name']) . ' - San Luis Opina';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/back-SLO/public/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/back-SLO/public/categories">Categorías</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($category['name']) ?></li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-edit text-primary"></i> Editar Categoría</h1>
                <p class="text-muted mb-0">ID: #<?= $category['id'] ?> | Creado: <?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></p>
            </div>
            <a href="/back-SLO/public/categories" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Categorías
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Información de la Categoría</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/back-SLO/public/categories/edit/<?= $category['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= htmlspecialchars($category['name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control bg-light" id="slug"
                                    value="<?= htmlspecialchars($category['slug']) ?>" readonly>
                                <small class="form-text text-muted">Se genera automáticamente desde el nombre</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="Descripción de la categoría..."><?= htmlspecialchars($category['description']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Color de la Categoría</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="color" class="form-control form-control-color"
                                        id="color" name="color" value="<?= $category['color'] ?? '#6c757d' ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded me-2" style="width: 24px; height: 24px; background: <?= $category['color'] ?? '#6c757d' ?>;" id="color-preview"></div>
                                        <span class="text-muted">Vista previa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?= $category['status'] === 'active' ? 'selected' : '' ?>>Activa</option>
                                    <option value="inactive" <?= $category['status'] === 'inactive' ? 'selected' : '' ?>>Inactiva</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="/back-SLO/public/categories" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Información de la categoría -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">ID:</span>
                            <code>#<?= $category['id'] ?></code>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Slug actual:</span>
                            <code><?= htmlspecialchars($category['slug']) ?></code>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Estado:</span>
                            <span class="badge bg-<?= $category['status'] === 'active' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $category['status'] === 'active' ? 'success' : 'secondary' ?> px-3 py-2">
                                <?= $category['status'] === 'active' ? 'ACTIVA' : 'INACTIVA' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Creado:</span>
                            <span><?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Actualizado:</span>
                            <span><?= date('d/m/Y H:i', strtotime($category['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de la categoría -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h6>
            </div>
            <div class="card-body">
                <?php
                global $pdo;
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
                $stmt->execute([$category['id']]);
                $total_articles = $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ? AND status = 'published'");
                $stmt->execute([$category['id']]);
                $published_articles = $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT SUM(views) FROM articles WHERE category_id = ?");
                $stmt->execute([$category['id']]);
                $total_views = $stmt->fetchColumn() ?? 0;
                ?>

                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="metric-icon primary d-inline-flex mb-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="metric-value" style="font-size: 20px;"><?= $total_articles ?></div>
                        <div class="metric-label">Artículos</div>
                    </div>
                    <div class="col-6">
                        <div class="metric-icon success d-inline-flex mb-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="metric-value" style="font-size: 20px;"><?= $published_articles ?></div>
                        <div class="metric-label">Publicados</div>
                    </div>
                    <div class="col-12">
                        <div class="metric-icon info d-inline-flex mb-2" style="width: 40px; height: 40px;">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="metric-value" style="font-size: 20px;"><?= number_format($total_views) ?></div>
                        <div class="metric-label">Vistas Totales</div>
                    </div>
                </div>

                <?php if ($total_articles > 0): ?>
                    <div class="mt-3">
                        <a href="/back-SLO/public/articles?category=<?= $category['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-list me-2"></i>Ver Artículos de esta Categoría
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones peligrosas -->
        <div class="card">
            <div class="card-header bg-danger bg-opacity-10">
                <h6 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Zona de Peligro</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Las siguientes acciones son irreversibles.</p>

                <?php if ($total_articles > 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>No se puede eliminar:</strong> Esta categoría tiene <?= $total_articles ?> artículo(s) asociado(s).
                    </div>
                <?php else: ?>
                    <a href="/back-SLO/public/categories/delete/<?= $category['id'] ?>"
                        class="btn btn-outline-danger btn-sm w-100"
                        onclick="return confirm('¿Estás seguro de eliminar esta categoría? Esta acción no se puede deshacer.')">
                        <i class="fas fa-trash me-2"></i>Eliminar Categoría
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar preview del color
        const colorInput = document.getElementById('color');
        const colorPreview = document.getElementById('color-preview');

        colorInput.addEventListener('input', function() {
            colorPreview.style.background = this.value;
        });

        // Auto-generar slug cuando cambia el nombre
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        nameInput.addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        });
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../base.php';
?>