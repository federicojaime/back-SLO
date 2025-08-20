<?php
// templates/layout/header.php - Header común del frontend
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Meta Tags SEO -->
    <?= get_meta_tags($page_title ?? null, $meta_description ?? null) ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon.png">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>front/public/assets/css/style.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Structured Data -->
    <?php if (isset($article)): ?>
        <?= get_article_structured_data($article) ?>
    <?php endif; ?>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Cargando San Luis Opina...</div>
    </div>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="live-indicator">
                        <div class="live-dot"></div>
                        <span>EN VIVO - <?= RADIO_FREQUENCY ?></span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        <span><?= date('l, d F Y', time()) ?></span>
                        <div class="social-links-top">
                            <a href="<?= FACEBOOK_URL ?>" target="_blank" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="<?= INSTAGRAM_URL ?>" target="_blank" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="<?= TWITTER_URL ?>" target="_blank" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header" id="main-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <!-- Logo -->
                    <a class="navbar-brand logo-container" href="<?= SITE_URL ?>">
                        <img src="<?= SITE_URL ?><?= LOGO_URL ?>" alt="<?= SITE_NAME ?>" class="logo-image">
                        <div>
                            <div class="site-title"><?= SITE_NAME ?></div>
                            <div class="site-subtitle">Portal de Noticias</div>
                        </div>
                    </a>

                    <!-- Mobile Toggle -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Navigation -->
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page === 'home' ? 'active' : '' ?>" href="<?= SITE_URL ?>">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>

                            <?php
                            $categories = get_categories();
                            foreach ($categories as $category):
                                $active = ($current_page === 'category' && isset($category_slug) && $category_slug === $category['slug']) ? 'active' : '';
                            ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $active ?>" href="<?= SITE_URL ?>/categoria/<?= $category['slug'] ?>">
                                        <?php if (isset($GLOBALS['main_categories'][$category['slug']])): ?>
                                            <i class="<?= $GLOBALS['main_categories'][$category['slug']]['icon'] ?>"></i>
                                        <?php endif; ?>
                                        <?= safe_html($category['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Search -->
                        <div class="navbar-nav">
                            <form class="d-flex search-form" role="search" action="<?= SITE_URL ?>/buscar" method="GET">
                                <div class="input-group">
                                    <input class="form-control search-input"
                                        type="search"
                                        name="q"
                                        placeholder="Buscar noticias..."
                                        value="<?= isset($_GET['q']) ? safe_html($_GET['q']) : '' ?>"
                                        autocomplete="off">
                                    <button class="btn search-btn" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>

                                <!-- Search Results Dropdown -->
                                <div class="search-results" id="search-results" style="display: none;">
                                    <div class="search-loading">
                                        <i class="fas fa-spinner fa-spin"></i> Buscando...
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Breadcrumbs (si están definidos) -->
    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
        <div class="breadcrumbs-section">
            <div class="container">
                <?= get_breadcrumbs($breadcrumbs) ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Ocultar loading cuando la página esté lista
        window.addEventListener('load', function() {
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.style.opacity = '0';
                setTimeout(() => {
                    loadingOverlay.style.display = 'none';
                }, 500);
            }
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const searchResults = document.getElementById('search-results');
            let searchTimeout;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    clearTimeout(searchTimeout);

                    if (query.length >= 3) {
                        searchTimeout = setTimeout(() => {
                            performSearch(query);
                        }, 300);
                    } else {
                        searchResults.style.display = 'none';
                    }
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.search-form')) {
                        searchResults.style.display = 'none';
                    }
                });
            }

            async function performSearch(query) {
                try {
                    searchResults.style.display = 'block';
                    searchResults.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';

                    const response = await fetch(`<?= SITE_URL ?>/api/search?q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (data.success && data.data.length > 0) {
                        let html = '<div class="search-header">Resultados de búsqueda:</div>';

                        data.data.forEach(article => {
                            html += `
                                <a href="${article.url}" class="search-result-item">
                                    <div class="search-result-title">${article.title}</div>
                                    <div class="search-result-excerpt">${article.excerpt || ''}</div>
                                    <div class="search-result-meta">
                                        <span class="search-result-category">${article.category_name || 'Sin categoría'}</span>
                                        <span class="search-result-date">${article.time_ago}</span>
                                    </div>
                                </a>
                            `;
                        });

                        html += `<div class="search-footer">
                            <a href="<?= SITE_URL ?>/buscar?q=${encodeURIComponent(query)}" class="search-view-all">
                                Ver todos los resultados <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>`;

                        searchResults.innerHTML = html;
                    } else {
                        searchResults.innerHTML = '<div class="search-no-results">No se encontraron resultados</div>';
                    }
                } catch (error) {
                    console.error('Error en búsqueda:', error);
                    searchResults.innerHTML = '<div class="search-error">Error al buscar. Intenta nuevamente.</div>';
                }
            }
        });
    </script>