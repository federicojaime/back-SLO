<?php 
$title = 'Categorías - San Luis Opina';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-tags text-primary"></i> Gestión de Categorías</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Lista de Categorías</h5>
            </div>
            <div class="card-body">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay categorías creadas</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Slug</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $category['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($category['name']) ?></strong></td>
                                    <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                                    <td><?= htmlspecialchars($category['description']) ?></td>
                                    <td>
                                        <span class="badge <?= $category['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $category['status'] === 'active' ? 'Activa' : 'Inactiva' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger btn-delete" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Nueva Categoría</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/back-SLO/public/categories">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Crear Categoría
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include '../base.php'; 
?>
