<?php 
$title = 'Modales Promocionales - San Luis Opina';
ob_start();
?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-light mb-1">Modales Promocionales</h2>
                <p class="text-muted small mb-0">Gestiona los modales promocionales del sitio web</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createModalModal">
                <i class="fas fa-plus me-2"></i>Nuevo Modal
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-window-restore me-2"></i>Lista de Modales
            </h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>Todos los estados</option>
                    <option>Activos</option>
                    <option>Inactivos</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($modals)): ?>
            <div class="text-center py-5">
                <div class="metric-icon primary d-inline-flex mb-3">
                    <i class="fas fa-window-restore fa-3x"></i>
                </div>
                <h5 class="text-dark mb-2">No hay modales promocionales</h5>
                <p class="text-muted mb-4">Crea tu primer modal promocional</p>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createModalModal">
                    <i class="fas fa-plus me-2"></i>Crear Primer Modal
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Modal</th>
                            <th>Configuración</th>
                            <th>Frecuencia</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modals as $modal): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <?php if ($modal['image_url']): ?>
                                        <div class="me-3" style="width: 50px; height: 40px;">
                                            <img src="<?= htmlspecialchars($modal['image_url']) ?>" 
                                                 alt="Preview" class="img-fluid rounded"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    <?php else: ?>
                                        <div class="metric-icon primary me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-window-restore"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($modal['title']) ?></h6>
                                        <small class="text-muted">ID: <?= $modal['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1" style="font-size: 10px;">
                                        <?= strtoupper($modal['position']) ?>
                                    </span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1" style="font-size: 10px;">
                                        <?= strtoupper($modal['size']) ?>
                                    </span>
                                    <?php if ($modal['auto_close_seconds']): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1" style="font-size: 10px;">
                                            AUTO: <?= $modal['auto_close_seconds'] ?>s
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $frequencyLabels = [
                                    'always' => 'Siempre',
                                    'once_per_session' => 'Una vez por sesión',
                                    'once_per_day' => 'Una vez por día',
                                    'once_per_week' => 'Una vez por semana'
                                ];
                                ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                    <?= $frequencyLabels[$modal['display_frequency']] ?? $modal['display_frequency'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           <?= $modal['status'] === 'active' ? 'checked' : '' ?>
                                           onchange="toggleModalStatus(<?= $modal['id'] ?>)">
                                    <label class="form-check-label">
                                        <span class="badge bg-<?= $modal['status'] === 'active' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $modal['status'] === 'active' ? 'success' : 'secondary' ?> rounded-pill px-3 py-2">
                                            <?= $modal['status'] === 'active' ? 'ACTIVO' : 'INACTIVO' ?>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-medium"><?= date('d/m/Y', strtotime($modal['created_at'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($modal['created_at'])) ?> hs</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="previewModal(<?= $modal['id'] ?>)" 
                                            title="Vista previa" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editModal(<?= $modal['id'] ?>)" 
                                            title="Editar" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="previewModal(<?= $modal['id'] ?>)">
                                                    <i class="fas fa-eye me-2"></i>Vista previa
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="editModal(<?= $modal['id'] ?>)">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="toggleModalStatus(<?= $modal['id'] ?>)">
                                                    <i class="fas fa-toggle-<?= $modal['status'] === 'active' ? 'off' : 'on' ?> me-2"></i>
                                                    <?= $modal['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" 
                                                   href="/back-SLO/public/promotional-modals/delete/<?= $modal['id'] ?>"
                                                   onclick="return confirm('¿Estás seguro de eliminar este modal?')">
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

<!-- Estadísticas de modales -->
<div class="row g-4 mt-4">
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon primary me-3">
                    <i class="fas fa-window-restore"></i>
                </div>
                <div>
                    <div class="metric-label">Total Modales</div>
                    <div class="metric-value" style="font-size: 24px;"><?= count($modals) ?></div>
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
                    <div class="metric-label">Activos</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($modals, function ($m) {
                            return $m['status'] === 'active';
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
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="metric-label">Con Auto-cierre</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($modals, function ($m) {
                            return !empty($m['auto_close_seconds']);
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
                    <i class="fas fa-image"></i>
                </div>
                <div>
                    <div class="metric-label">Con Imagen</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($modals, function ($m) {
                            return !empty($m['image_url']);
                        })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Modal Promocional -->
<div class="modal fade" id="createModalModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Modal Promocional
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/back-SLO/public/promotional-modals" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Título del Modal *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Contenido *</label>
                                <textarea class="form-control" id="content" name="content" rows="5" required
                                          placeholder="Contenido HTML del modal..."></textarea>
                                <small class="form-text text-muted">Puedes usar HTML básico</small>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Imagen (opcional)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">JPG, PNG, GIF. Tamaño recomendado: 400x300px</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="button_text" class="form-label">Texto del Botón</label>
                                        <input type="text" class="form-control" id="button_text" name="button_text" 
                                               value="Cerrar" placeholder="Cerrar">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="button_url" class="form-label">URL del Botón (opcional)</label>
                                        <input type="url" class="form-control" id="button_url" name="button_url"
                                               placeholder="https://ejemplo.com">
                                        <small class="form-text text-muted">Si está vacío, solo cierra el modal</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Configuración</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="display_frequency" class="form-label">Frecuencia</label>
                                        <select class="form-select" id="display_frequency" name="display_frequency">
                                            <option value="once_per_session">Una vez por sesión</option>
                                            <option value="once_per_day">Una vez por día</option>
                                            <option value="once_per_week">Una vez por semana</option>
                                            <option value="always">Siempre</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="position" class="form-label">Posición</label>
                                        <select class="form-select" id="position" name="position">
                                            <option value="center">Centro</option>
                                            <option value="top">Arriba</option>
                                            <option value="bottom">Abajo</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="size" class="form-label">Tamaño</label>
                                        <select class="form-select" id="size" name="size">
                                            <option value="small">Pequeño</option>
                                            <option value="medium">Mediano</option>
                                            <option value="large">Grande</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="auto_close_seconds" class="form-label">Auto-cerrar (segundos)</label>
                                        <input type="number" class="form-control" id="auto_close_seconds" 
                                               name="auto_close_seconds" min="1" max="60"
                                               placeholder="Opcional">
                                        <small class="form-text text-muted">Dejar vacío para desactivar</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Estado</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="active">Activo</option>
                                            <option value="inactive">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Crear Modal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista Previa del Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="preview-content" class="border rounded p-4 text-center">
                    <!-- Contenido del preview se carga aquí -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar Preview</button>
            </div>
        </div>
    </div>
</div>

<script>
// Datos de modales para JavaScript
const modalsData = <?= json_encode($modals) ?>;

// Función para editar modal
function editModal(modalId) {
    const modal = modalsData.find(m => m.id == modalId);
    
    if (modal) {
        // Aquí podrías implementar un modal de edición similar al de creación
        alert('Funcionalidad de edición en desarrollo. Modal ID: ' + modalId);
    }
}

// Función para vista previa
function previewModal(modalId) {
    const modal = modalsData.find(m => m.id == modalId);
    
    if (modal) {
        let previewHTML = `
            <div class="modal-preview" style="max-width: 400px; margin: 0 auto;">
                <h4 class="mb-3">${modal.title}</h4>
                ${modal.image_url ? `<img src="${modal.image_url}" class="img-fluid mb-3 rounded" alt="Modal Image">` : ''}
                <div class="mb-3">${modal.content}</div>
                <button class="btn btn-primary">${modal.button_text}</button>
            </div>
            <div class="mt-4 p-3 bg-light rounded">
                <small class="text-muted">
                    <strong>Configuración:</strong><br>
                    Frecuencia: ${modal.display_frequency}<br>
                    Posición: ${modal.position}<br>
                    Tamaño: ${modal.size}<br>
                    ${modal.auto_close_seconds ? `Auto-cerrar: ${modal.auto_close_seconds}s<br>` : ''}
                    Estado: ${modal.status}
                </small>
            </div>
        `;
        
        document.getElementById('preview-content').innerHTML = previewHTML;
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    }
}

// Función para cambiar estado
function toggleModalStatus(modalId) {
    if (confirm('¿Confirmas el cambio de estado de este modal?')) {
        window.location.href = `/back-SLO/public/promotional-modals/toggle/${modalId}`;
    }
}

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
include __DIR__ . '/../base.php'; 
?>