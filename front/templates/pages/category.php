<?php
// templates/pages/category.php - Vista por categoría
include '../templates/layout/header.php';

// Breadcrumbs
$breadcrumbs = [
    ['title' => $category['name'], 'url' => '']
];
?>

<main class="main-content">
    <!-- Hero Section de Categoría -->
    <section class="category-hero">
        <div class="container">
            <div class="hero-content">
                <!-- Breadcrumbs -->
                <?= get_breadcrumbs($breadcrumbs) ?>
                
                <div class="category-header">
                    <div class="category-icon" style="background-color: <?= $category['color'] ?? '#6b7280' ?>">
                        <?php if (isset($GLOBALS['main_categories'][$category['slug']])): ?>
                            <i class="<?= $GLOBALS['main_categories'][$category['slug']]['icon'] ?>"></i>
                        <?php else: ?>
                            <i class="fas fa-tag"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="category-info">
                        <h1 class="category-title"><?= safe_html($category['name']) ?></h1>
                        
                        <?php if ($category['description']): ?>
                            <p class="category-description"><?= safe_html($category['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="category-stats">
                            <span class="stat-item">
                                <i class="fas fa-newspaper"></i>
                                <?= $articles['total'] ?> artículo<?= $articles['total'] != 1 ? 's' : '' ?>
                            </span>
                            <span class="stat-item">
                                <i class="fas fa-calendar"></i>
                                Última actualización: <?= date('d/m/Y') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtros y Búsqueda -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-bar">
                <div class="filters-left">
                    <div class="view-toggles">
                        <button class="view-toggle active" data-view="grid" title="Vista en grilla">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-toggle" data-view="list" title="Vista en lista">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    
                    <div class="sort-options">
                        <select class="form-select form-select-sm" id="sort-selector">
                            <option value="newest">Más recientes</option>
                            <option value="oldest">Más antiguos</option>
                            <option value="popular">Más populares</option>
                            <option value="alphabetical">A-Z</option>
                        </select>
                    </div>
                </div>
                
                <div class="filters-right">
                    <div class="search-filter">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               placeholder="Buscar en <?= safe_html($category['name']) ?>..." 
                               id="category-search">
                    </div>
                    
                    <div class="results-count">
                        <span id="results-counter"><?= count($articles['data']) ?></span> resultados
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Grid de Artículos -->
    <section class="articles-section">
        <div class="container">
            <?php if (empty($articles['data'])): ?>
                <!-- Estado vacío -->
                <div class="empty-state">
                    <div class="empty-icon" style="color: <?= $category['color'] ?? '#6b7280' ?>">
                        <i class="fas fa-newspaper fa-3x"></i>
                    </div>
                    <h3>No hay artículos en esta categoría</h3>
                    <p>Aún no se han publicado artículos en <strong><?= safe_html($category['name']) ?></strong>.</p>
                    <a href="<?= SITE_URL ?>" class="btn btn-primary-custom">
                        <i class="fas fa-arrow-left"></i> Volver al Inicio
                    </a>
                </div>
            <?php else: ?>
                <!-- Loading State -->
                <div id="articles-loading" class="articles-loading" style="display: none;">
                    <div class="loading-grid">
                        <?php for ($i = 0; $i < 12; $i++): ?>
                            <div class="loading-card skeleton">
                                <div class="loading-image skeleton"></div>
                                <div class="loading-content">
                                    <div class="loading-line skeleton"></div>
                                    <div class="loading-line skeleton"></div>
                                    <div class="loading-line skeleton short"></div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Articles Grid -->
                <div id="articles-grid" class="articles-grid view-grid">
                    <?php foreach ($articles['data'] as $index => $article): ?>
                        <?php include '../templates/components/article-card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination-section">
                        <?= render_pagination($pagination, "/categoria/{$category['slug']}") ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Sidebar con Categorías Relacionadas -->
    <section class="related-categories">
        <div class="container">
            <div class="sidebar-section">
                <h4 class="sidebar-title">
                    <i class="fas fa-tags"></i>
                    Otras Categorías
                </h4>
                
                <div class="categories-grid">
                    <?php 
                    $all_categories = get_categories();
                    $related_categories = array_filter($all_categories, function($cat) use ($category) {
                        return $cat['id'] != $category['id'];
                    });
                    $limited_categories = array_slice($related_categories, 0, 6);
                    ?>
                    
                    <?php foreach ($limited_categories as $rel_category): ?>
                        <a href="<?= SITE_URL ?>/categoria/<?= $rel_category['slug'] ?>" 
                           class="category-card"
                           style="border-color: <?= $rel_category['color'] ?? '#6b7280' ?>">
                            <div class="category-card-icon" style="background-color: <?= $rel_category['color'] ?? '#6b7280' ?>">
                                <?php if (isset($GLOBALS['main_categories'][$rel_category['slug']])): ?>
                                    <i class="<?= $GLOBALS['main_categories'][$rel_category['slug']]['icon'] ?>"></i>
                                <?php else: ?>
                                    <i class="fas fa-tag"></i>
                                <?php endif; ?>
                            </div>
                            <div class="category-card-content">
                                <h6><?= safe_html($rel_category['name']) ?></h6>
                                <span class="article-count"><?= $rel_category['article_count'] ?? 0 ?> artículos</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Category Hero Styles */
.category-hero {
    background: var(--gradient-primary);
    color: var(--white);
    padding: 40px 0;
    position: relative;
    overflow: hidden;
}

.category-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 20px;
}

.category-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
    box-shadow: var(--shadow-xl);
    flex-shrink: 0;
}

.category-info {
    flex: 1;
}

.category-title {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 10px;
    letter-spacing: -0.02em;
}

.category-description {
    font-size: 1.125rem;
    opacity: 0.9;
    margin-bottom: 15px;
    line-height: 1.6;
}

.category-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Filters Section */
.filters-section {
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: 20px 0;
    position: sticky;
    top: 80px;
    z-index: 100;
}

.filters-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.filters-left,
.filters-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.view-toggles {
    display: flex;
    gap: 5px;
}

.view-toggle {
    width: 36px;
    height: 36px;
    border: 2px solid var(--gray-300);
    background: var(--white);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--gray-600);
}

.view-toggle.active,
.view-toggle:hover {
    border-color: var(--primary);
    background: var(--primary);
    color: var(--white);
}

.results-count {
    font-size: 0.9rem;
    color: var(--gray-600);
    font-weight: 500;
}

/* Articles Grid */
.articles-section {
    padding: 40px 0;
    background: var(--gray-50);
}

.articles-grid.view-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.articles-grid.view-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.articles-grid.view-list .news-card {
    display: flex;
    height: 200px;
}

.articles-grid.view-list .news-card-image-container {
    width: 300px;
    height: 100%;
    flex-shrink: 0;
}

.articles-grid.view-list .news-card-content {
    flex: 1;
    height: 100%;
    padding: 20px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-icon {
    margin-bottom: 30px;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 15px;
}

.empty-state p {
    color: var(--gray-600);
    margin-bottom: 30px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* Related Categories */
.related-categories {
    padding: 60px 0;
    background: var(--white);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.category-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
    color: inherit;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    color: inherit;
}

.category-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
}

.category-card-content h6 {
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--gray-800);
}

.article-count {
    font-size: 0.85rem;
    color: var(--gray-500);
}

/* Loading States */
.loading-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.loading-card {
    background: var(--white);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid var(--gray-200);
}

.loading-image {
    height: 200px;
    background: var(--gray-200);
}

.loading-content {
    padding: 20px;
}

.loading-line {
    height: 16px;
    background: var(--gray-200);
    border-radius: 4px;
    margin-bottom: 10px;
}

.loading-line.short {
    width: 60%;
}

/* Responsive Design */
@media (max-width: 768px) {
    .category-title {
        font-size: 2rem;
    }
    
    .category-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .category-icon {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
    
    .filters-bar {
        flex-direction: column;
        gap: 15px;
    }
    
    .filters-left,
    .filters-right {
        width: 100%;
        justify-content: space-between;
    }
    
    .articles-grid.view-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .articles-grid.view-list .news-card {
        flex-direction: column;
        height: auto;
    }
    
    .articles-grid.view-list .news-card-image-container {
        width: 100%;
        height: 200px;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCategoryPage();
});

function initializeCategoryPage() {
    setupViewToggles();
    setupSortOptions();
    setupCategorySearch();
}

// View toggles (grid/list)
function setupViewToggles() {
    const viewToggles = document.querySelectorAll('.view-toggle');
    const articlesGrid = document.getElementById('articles-grid');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active state
            viewToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update grid class
            articlesGrid.className = `articles-grid view-${view}`;
            
            // Save preference
            localStorage.setItem('preferred_view', view);
        });
    });
    
    // Load saved preference
    const savedView = localStorage.getItem('preferred_view');
    if (savedView) {
        const toggle = document.querySelector(`[data-view="${savedView}"]`);
        if (toggle) {
            toggle.click();
        }
    }
}

// Sort functionality
function setupSortOptions() {
    const sortSelector = document.getElementById('sort-selector');
    
    sortSelector.addEventListener('change', function() {
        const sortBy = this.value;
        sortArticles(sortBy);
    });
}

function sortArticles(sortBy) {
    const articlesGrid = document.getElementById('articles-grid');
    const articles = Array.from(articlesGrid.children);
    
    articles.sort((a, b) => {
        switch (sortBy) {
            case 'newest':
                return new Date(b.dataset.publishedAt || 0) - new Date(a.dataset.publishedAt || 0);
            case 'oldest':
                return new Date(a.dataset.publishedAt || 0) - new Date(b.dataset.publishedAt || 0);
            case 'popular':
                return (parseInt(b.dataset.views) || 0) - (parseInt(a.dataset.views) || 0);
            case 'alphabetical':
                return a.querySelector('.news-title').textContent.localeCompare(
                    b.querySelector('.news-title').textContent
                );
            default:
                return 0;
        }
    });
    
    // Re-append sorted articles
    articles.forEach(article => articlesGrid.appendChild(article));
}

// Category search
function setupCategorySearch() {
    const searchInput = document.getElementById('category-search');
    const resultsCounter = document.getElementById('results-counter');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterArticles(query);
            }, 300);
        });
    }
    
    function filterArticles(query) {
        const articles = document.querySelectorAll('#articles-grid .news-card');
        let visibleCount = 0;
        
        articles.forEach(article => {
            const title = article.querySelector('.news-title').textContent.toLowerCase();
            const excerpt = article.querySelector('.news-excerpt')?.textContent.toLowerCase() || '';
            
            const matches = title.includes(query) || excerpt.includes(query);
            
            if (matches || query === '') {
                article.style.display = '';
                visibleCount++;
            } else {
                article.style.display = 'none';
            }
        });
        
        if (resultsCounter) {
            resultsCounter.textContent = visibleCount;
        }
        
        // Show/hide empty state
        const articlesGrid = document.getElementById('articles-grid');
        const emptyState = document.querySelector('.empty-state');
        
        if (visibleCount === 0 && query !== '') {
            if (!document.querySelector('.search-empty-state')) {
                const searchEmpty = document.createElement('div');
                searchEmpty.className = 'search-empty-state empty-state';
                searchEmpty.innerHTML = `
                    <div class="empty-icon">
                        <i class="fas fa-search fa-3x"></i>
                    </div>
                    <h3>No se encontraron resultados</h3>
                    <p>No hay artículos que coincidan con "<strong>${query}</strong>" en esta categoría.</p>
                    <button onclick="clearCategorySearch()" class="btn btn-primary-custom">
                        <i class="fas fa-times"></i> Limpiar búsqueda
                    </button>
                `;
                articlesGrid.after(searchEmpty);
            }
        } else {
            const searchEmpty = document.querySelector('.search-empty-state');
            if (searchEmpty) {
                searchEmpty.remove();
            }
        }
    }
}

function clearCategorySearch() {
    const searchInput = document.getElementById('category-search');
    if (searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    }
}

// Infinite scroll functionality
let isLoading = false;
let currentPage = <?= $pagination['current_page'] ?>;
const totalPages = <?= $pagination['total_pages'] ?>;
const categorySlug = '<?= $category['slug'] ?>';

function setupInfiniteScroll() {
    window.addEventListener('scroll', function() {
        if (isLoading || currentPage >= totalPages) return;
        
        const threshold = document.documentElement.scrollHeight - window.innerHeight - 1000;
        
        if (window.scrollY > threshold) {
            loadMoreArticles();
        }
    });
}

async function loadMoreArticles() {
    if (isLoading || currentPage >= totalPages) return;
    
    isLoading = true;
    currentPage++;
    
    try {
        showLoadingState();
        
        const response = await fetch(`<?= SITE_URL ?>/api/load-more?category=${categorySlug}&page=${currentPage}`);
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            appendArticles(data.data);
        }
        
        hideLoadingState();
    } catch (error) {
        console.error('Error loading more articles:', error);
        currentPage--; // Revert page increment
        hideLoadingState();
    }
    
    isLoading = false;
}

function showLoadingState() {
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'infinite-loading';
    loadingIndicator.className = 'text-center py-4';
    loadingIndicator.innerHTML = `
        <div class="d-inline-flex align-items-center gap-2">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span>Cargando más artículos...</span>
        </div>
    `;
    
    document.querySelector('.articles-section .container').appendChild(loadingIndicator);
}

function hideLoadingState() {
    const loadingIndicator = document.getElementById('infinite-loading');
    if (loadingIndicator) {
        loadingIndicator.remove();
    }
}

function appendArticles(articles) {
    const articlesGrid = document.getElementById('articles-grid');
    const currentCount = articlesGrid.children.length;
    
    articles.forEach((article, index) => {
        const articleCard = createArticleCard(article, currentCount + index);
        articlesGrid.appendChild(articleCard);
        
        // Animate entrance
        setTimeout(() => {
            articleCard.style.opacity = '1';
            articleCard.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function createArticleCard(article, index) {
    const card = document.createElement('article');
    card.className = 'news-card';
    card.dataset.articleId = article.id;
    card.dataset.publishedAt = article.published_at || article.created_at;
    card.dataset.views = article.views || 0;
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'all 0.6s ease';
    
    const imageUrl = article.featured_image || '<?= SITE_URL . DEFAULT_IMAGE ?>';
    const categoryColor = article.category_color || '#6b7280';
    const articleUrl = `<?= SITE_URL ?>/articulo/${article.slug}`;
    const timeAgo = formatTimeAgo(article.published_at || article.created_at);
    const excerpt = truncateText(article.excerpt || article.content || '', 120);
    
    card.innerHTML = `
        <div class="news-card-image-container">
            <img src="${imageUrl}" 
                 alt="${escapeHtml(article.title)}" 
                 class="news-card-image"
                 loading="lazy"
                 onerror="this.src='<?= SITE_URL . DEFAULT_IMAGE ?>'">
            
            ${article.category_name ? `
                <span class="news-category" style="background-color: ${categoryColor}">
                    ${escapeHtml(article.category_name)}
                </span>
            ` : ''}
            
            <div class="reading-time">
                <i class="fas fa-clock"></i>
                ${Math.max(1, Math.ceil((article.content || '').split(' ').length / 200))} min
            </div>
        </div>
        
        <div class="news-card-content">
            <h3 class="news-title">
                <a href="${articleUrl}" class="article-link">
                    ${escapeHtml(article.title)}
                </a>
            </h3>
            
            ${excerpt ? `<p class="news-excerpt">${escapeHtml(excerpt)}</p>` : ''}
            
            <div class="news-meta">
                <div class="meta-left">
                    <span class="news-date">
                        <i class="fas fa-calendar-alt"></i>
                        ${timeAgo}
                    </span>
                    
                    ${article.author_name ? `
                        <span class="news-author">
                            <i class="fas fa-user"></i>
                            ${escapeHtml(article.author_name)}
                        </span>
                    ` : ''}
                </div>
                
                <div class="meta-right">
                    ${article.views > 0 ? `
                        <span class="news-views">
                            <i class="fas fa-eye"></i>
                            ${formatNumber(article.views)}
                        </span>
                    ` : ''}
                    
                    <button class="share-btn" 
                            onclick="shareArticle(event, '${article.id}', '${escapeHtml(article.title)}', '${articleUrl}')"
                            aria-label="Compartir artículo">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-hover-overlay">
            <div class="hover-content">
                <i class="fas fa-arrow-right"></i>
                <span>Leer más</span>
            </div>
        </div>
    `;
    
    // Add click handler
    card.addEventListener('click', function(e) {
        if (!e.target.closest('.share-btn')) {
            window.location.href = articleUrl;
        }
    });
    
    return card;
}

// Utility functions
function formatTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return 'Hace unos segundos';
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} h`;
    if (diff < 2592000) return `Hace ${Math.floor(diff / 86400)} días`;

    return date.toLocaleDateString('es-AR');
}

function formatNumber(num) {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
}

function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength).replace(/\s+\S*$/, '') + '...';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize infinite scroll if enabled
if (totalPages > 1) {
    setupInfiniteScroll();
}

// Analytics tracking
function trackCategoryView() {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'category_view', {
            'event_category': 'engagement',
            'event_label': categorySlug
        });
    }
}

// Track category view
trackCategoryView();
</script>

<?php include '../templates/layout/footer.php'; ?>