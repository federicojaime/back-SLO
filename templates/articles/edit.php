<?php
$title = 'Editar: ' . htmlspecialchars($article['title']) . ' - San Luis Opina';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/back-SLO/public/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/back-SLO/public/articles">Artículos</a></li>
                <li class="breadcrumb-item"><a href="/back-SLO/public/articles/view/<?= $article['id'] ?>"><?= htmlspecialchars($article['title']) ?></a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-edit text-primary"></i> Editar Artículo</h1>
                <p class="text-muted mb-0">ID: #<?= $article['id'] ?> | Creado: <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?></p>
            </div>
            <a href="/back-SLO/public/articles/view/<?= $article['id'] ?>" class="btn btn-outline-secondary">
                <i class="fas fa-eye"></i> Ver Artículo
            </a>
        </div>
    </div>
</div>

<form method="POST" action="/back-SLO/public/articles/edit/<?= $article['id'] ?>" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit"></i> Contenido del Artículo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?= htmlspecialchars($article['title']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Extracto</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"
                            placeholder="Breve descripción del artículo..."><?= htmlspecialchars($article['excerpt']) ?></textarea>
                        <small class="form-text text-muted">Máximo 160 caracteres recomendados para SEO</small>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido *</label>
                        <div id="editor"></div>
                        <textarea name="content" id="content" style="display: none;" required><?= htmlspecialchars($article['content']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-cog"></i> Configuración</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Borrador</option>
                            <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Publicado</option>
                            <option value="archived" <?= $article['status'] === 'archived' ? 'selected' : '' ?>>Archivado</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Categoría</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Sin categoría</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $article['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Imagen Destacada</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image"
                            accept="image/*" data-preview="image-preview">
                        <?php if ($article['featured_image']): ?>
                            <div class="mt-2">
                                <img src="<?= htmlspecialchars($article['featured_image']) ?>"
                                    class="img-fluid" style="max-height: 200px;" alt="Imagen actual">
                                <small class="d-block text-muted mt-1">Imagen actual</small>
                            </div>
                        <?php endif; ?>
                        <img id="image-preview" class="mt-2 img-fluid" style="display: none; max-height: 200px;">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                            <?= $article['featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="featured">
                            Artículo destacado
                        </label>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?>
                        </div>
                        <div class="mb-2">
                            <strong>Actualizado:</strong> <?= date('d/m/Y H:i', strtotime($article['updated_at'])) ?>
                        </div>
                        <?php if ($article['published_at']): ?>
                            <div class="mb-2">
                                <strong>Publicado:</strong> <?= date('d/m/Y H:i', strtotime($article['published_at'])) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <strong>Vistas:</strong> <?= number_format($article['views']) ?>
                        </div>
                    </small>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="/back-SLO/public/articles/view/<?= $article['id'] ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="/back-SLO/public/articles/delete/<?= $article['id'] ?>"
                            class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('¿Estás seguro de eliminar este artículo?')">
                            <i class="fas fa-trash"></i> Eliminar Artículo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar contenido en el editor Quill
        if (typeof Quill !== 'undefined') {
            const contentTextarea = document.getElementById('content');
            const editor = document.querySelector('#editor');

            if (editor && contentTextarea) {
                // Crear instancia de Quill
                const quill = new Quill('#editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{
                                'header': [1, 2, 3, 4, 5, 6, false]
                            }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{
                                'color': []
                            }, {
                                'background': []
                            }],
                            [{
                                'align': []
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['blockquote', 'code-block'],
                            ['link', 'image'],
                            ['clean']
                        ]
                    }
                });

                // Cargar contenido inicial
                quill.root.innerHTML = contentTextarea.value;

                // Sincronizar cambios
                quill.on('text-change', function() {
                    contentTextarea.value = quill.root.innerHTML;
                });
            }
        }
    });
</script>

<?php
$content = ob_get_clean();
include '../base.php';
?>