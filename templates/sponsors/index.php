<?php 
$title = 'Gestión de Sponsors - San Luis Opina';
ob_start();
?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-light mb-1">Gestión de Sponsors</h2>
                <p class="text-muted small mb-0">Administra los patrocinadores del portal</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createSponsorModal">
                <i class="fas fa-plus me-2"></i>Nuevo Sponsor
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-handshake me-2"></i>Lista de Sponsors
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
        <?php if (empty($sponsors)): ?>
            <div class="text-center py-5">
                <div class="metric-icon primary d-inline-flex mb-3">
                    <i class="fas fa-handshake fa-3x"></i>
                </div>
                <h5 class="text-dark mb-2">No hay sponsors</h5>
                <p class="text-muted mb-4">Agrega tu primer sponsor para comenzar</p>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createSponsorModal">
                    <i class="fas fa-plus me-2"></i>Agregar Primer Sponsor
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Sponsor</th>
                            <th>Descripción</th>
                            <th>Contacto</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sponsors as $sponsor): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="width: 60px; height: 45px;">
                                        <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" 
                                             alt="<?= htmlspecialchars($sponsor['name']) ?>"
                                             class="img-fluid rounded border"
                                             style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold">
                                            <?php if ($sponsor['website_url']): ?>
                                                <a href="<?= htmlspecialchars($sponsor['website_url']) ?>" 
                                                   target="_blank" class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($sponsor['name']) ?>
                                                    <i class="fas fa-external-link-alt ms-1 text-muted" style="font-size: 10px;"></i>
                                                </a>
                                            <?php else: ?>
                                                <?= htmlspecialchars($sponsor['name']) ?>
                                            <?php endif; ?>
                                        </h6>
                                        <small class="text-muted">ID: <?= $sponsor['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted"><?= htmlspecialchars($sponsor['description'] ?? 'Sin descripción') ?></span>
                            </td>
                            <td>
                                <div>
                                    <?php if ($sponsor['contact_email']): ?>
                                        <div class="mb-1">
                                            <i class="fas fa-envelope text-muted me-2"></i>
                                            <a href="mailto:<?= htmlspecialchars($sponsor['contact_email']) ?>" 
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($sponsor['contact_email']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($sponsor['phone']): ?>
                                        <div>
                                            <i class="fas fa-phone text-muted me-2"></i>
                                            <a href="tel:<?= htmlspecialchars($sponsor['phone']) ?>" 
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($sponsor['phone']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-sort-numeric-down text-muted me-2"></i>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                        <?= $sponsor['priority'] ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           <?= $sponsor['status'] === 'active' ? 'checked' : '' ?>
                                           onchange="toggleSponsorStatus(<?= $sponsor['id'] ?>)">
                                    <label class="form-check-label">
                                        <span class="badge bg-<?= $sponsor['status'] === 'active' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $sponsor['status'] === 'active' ? 'success' : 'secondary' ?> rounded-pill px-3 py-2">
                                            <?= $sponsor['status'] === 'active' ? 'ACTIVO' : 'INACTIVO' ?>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-medium"><?= date('d/m/Y', strtotime($sponsor['created_at'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($sponsor['created_at'])) ?> hs</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editSponsor(<?= $sponsor['id'] ?>)" 
                                            title="Editar" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="editSponsor(<?= $sponsor['id'] ?>)">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="toggleSponsorStatus(<?= $sponsor['id'] ?>)">
                                                    <i class="fas fa-toggle-<?= $sponsor['status'] === 'active' ? 'off' : 'on' ?> me-2"></i>
                                                    <?= $sponsor['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" 
                                                   href="/back-SLO/public/sponsors/delete/<?= $sponsor['id'] ?>"
                                                   onclick="return confirm('¿Estás seguro de eliminar este sponsor?')">
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

<!-- Estadísticas de sponsors -->
<div class="row g-4 mt-4">
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon primary me-3">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <div class="metric-label">Total Sponsors</div>
                    <div class="metric-value" style="font-size: 24px;"><?= count($sponsors) ?></div>
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
                        <?= count(array_filter($sponsors, function ($s) {
                            return $s['status'] === 'active';
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
                    <div class="metric-label">Inactivos</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($sponsors, function ($s) {
                            return $s['status'] === 'inactive';
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
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <div class="metric-label">Prioridad Alta</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($sponsors, function ($s) {
                            return $s['priority'] <= 3 && $s['status'] === 'active';
                        })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Sponsor -->
<div class="modal fade" id="createSponsorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Sponsor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/back-SLO/public/sponsors" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Sponsor *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioridad</label>
                                <input type="number" class="form-control" id="priority" name="priority" 
                                       value="0" min="0" max="100">
                                <small class="form-text text-muted">0 = Mayor prioridad</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Descripción breve del sponsor..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo del Sponsor *</label>
                        <input type="file" class="form-control" id="logo" name="logo" 
                               accept="image/*" required>
                        <small class="form-text text-muted">Formatos: JPG, PNG, GIF, SVG. Tamaño recomendado: 200x100px</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="website_url" class="form-label">Sitio Web</label>
                                <input type="url" class="form-control" id="website_url" name="website_url"
                                       placeholder="https://ejemplo.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_email" class="form-label">Email de Contacto</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email"
                                       placeholder="contacto@sponsor.com">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       placeholder="+54 266 123-4567">
                            </div>
                        </div>
                        <div class="col-md-6">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Crear Sponsor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Sponsor -->
<div class="modal fade" id="editSponsorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Sponsor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSponsorForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nombre del Sponsor *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_priority" class="form-label">Prioridad</label>
                                <input type="number" class="form-control" id="edit_priority" name="priority" 
                                       min="0" max="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_logo" class="form-label">Nuevo Logo (opcional)</label>
                        <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*">
                        <small class="form-text text-muted">Dejar vacío para mantener el logo actual</small>
                        <div id="current_logo_preview" class="mt-2"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_website_url" class="form-label">Sitio Web</label>
                                <input type="url" class="form-control" id="edit_website_url" name="website_url">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_contact_email" class="form-label">Email de Contacto</label>
                                <input type="email" class="form-control" id="edit_contact_email" name="contact_email">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="edit_phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Estado</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                </select>
                            </div>
                        </div>
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
// Función para editar sponsor
function editSponsor(sponsorId) {
    const sponsors = <?= json_encode($sponsors) ?>;
    const sponsor = sponsors.find(s => s.id == sponsorId);
    
    if (sponsor) {
        document.getElementById('edit_name').value = sponsor.name;
        document.getElementById('edit_description').value = sponsor.description || '';
        document.getElementById('edit_priority').value = sponsor.priority;
        document.getElementById('edit_website_url').value = sponsor.website_url || '';
        document.getElementById('edit_contact_email').value = sponsor.contact_email || '';
        document.getElementById('edit_phone').value = sponsor.phone || '';
        document.getElementById('edit_status').value = sponsor.status;
        
        // Mostrar logo actual
        const logoPreview = document.getElementById('current_logo_preview');
        logoPreview.innerHTML = `
            <img src="${sponsor.logo_url}" alt="Logo actual" 
                 class="img-thumbnail" style="max-height: 80px;">
            <small class="d-block text-muted mt-1">Logo actual</small>
        `;
        
        document.getElementById('editSponsorForm').action = `/back-SLO/public/sponsors/edit/${sponsorId}`;
        
        new bootstrap.Modal(document.getElementById('editSponsorModal')).show();
    }
}

// Función para cambiar estado
function toggleSponsorStatus(sponsorId) {
    if (confirm('¿Confirmas el cambio de estado de este sponsor?')) {
        window.location.href = `/back-SLO/public/sponsors/toggle/${sponsorId}`;
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