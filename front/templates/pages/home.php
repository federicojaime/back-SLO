<?php
// templates/pages/home.php - Página Principal
include '../templates/layout/header.php';
?>

<main class="main-content">
    <!-- Hero Section con Noticia Destacada -->
    <?php if ($featured_article): ?>
        <section class="hero-section">
            <div class="container">
                <div class="hero-featured" onclick="window.location.href='/articulo/<?= $featured_article['slug'] ?>'">
                    <div class="hero-image">
                        <img src="<?= get_image_url($featured_article['featured_image']) ?>"
                            alt="<?= safe_html($featured_article['title']) ?>"
                            loading="eager">
                        <div class="hero-overlay">
                            <?php if ($featured_article['category_name']): ?>
                                <span class="hero-category" style="background-color: <?= $featured_article['category_color'] ?? '#dc2626' ?>">
                                    <?= safe_html($featured_article['category_name']) ?>
                                </span>
                            <?php endif; ?>

                            <h1 class="hero-title"><?= safe_html($featured_article['title']) ?></h1>

                            <?php if ($featured_article['excerpt']): ?>
                                <p class="hero-excerpt"><?= safe_html($featured_article['excerpt']) ?></p>
                            <?php endif; ?>

                            <div class="hero-meta">
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <?= time_ago($featured_article['published_at'] ?? $featured_article['created_at']) ?>
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    <?= format_number($featured_article['views'] ?? 0) ?> vistas
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-user"></i>
                                    <?= safe_html($featured_article['author_name']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Filtros de Categorías -->
    <section class="categories-section">
        <div class="container">
            <div class="categories-filter">
                <button class="category-btn active" data-category="all">
                    <i class="fas fa-globe"></i>
                    Todas las Noticias
                </button>

                <?php foreach ($categories as $category): ?>
                    <?php
                    $category_info = $GLOBALS['main_categories'][$category['slug']] ?? null;
                    $icon = $category_info ? $category_info['icon'] : 'fas fa-tag';
                    $color = $category['color'] ?? '#6b7280';
                    ?>
                    <button class="category-btn" data-category="<?= $category['slug'] ?>" data-color="<?= $color ?>">
                        <i class="<?= $icon ?>"></i>
                        <?= safe_html($category['name']) ?>
                        <span class="category-count"><?= $category['article_count'] ?? 0 ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Grid de Noticias -->
    <section class="news-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Últimas Noticias</h2>
                <p class="section-subtitle">Mantente informado con las noticias más importantes de San Luis</p>
            </div>

            <!-- Loading State -->
            <div id="news-loading" class="news-loading" style="display: none;">
                <div class="loading-grid">
                    <?php for ($i = 0; $i < 12; $i++): ?>
                        <div class="loading-card">
                            <div class="loading-image"></div>
                            <div class="loading-content">
                                <div class="loading-line short"></div>
                                <div class="loading-line"></div>
                                <div class="loading-line"></div>
                                <div class="loading-line medium"></div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Grid de Artículos -->
            <div id="news-grid" class="news-grid">
                <?php foreach ($latest_articles['data'] as $index => $article): ?>
                    <?php include '../templates/components/article-card.php'; ?>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button -->
            <div class="load-more-section">
                <button id="load-more-btn" class="load-more-btn" data-page="2">
                    <i class="fas fa-plus"></i>
                    Cargar más noticias
                </button>
            </div>
        </div>
    </section>

    <!-- Newsletter Subscription -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-card">
                <div class="newsletter-content">
                    <div class="newsletter-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="newsletter-title">Mantente Informado</h3>
                    <p class="newsletter-description">
                        Recibe las noticias más importantes de San Luis directamente en tu email
                    </p>
                    <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                        <div class="form-group">
                            <input type="email"
                                class="newsletter-input"
                                placeholder="Tu email"
                                required>
                            <button type="submit" class="newsletter-btn">
                                <i class="fas fa-paper-plane"></i>
                                Suscribirse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeHomePage();
    });

    function initializeHomePage() {
        setupCategoryFilters();
        setupLoadMore();
        setupInfiniteScroll();
    }

    // Sistema de filtros por categoría
    function setupCategoryFilters() {
        const categoryBtns = document.querySelectorAll('.category-btn');
        const newsGrid = document.getElementById('news-grid');
        const loadMoreBtn = document.getElementById('load-more-btn');

        categoryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.dataset.category;
                const color = this.dataset.color;

                // Actualizar botones activos
                categoryBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Actualizar color del botón si tiene
                if (color) {
                    this.style.setProperty('--category-color', color);
                }

                // Cargar noticias de la categoría
                loadNewsByCategory(category);

                // Reset del botón load more
                loadMoreBtn.dataset.page = '2';
                loadMoreBtn.dataset.category = category;
            });
        });
    }

    // Cargar noticias por categoría
    async function loadNewsByCategory(category) {
        const newsGrid = document.getElementById('news-grid');
        const loadingState = document.getElementById('news-loading');

        // Mostrar loading
        newsGrid.style.display = 'none';
        loadingState.style.display = 'block';

        try {
            const response = await fetch(`/api/load-more?category=${category}&page=1`);
            const data = await response.json();

            if (data.success) {
                newsGrid.innerHTML = '';

                data.data.forEach((article, index) => {
                    const articleCard = createArticleCard(article, index);
                    newsGrid.appendChild(articleCard);
                });

                // Animar entrada
                setTimeout(() => {
                    newsGrid.style.display = 'grid';
                    loadingState.style.display = 'none';

                    // Trigger animations
                    newsGrid.querySelectorAll('.news-card').forEach((card, index) => {
                        setTimeout(() => {
                            card.classList.add('animate-in');
                        }, index * 100);
                    });
                }, 500);
            }
        } catch (error) {
            console.error('Error loading news:', error);
            loadingState.style.display = 'none';
            newsGrid.style.display = 'grid';
        }
    }

    // Sistema Load More
    function setupLoadMore() {
        const loadMoreBtn = document.getElementById('load-more-btn');

        loadMoreBtn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            const category = this.dataset.category || 'all';

            loadMoreArticles(page, category);

            this.dataset.page = page + 1;
        });
    }

    // Cargar más artículos
    async function loadMoreArticles(page, category) {
        const loadMoreBtn = document.getElementById('load-more-btn');
        const newsGrid = document.getElementById('news-grid');

        // Estado de carga
        loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
        loadMoreBtn.disabled = true;

        try {
            const response = await fetch(`/api/load-more?category=${category}&page=${page}`);
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                data.data.forEach((article, index) => {
                    const articleCard = createArticleCard(article, index);
                    newsGrid.appendChild(articleCard);

                    // Animar entrada
                    setTimeout(() => {
                        articleCard.classList.add('animate-in');
                    }, index * 100);
                });

                // Restaurar botón
                loadMoreBtn.innerHTML = '<i class="fas fa-plus"></i> Cargar más noticias';
                loadMoreBtn.disabled = false;

                // Ocultar botón si no hay más artículos
                if (data.data.length < 12) {
                    loadMoreBtn.style.display = 'none';
                }
            } else {
                // No hay más artículos
                loadMoreBtn.innerHTML = '<i class="fas fa-check"></i> No hay más noticias';
                loadMoreBtn.disabled = true;
                setTimeout(() => {
                    loadMoreBtn.style.display = 'none';
                }, 2000);
            }
        } catch (error) {
            console.error('Error loading more articles:', error);
            loadMoreBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error al cargar';
            loadMoreBtn.disabled = false;
        }
    }

    // Infinite scroll opcional
    function setupInfiniteScroll() {
        let isLoading = false;

        window.addEventListener('scroll', function() {
            if (isLoading) return;

            const loadMoreBtn = document.getElementById('load-more-btn');
            if (!loadMoreBtn || loadMoreBtn.style.display === 'none') return;

            const rect = loadMoreBtn.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;

            if (isVisible) {
                isLoading = true;
                loadMoreBtn.click();

                setTimeout(() => {
                    isLoading = false;
                }, 2000);
            }
        });
    }

    // Crear tarjeta de artículo dinámicamente
    function createArticleCard(article, index) {
        const card = document.createElement('article');
        card.className = 'news-card';
        card.style.setProperty('--delay', `${index * 0.1}s`);

        const categoryColor = article.category_color || '#6b7280';
        const imageUrl = article.featured_image || '/assets/images/default-news.jpg';

        card.innerHTML = `
        <a href="/articulo/${article.slug}" class="card-link">
            <div class="card-image">
                <img src="${imageUrl}" alt="${article.title}" loading="lazy">
                ${article.category_name ? `
                <span class="card-category" style="background-color: ${categoryColor}">
                    ${article.category_name}
                </span>
                ` : ''}
            </div>
            <div class="card-content">
                <h3 class="card-title">${article.title}</h3>
                ${article.excerpt ? `<p class="card-excerpt">${article.excerpt}</p>` : ''}
                <div class="card-meta">
                    <span class="meta-item">
                        <i class="fas fa-clock"></i>
                        ${formatTimeAgo(article.published_at || article.created_at)}
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-eye"></i>
                        ${formatNumber(article.views || 0)}
                    </span>
                </div>
            </div>
        </a>
    `;

        return card;
    }

    // Newsletter subscription
    function subscribeNewsletter(event) {
        event.preventDefault();

        const form = event.target;
        const email = form.querySelector('input[type="email"]').value;
        const btn = form.querySelector('button');

        // Simular suscripción (implementar con tu backend)
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-check"></i> ¡Suscrito!';
            form.reset();

            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Suscribirse';
                btn.disabled = false;
            }, 2000);
        }, 1000);
    }

    // Utilidades
    function formatTimeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'hace unos segundos';
        if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`;
        if (diff < 86400) return `hace ${Math.floor(diff / 3600)} h`;
        if (diff < 2592000) return `hace ${Math.floor(diff / 86400)} días`;

        return date.toLocaleDateString('es-AR');
    }

    function formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toString();
    }
</script>

<?php include '../templates/layout/footer.php'; ?>