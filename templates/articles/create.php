<?php 
$title = 'Crear Artículo - San Luis Opina';
ob_start();
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/back-SLO/public/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/back-SLO/public/articles">Artículos</a></li>
                <li class="breadcrumb-item active">Crear Artículo</li>
            </ol>
        </nav>
        
        <h1><i class="fas fa-plus text-primary"></i> Crear Nuevo Artículo</h1>
    </div>
</div>

<form method="POST" action="/back-SLO/public/articles/create" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit"></i> Contenido del Artículo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Extracto</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                  placeholder="Breve descripción del artículo..."></textarea>
                        <small class="form-text text-muted">Máximo 160 caracteres recomendados para SEO</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido *</label>
                        <div id="editor"></div>
                        <textarea name="content" id="content" style="display: none;" required></textarea>
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
                            <option value="draft">Borrador</option>
                            <option value="published">Publicar</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Categoría</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Sin categoría</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Imagen Destacada</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" 
                               accept="image/*" data-preview="image-preview">
                        <img id="image-preview" class="mt-2 img-fluid" style="display: none; max-height: 200px;">
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1">
                        <label class="form-check-label" for="featured">
                            Artículo destacado
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Artículo
                        </button>
                        <a href="/back-SLO/public/articles" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php 
$content = ob_get_clean();
include '../base.php'; 
?>
