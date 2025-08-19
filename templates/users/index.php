<?php 
$title = 'Gestión de Usuarios - San Luis Opina';
ob_start();
?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-light mb-1">Gestión de Usuarios</h2>
                <p class="text-muted small mb-0">Administra los usuarios del sistema</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus me-2"></i>Nuevo Usuario
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Lista de Usuarios
            </h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>Todos los roles</option>
                    <option>Administradores</option>
                    <option>Editores</option>
                    <option>Autores</option>
                </select>
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>Todos los estados</option>
                    <option>Activos</option>
                    <option>Inactivos</option>
                </select>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" class="form-control" placeholder="Buscar usuarios...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <div class="metric-icon primary d-inline-flex mb-3">
                    <i class="fas fa-users fa-3x"></i>
                </div>
                <h5 class="text-dark mb-2">No hay usuarios</h5>
                <p class="text-muted mb-4">Crea el primer usuario para comenzar</p>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus me-2"></i>Crear Primer Usuario
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Artículos</th>
                            <th>Último Login</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-<?= $user['status'] === 'active' ? 'primary' : 'secondary' ?> d-flex align-items-center justify-content-center me-3" 
                                         style="width: 45px; height: 45px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold">
                                            <a href="/back-SLO/public/users/view/<?= $user['id'] ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($user['full_name']) ?>
                                            </a>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted">@<?= htmlspecialchars($user['username']) ?></small>
                                            <small class="text-muted">•</small>
                                            <small class="text-muted">ID: <?= $user['id'] ?></small>
                                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1" style="font-size: 10px;">
                                                    <i class="fas fa-crown me-1"></i>TÚ
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-medium"><?= htmlspecialchars($user['email']) ?></div>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i>Email verificado
                                    </small>
                                </div>
                            </td>
                            <td>
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
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           <?= $user['status'] === 'active' ? 'checked' : '' ?>
                                           <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>
                                           onchange="toggleUserStatus(<?= $user['id'] ?>)">
                                    <label class="form-check-label">
                                        <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?> rounded-pill px-3 py-2">
                                            <?= $user['status'] === 'active' ? 'ACTIVO' : 'INACTIVO' ?>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <?php
                                global $pdo;
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ?");
                                $stmt->execute([$user['id']]);
                                $article_count = $stmt->fetchColumn();
                                ?>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-newspaper text-muted me-2"></i>
                                    <span class="fw-medium"><?= $article_count ?></span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?php if ($user['last_login']): ?>
                                        <div class="fw-medium"><?= date('d/m/Y', strtotime($user['last_login'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($user['last_login'])) ?> hs</small>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/back-SLO/public/users/view/<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-outline-info" title="Ver" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editUser(<?= $user['id'] ?>)" 
                                            title="Editar" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="/back-SLO/public/users/view/<?= $user['id'] ?>">
                                                    <i class="fas fa-eye me-2"></i>Ver perfil
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="editUser(<?= $user['id'] ?>)">
                                                    <i class="fas fa-edit me-2"></i>Editar
                                                </a>
                                            </li>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="toggleUserStatus(<?= $user['id'] ?>)">
                                                    <i class="fas fa-toggle-<?= $user['status'] === 'active' ? 'off' : 'on' ?> me-2"></i>
                                                    <?= $user['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" 
                                                   href="/back-SLO/public/users/delete/<?= $user['id'] ?>"
                                                   onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                    <i class="fas fa-trash me-2"></i>Eliminar
                                                </a>
                                            </li>
                                            <?php endif; ?>
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

<!-- Estadísticas de usuarios -->
<div class="row g-4 mt-4">
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon primary me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="metric-label">Total Usuarios</div>
                    <div class="metric-value" style="font-size: 24px;"><?= count($users) ?></div>
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
                        <?= count(array_filter($users, function($u) { return $u['status'] === 'active'; })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon danger me-3">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <div class="metric-label">Administradores</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($users, function($u) { return $u['role'] === 'admin'; })) ?>
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
                    <div class="metric-label">Editores</div>
                    <div class="metric-value" style="font-size: 24px;">
                        <?= count(array_filter($users, function($u) { return $u['role'] === 'editor'; })) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/back-SLO/public/users">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuario *</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="author">Autor</option>
                                    <option value="editor">Editor</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_username" class="form-label">Nombre de usuario *</label>
                                <input type="text" class="form-control" id="edit_username" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">Nueva contraseña</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                                <small class="form-text text-muted">Dejar en blanco para mantener la actual</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_role" class="form-label">Rol</label>
                                <select class="form-select" id="edit_role" name="role">
                                    <option value="author">Autor</option>
                                    <option value="editor">Editor</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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

                    <div class="mb-3">
                        <label for="edit_bio" class="form-label">Biografía</label>
                        <textarea class="form-control" id="edit_bio" name="bio" rows="3"></textarea>
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
// Función para editar usuario
function editUser(userId) {
    // Buscar el usuario en los datos
    const users = <?= json_encode($users) ?>;
    const user = users.find(u => u.id == userId);
    
    if (user) {
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_full_name').value = user.full_name;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_status').value = user.status;
        document.getElementById('edit_bio').value = user.bio || '';
        document.getElementById('editUserForm').action = `/back-SLO/public/users/edit/${userId}`;
        
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }
}

// Función para cambiar estado
function toggleUserStatus(userId) {
    if (confirm('¿Confirmas el cambio de estado de este usuario?')) {
        window.location.href = `/back-SLO/public/users/toggle/${userId}`;
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