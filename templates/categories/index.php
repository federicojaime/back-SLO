<?php
$title = 'Categorías - San Luis Opina';
ob_start();
?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-light mb-1">Gestión de Categorías</h2>
                <p class="text-muted small mb-0">Organiza el contenido del portal por temas</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fas fa-plus me-2"></i>Nueva Categoría
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-tags me-2"></i>Lista de Categorías
            </h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>Todos los estados</option>
                    <option>Activas</option>
                    <option>Inactivas</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <div class="metric-icon primary d-inline-flex mb-3">
                    <i class="fas fa-tags fa-3x"></i>
                </div>
                <h5 class="text-dark mb-2">No hay categorías</h5>
                <p class="text-muted mb-4">Crea tu primera categoría para organizar el contenido</p>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="fas fa-plus me-2"></i>Crear Primera Categoría
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Categoría</th>
                            <th>Slug</th>
                            <th>Descripción</th>
                            <th>Artículos</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded" style="width: 24px; height: 24px; background: <?= $category['color'] ?? '#6c757d' ?>; margin-right: 12px;"></div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($category['name']) ?></h6>
                                            <small class="text-muted">ID: <?= $category['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($category['slug']) ?></code>
                                </td>
                                <td>
                                    <span class="text-muted"><?= htmlspecialchars($category['description'] ?? 'Sin descripción') ?></span>
                                </td>
                                <td>
                                    <?php
                                    // Contar artículos por categoría (esto debería venir del controlador)
                                    global $pdo;
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
                                    $stmt->execute([$category['id']]);
                                    $article_count = $stmt->fetchColumn();
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-newspaper text-muted me-2"></i>
                                        <span class="fw-medium"><?= $article_count ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            <?= $category['status'] === 'active' ? 'checked' : '' ?>
                                            onchange="toggleCategoryStatus(<?= $category['id'] ?>)">
                                        <label class="form-check-label">
                                            <span class="badge bg-<?= $category['status'] === 'active' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $category['status'] === 'active' ? 'success' : 'secondary' ?> rounded-pill px-3 py-2">
                                                <?= $category['status'] === 'active' ? 'ACTIVA' : 'INACTIVA' ?>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium"><?= date('d/m/Y', strtotime($category['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($category['created_at'])) ?> hs</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="editCategory(<?= $category['id'] ?>)"
                                            title="Editar" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="editCategory(<?= $category['id'] ?>)">
                                                        <i class="fas fa-edit me-2"></i>Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="toggleCategoryStatus(<?= $category['id'] ?>)">
                                                        <i class="fas fa-toggle-<?= $category['status'] === 'active' ? 'off' : 'on' ?> me-2"></i>
                                                        <?= $category['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="/back-SLO/public/categories/delete/<?= $category['id'] ?>"
                                                        onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
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
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas de categorías -->
<div class="row g-4 mt-4">
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon primary me-3">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <div class="metric-label">Total Categorías</div>
                    <div class="metric-value" style="font-size: 24px;"><?= count($categories) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon success me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="metric-label">Activas</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($categories, function ($c) {
                            return $c['status'] === 'active';
                        })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon warning me-3">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div>
                    <div class="metric-label">Inactivas</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($categories, function ($c) {
                            return $c['status'] === 'inactive';
                        })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon info me-3">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div>
                    <div class="metric-label">Con Artículos</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?php
                        $categories_with_articles = 0;
                        foreach ($categories as $cat) {
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
                            $stmt->execute([$cat['id']]);
                            if ($stmt->fetchColumn() > 0) $categories_with_articles++;
                        }
                        echo $categories_with_articles;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Categoría -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nueva Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/back-SLO/public/categories">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Descripción de la categoría..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="#6c757d">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Crear Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Categoría -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_color" class="form-label">Color</label>
                        <input type="color" class="form-control form-control-color" id="edit_color" name="color">
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Estado</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="active">Activa</option>
                            <option value="inactive">Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función para editar categoría
    function editCategory(categoryId) {
        // Buscar la categoría en los datos
        const categories = <?= json_encode($categories) ?>;
        const category = categories.find(c => c.id == categoryId);

        if (category) {
            document.getElementById('edit_name').value = category.name;
            document.getElementById('edit_description').value = category.description || '';
            document.getElementById('edit_color').value = category.color || '#6c757d';
            document.getElementById('edit_status').value = category.status;
            document.getElementById('editCategoryForm').action = `/back-SLO/public/categories/edit/${categoryId}`;

            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }
    }

    // Función para cambiar estado
    function toggleCategoryStatus(categoryId) {
        if (confirm('¿Confirmas el cambio de estado de esta categoría?')) {
            window.location.href = `/back-SLO/public/categories/toggle/${categoryId}`;
        }
    }

    // Auto-generar slug
    document.getElementById('name').addEventListener('input', function() {
        // Opcional: mostrar preview del slug
    });

    // Habilitar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../base.php';
?>