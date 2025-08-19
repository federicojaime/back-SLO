<?php
$title = 'Editar: ' . htmlspecialchars($user['full_name']) . ' - San Luis Opina';
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
                <h1><i class="fas fa-edit text-primary"></i> Editar Usuario</h1>
                <p class="text-muted mb-0">
                    ID: #<?= $user['id'] ?> | 
                    Registrado: <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                        <span class="badge bg-info ms-2">Tu perfil</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="/back-SLO/public/users/view/<?= $user['id'] ?>" class="btn btn-outline-info">
                    <i class="fas fa-eye"></i> Ver Perfil
                </a>
                <a href="/back-SLO/public/users" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-edit"></i> Información del Usuario</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/back-SLO/public/users/edit/<?= $user['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuario *</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nueva contraseña</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="form-text text-muted">Dejar en blanco para mantener la actual</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="password_confirm">
                                <small class="form-text text-muted">Solo para verificación</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol del usuario</label>
                                <select class="form-select" id="role" name="role" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="author" <?= $user['role'] === 'author' ? 'selected' : '' ?>>Autor</option>
                                    <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <small class="form-text text-muted">No puedes cambiar tu propio rol</small>
                                    <input type="hidden" name="role" value="<?= $user['role'] ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                                    <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <small class="form-text text-muted">No puedes desactivar tu propia cuenta</small>
                                    <input type="hidden" name="status" value="<?= $user['status'] ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Biografía</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4" 
                                  placeholder="Información adicional sobre el usuario..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="/back-SLO/public/users" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Avatar del usuario -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user-circle me-2"></i>Avatar</h6>
            </div>
            <div class="card-body text-center">
                <div class="rounded-circle bg-<?= $user['status'] === 'active' ? 'primary' : 'secondary' ?> d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 100px; height: 100px;">
                    <i class="fas fa-user text-white fa-3x"></i>
                </div>
                <h6 class="mb-2"><?= htmlspecialchars($user['full_name']) ?></h6>
                <p class="text-muted mb-3">@<?= htmlspecialchars($user['username']) ?></p>
                <button class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-camera me-2"></i>Cambiar Avatar
                </button>
            </div>
        </div>

        <!-- Información del usuario -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">ID:</span>
                            <code>#<?= $user['id'] ?></code>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Estado:</span>
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?> px-3 py-2">
                                <?= $user['status'] === 'active' ? 'ACTIVO' : 'INACTIVO' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Rol:</span>
                            <?php
                            $roleConfig = [
                                'admin' => ['class' => 'danger', 'text' => 'ADMIN'],
                                'editor' => ['class' => 'warning', 'text' => 'EDITOR'],
                                'author' => ['class' => 'info', 'text' => 'AUTOR']
                            ];
                            $config = $roleConfig[$user['role']] ?? ['class' => 'secondary', 'text' => strtoupper($user['role'])];
                            ?>
                            <span class="badge bg-<?= $config['class'] ?> bg-opacity-10 text-<?= $config['class'] ?> px-3 py-2">
                                <?= $config['text'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Registrado:</span>
                            <span><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Último login:</span>
                            <span><?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas del usuario -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h6>
            </div>
            <div class="card-body">
                <?php
                global $pdo;
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ?");
                $stmt->execute([$user['id']]);
                $total_articles = $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ? AND status = 'published'");
                $stmt->execute([$user['id']]);
                $published_articles = $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT SUM(views) FROM articles WHERE author_id = ?");
                $stmt->execute([$user['id']]);
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
                    <a href="/back-SLO/public/articles?author=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-list me-2"></i>Ver Artículos del Usuario
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones peligrosas -->
        <?php if ($user['id'] != $_SESSION['user_id']): ?>
        <div class="card">
            <div class="card-header bg-danger bg-opacity-10">
                <h6 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Zona de Peligro</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Las siguientes acciones son irreversibles.</p>
                
                <?php if ($total_articles > 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>No se puede eliminar:</strong> Este usuario tiene <?= $total_articles ?> artículo(s) asociado(s).
                </div>
                <?php else: ?>
                <a href="/back-SLO/public/users/delete/<?= $user['id'] ?>" 
                   class="btn btn-outline-danger btn-sm w-100"
                   onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                    <i class="fas fa-trash me-2"></i>Eliminar Usuario
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validar que las contraseñas coincidan
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    function validatePasswords() {
        if (password.value && passwordConfirm.value) {
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
            } else {
                passwordConfirm.setCustomValidity('');
            }
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../base.php';
?>