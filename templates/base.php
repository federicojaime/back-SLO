<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'San Luis Opina - Panel Administrativo' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="/back-SLO/public/assets/css/style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dashboard-layout">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-header">
                    <div class="d-flex align-items-center">
                        <div class="sidebar-logo">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <span class="sidebar-title">San Luis Opina</span>
                    </div>
                </div>

                <div class="sidebar-nav">
                    <div class="nav-section">
                        <span class="nav-section-title">NAVEGACIÓN</span>
                        <ul class="nav-items">
                            <li class="nav-item">
                                <a href="/back-SLO/public/dashboard" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">
                                    <i class="fas fa-chart-pie"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/back-SLO/public/articles" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/articles') !== false) ? 'active' : '' ?>">
                                    <i class="fas fa-newspaper"></i>
                                    <span>Artículos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/back-SLO/public/articles/create" class="nav-link">
                                    <i class="fas fa-plus"></i>
                                    <span>Nuevo Artículo</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/back-SLO/public/categories" class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/categories') !== false) ? 'active' : '' ?>">
                                    <i class="fas fa-tags"></i>
                                    <span>Categorías</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/back-SLO/public/users" class="nav-link">
                                    <i class="fas fa-users"></i>
                                    <span>Usuarios</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="nav-section">
                        <span class="nav-section-title">CONFIGURACIÓN</span>
                        <ul class="nav-items">
                            <li class="nav-item">
                                <a href="/back-SLO/public/settings" class="nav-link">
                                    <i class="fas fa-cog"></i>
                                    <span>Configuración</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/back-SLO/public/profile" class="nav-link">
                                    <i class="fas fa-user"></i>
                                    <span>Mi Perfil</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="sidebar-footer">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <div class="user-name"><?= $_SESSION['full_name'] ?></div>
                            <div class="user-role"><?= ucfirst($_SESSION['role']) ?></div>
                        </div>
                        <div class="user-actions">
                            <a href="/back-SLO/public/logout" class="logout-btn" title="Cerrar Sesión">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Top Header -->
                <div class="top-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
                            <span class="page-subtitle">
                                <?php
                                if (strpos($_SERVER['REQUEST_URI'], '/articles') !== false) echo 'Gestión de contenido';
                                elseif (strpos($_SERVER['REQUEST_URI'], '/categories') !== false) echo 'Organización del sitio';
                                elseif (strpos($_SERVER['REQUEST_URI'], '/users') !== false) echo 'Administración de usuarios';
                                elseif (strpos($_SERVER['REQUEST_URI'], '/settings') !== false) echo 'Configuración del sistema';
                                elseif (strpos($_SERVER['REQUEST_URI'], '/profile') !== false) echo 'Tu información personal';
                                else echo 'Panel de control del portal';
                                ?>
                            </span>
                        </div>
                        <div class="header-actions">
                            <div class="status-indicator">
                                <span class="status-dot"></span>
                                <span class="status-text">Online</span>
                            </div>
                            <div class="current-time"><?= date('d/m/Y H:i') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="content-area">
                    <!-- Alertas -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3"></i>
                                <div class="flex-grow-1"><?= $_SESSION['success'] ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    <?php unset($_SESSION['success']);
                    endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-3"></i>
                                <div class="flex-grow-1"><?= $_SESSION['error'] ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    <?php unset($_SESSION['error']);
                    endif; ?>

                    <!-- Page Content -->
                    <?= $content ?? '' ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Login sin sidebar -->
        <div class="login-wrapper">
            <?= $content ?? '' ?>
        </div>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="/back-SLO/public/assets/js/app.js"></script>

    <script>
        // Sidebar toggle para móviles
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            // Auto-collapse en móviles
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
            }

            // Auto-hide alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-success');
                alerts.forEach(function(alert) {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) closeBtn.click();
                });
            }, 5000);
        });
    </script>
</body>

</html>