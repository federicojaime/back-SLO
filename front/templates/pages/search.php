<?php
// templates/pages/search.php - Página de resultados de búsqueda
include '../templates/layout/header.php';

$search_term = $_GET['q'] ?? '';
$safe_search_term = safe_html($search_term);
?>

<main class="main-content">
    <!-- Search Hero -->
    <section class="search-hero">
        <div class="container">
            <div class="search-hero-content">
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Inicio</a></li>
                        <li class="breadcrumb-item active">Búsqueda</li>
                    </ol>
                </nav>

                <h1 class="search-title">
                    <?php if (!empty($search_term)): ?>
                        Resultados para: "<span class="search-query"><?= $safe_search_term ?></span>"
                    <?php else: ?>
                        Búsqueda en <?= SITE_NAME ?>
                    <?php endif; ?>
                </h1>

                <?php if (isset($articles) && !empty($search_term)): ?>
                    <p class="search-results-count">
                        <?= $articles['total'] ?> resultado<?= $articles['total'] != 1 ? 's' : '' ?> encontrado<?= $articles['total'] != 1 ? 's' : '' ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Search Form Section -->
    <section class="search-form-section">
        <div class="container">
            <div class="search-form-wrapper">
                <form class="search-form-main" action="<?= SITE_URL ?>/buscar" method="GET">
                    <div class="search-input-group">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="search"
                            name="q"
                            class="search-input-main"
                            placeholder="¿Qué estás buscando?"
                            value="<?= $safe_search_term ?>"
                            autocomplete="off"
                            required>
                        <button type="submit" class="search-btn-main">
                            Buscar
                        </button>
                    </div>

                    <!-- Search Suggestions -->
                    <div class="search-suggestions" id="search-suggestions" style="display: none;">
                        <div class="suggestions-content">
                            <h6>Sugerencias de búsqueda:</h6>
                            <div class="suggestions-list" id="suggestions-list">
                                <!-- Dynamic suggestions will be loaded here -->
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Popular Searches -->
                <div class="popular-searches">
                    <span class="popular-label">Búsquedas populares:</span>
                    <div class="popular-tags">
                        <a href="<?= SITE_URL ?>/buscar?q=intendente" class="popular-tag">intendente</a>
                        <a href="<?= SITE_URL ?>/buscar?q=salud" class="popular-tag">salud</a>
                        <a href="<?= SITE_URL ?>/buscar?q=educación" class="popular-tag">educación</a>
                        <a href="<?= SITE_URL ?>/buscar?q=turismo" class="popular-tag">turismo</a>
                        <a href="<?= SITE_URL ?>/buscar?q=minería" class="popular-tag">minería</a>
                        <a href="<?= SITE_URL ?>/buscar?q=seguridad" class="popular-tag">seguridad</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($search_term)): ?>
        <!-- Search Results -->
        <section class="search-results-section">
            <div class="container">
                <?php if (isset($error_message)): ?>
                    <!-- Error Message -->
                    <div class="search-error">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Error en la búsqueda</h3>
                        <p><?= safe_html($error_message) ?></p>
                        <a href="<?= SITE_URL ?>/buscar" class="btn btn-primary-custom">
                            Nueva búsqueda
                        </a>
                    </div>

                <?php elseif (empty($articles['data'])): ?>
                    <!-- No Results -->
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="fas fa-search fa-3x"></i>
                        </div>
                        <h3>No se encontraron resultados</h3>
                        <p>No pudimos encontrar artículos que coincidan con "<strong><?= $safe_search_term ?></strong>".</p>

                        <div class="search-tips">
                            <h5>Sugerencias para mejorar tu búsqueda:</h5>
                            <ul>
                                <li>Verifica la ortografía de las palabras</li>
                                <li>Usa términos más generales</li>
                                <li>Prueba con sinónimos</li>
                                <li>Reduce el número de palabras</li>
                            </ul>
                        </div>

                        <div class="search-alternatives">
                            <h6>O explora estas categorías:</h6>
                            <div class="category-suggestions">
                                <?php
                                $categories = get_categories();
                                $featured_categories = array_slice($categories, 0, 4);
                                foreach ($featured_categories as $category):
                                ?>
                                    <a href="<?= SITE_URL ?>/categoria/<?= $category['slug'] ?>"
                                        class="category-suggestion"
                                        style="border-color: <?= $category['color'] ?? '#6b7280' ?>">
                                        <div class="category-suggestion-icon" style="background-color: <?= $category['color'] ?? '#6b7280' ?>">
                                            <?php if (isset($GLOBALS['main_categories'][$category['slug']])): ?>
                                                <i class="<?= $GLOBALS['main_categories'][$category['slug']]['icon'] ?>"></i>
                                            <?php else: ?>
                                                <i class="fas fa-tag"></i>
                                            <?php endif; ?>
                                        </div>
                                        <span><?= safe_html($category['name']) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-info">
                            <h3>Resultados de búsqueda</h3>
                            <p><?= count($articles['data']) ?> de <?= $articles['total'] ?> resultados</p>
                        </div>

                        <div class="results-filters">
                            <div class="sort-options">
                                <label for="search-sort">Ordenar por:</label>
                                <select id="search-sort" class="form-select form-select-sm">
                                    <option value="relevance">Relevancia</option>
                                    <option value="newest">Más recientes</option>
                                    <option value="oldest">Más antiguos</option>
                                    <option value="popular">Más populares</option>
                                </select>
                            </div>

                            <div class="view-options">
                                <button class="view-toggle active" data-view="list" title="Vista lista">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button class="view-toggle" data-view="grid" title="Vista grilla">
                                    <i class="fas fa-th"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="search-loading" class="search-loading" style="display: none;">
                        <div class="loading-grid">
                            <?php for ($i = 0; $i < 6; $i++): ?>
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

                    <!-- Results Grid -->
                    <div id="search-results-grid" class="search-results-grid view-list">
                        <?php foreach ($articles['data'] as $index => $article): ?>
                            <article class="search-result-item">
                                <div class="search-result-image">
                                    <img src="<?= get_image_url($article['featured_image']) ?>"
                                        alt="<?= safe_html($article['title']) ?>"
                                        loading="lazy">

                                    <?php if ($article['category_name']): ?>
                                        <span class="search-result-category"
                                            style="background-color: <?= $article['category_color'] ?? '#6b7280' ?>">
                                            <?= safe_html($article['category_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="search-result-content">
                                    <h4 class="search-result-title">
                                        <a href="<?= SITE_URL ?>/articulo/<?= $article['slug'] ?>">
                                            <?= highlight_search_terms(safe_html($article['title']), $search_term) ?>
                                        </a>
                                    </h4>

                                    <?php if ($article['excerpt']): ?>
                                        <p class="search-result-excerpt">
                                            <?= highlight_search_terms(safe_html(smart_excerpt($article['excerpt'], 150)), $search_term) ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="search-result-meta">
                                        <div class="meta-left">
                                            <span class="search-result-date">
                                                <i class="fas fa-calendar"></i>
                                                <?= time_ago($article['published_at'] ?: $article['created_at']) ?>
                                            </span>

                                            <?php if ($article['author_name']): ?>
                                                <span class="search-result-author">
                                                    <i class="fas fa-user"></i>
                                                    <?= safe_html($article['author_name']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="meta-right">
                                            <?php if (isset($article['views']) && $article['views'] > 0): ?>
                                                <span class="search-result-views">
                                                    <i class="fas fa-eye"></i>
                                                    <?= format_number($article['views']) ?>
                                                </span>
                                            <?php endif; ?>

                                            <span class="search-result-reading-time">
                                                <i class="fas fa-clock"></i>
                                                <?= max(1, ceil(str_word_count(strip_tags($article['content'] ?? '')) / 200)) ?> min
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="search-result-actions">
                                    <a href="<?= SITE_URL ?>/articulo/<?= $article['slug'] ?>"
                                        class="read-more-btn">
                                        <i class="fas fa-arrow-right"></i>
                                        Leer más
                                    </a>

                                    <button class="share-btn"
                                        onclick="shareArticle(event, '<?= $article['id'] ?>', '<?= safe_html($article['title']) ?>', '<?= SITE_URL ?>/articulo/<?= $article['slug'] ?>')"
                                        aria-label="Compartir artículo">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <div class="search-pagination">
                            <?= render_pagination($pagination, "/buscar?q=" . urlencode($search_term)) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Related Categories -->
    <?php if (!empty($search_term) && !empty($articles['data'])): ?>
        <section class="search-related-section">
            <div class="container">
                <h4>Categorías relacionadas</h4>
                <div class="related-categories-grid">
                    <?php
                    $categories = get_categories();
                    $random_categories = array_slice($categories, 0, 6);
                    foreach ($random_categories as $category):
                    ?>
                        <a href="<?= SITE_URL ?>/categoria/<?= $category['slug'] ?>"
                            class="related-category-card">
                            <div class="category-icon" style="background-color: <?= $category['color'] ?? '#6b7280' ?>">
                                <?php if (isset($GLOBALS['main_categories'][$category['slug']])): ?>
                                    <i class="<?= $GLOBALS['main_categories'][$category['slug']]['icon'] ?>"></i>
                                <?php else: ?>
                                    <i class="fas fa-tag"></i>
                                <?php endif; ?>
                            </div>
                            <div class="category-info">
                                <h6><?= safe_html($category['name']) ?></h6>
                                <span><?= $category['article_count'] ?? 0 ?> artículos</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<style>
    /* Search Page Styles */
    .search-hero {
        background: var(--gradient-primary);
        color: var(--white);
        padding: 60px 0 40px;
        position: relative;
    }

    .search-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .search-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .search-title {
        font-size: clamp(2rem, 5vw, 3rem);
        font-weight: 900;
        margin-bottom: 15px;
        letter-spacing: -0.02em;
    }

    .search-query {
        color: var(--accent);
        background: rgba(255, 255, 255, 0.1);
        padding: 4px 12px;
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    .search-results-count {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Search Form Section */
    .search-form-section {
        background: var(--white);
        padding: 40px 0;
        border-bottom: 1px solid var(--gray-200);
    }

    .search-form-wrapper {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
    }

    .search-input-group {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--white);
        border: 3px solid var(--gray-200);
        border-radius: 25px;
        padding: 8px;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-lg);
        margin-bottom: 20px;
    }

    .search-input-group:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .search-icon {
        padding: 0 15px;
        color: var(--gray-400);
        font-size: 18px;
    }

    .search-input-main {
        flex: 1;
        border: none;
        outline: none;
        font-size: 18px;
        padding: 15px 10px;
        background: transparent;
        color: var(--gray-800);
    }

    .search-input-main::placeholder {
        color: var(--gray-400);
    }

    .search-btn-main {
        background: var(--gradient-primary);
        color: var(--white);
        border: none;
        padding: 12px 30px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-left: 10px;
    }

    .search-btn-main:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        box-shadow: var(--shadow-xl);
        z-index: 1000;
        margin-top: 5px;
    }

    .suggestions-content {
        padding: 20px;
    }

    .suggestions-content h6 {
        color: var(--gray-600);
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .suggestions-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .popular-searches {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .popular-label {
        color: var(--gray-600);
        font-weight: 600;
        font-size: 14px;
    }

    .popular-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .popular-tag {
        background: var(--gray-100);
        color: var(--gray-700);
        padding: 6px 12px;
        border-radius: 15px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .popular-tag:hover {
        background: var(--primary);
        color: var(--white);
        transform: translateY(-2px);
    }

    /* Search Results */
    .search-results-section {
        padding: 40px 0;
        background: var(--gray-50);
    }

    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--gray-200);
    }

    .results-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 5px;
    }

    .results-info p {
        color: var(--gray-600);
        margin: 0;
    }

    .results-filters {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .sort-options {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sort-options label {
        font-size: 14px;
        font-weight: 500;
        color: var(--gray-600);
    }

    .view-options {
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

    /* Search Results Grid */
    .search-results-grid.view-list {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .search-results-grid.view-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .search-result-item {
        background: var(--white);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-200);
    }

    .search-result-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary);
    }

    .search-results-grid.view-list .search-result-item {
        display: flex;
        height: 200px;
    }

    .search-results-grid.view-grid .search-result-item {
        display: flex;
        flex-direction: column;
        height: auto;
    }

    .search-result-image {
        position: relative;
        overflow: hidden;
    }

    .search-results-grid.view-list .search-result-image {
        width: 250px;
        height: 100%;
        flex-shrink: 0;
    }

    .search-results-grid.view-grid .search-result-image {
        width: 100%;
        height: 200px;
    }

    .search-result-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .search-result-item:hover .search-result-image img {
        transform: scale(1.05);
    }

    .search-result-category {
        position: absolute;
        top: 10px;
        left: 10px;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .search-result-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .search-result-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .search-result-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .search-result-title a:hover {
        color: var(--primary);
    }

    .search-result-title mark {
        background: var(--accent);
        color: var(--dark);
        padding: 2px 4px;
        border-radius: 4px;
    }

    .search-result-excerpt {
        color: var(--gray-600);
        line-height: 1.6;
        margin-bottom: 15px;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .search-result-excerpt mark {
        background: var(--accent);
        color: var(--dark);
        padding: 2px 4px;
        border-radius: 4px;
    }

    .search-result-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: var(--gray-500);
        margin-top: auto;
    }

    .meta-left,
    .meta-right {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .search-result-date,
    .search-result-author,
    .search-result-views,
    .search-result-reading-time {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .search-result-actions {
        padding: 15px 20px;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--gray-50);
    }

    .read-more-btn {
        background: var(--gradient-primary);
        color: var(--white);
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .read-more-btn:hover {
        color: var(--white);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .share-btn {
        background: var(--gray-200);
        border: none;
        color: var(--gray-600);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .share-btn:hover {
        background: var(--primary);
        color: var(--white);
        transform: scale(1.1);
    }

    /* Error and No Results States */
    .search-error,
    .no-results {
        text-align: center;
        padding: 80px 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .error-icon,
    .no-results-icon {
        color: var(--gray-400);
        margin-bottom: 30px;
    }

    .search-error h3,
    .no-results h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 15px;
    }

    .search-error p,
    .no-results p {
        color: var(--gray-600);
        margin-bottom: 30px;
        font-size: 1.1rem;
    }

    .search-tips {
        background: var(--gray-50);
        border-radius: 12px;
        padding: 25px;
        margin: 30px 0;
        text-align: left;
    }

    .search-tips h5 {
        color: var(--gray-800);
        font-weight: 700;
        margin-bottom: 15px;
    }

    .search-tips ul {
        color: var(--gray-600);
        margin: 0;
        padding-left: 20px;
    }

    .search-tips li {
        margin-bottom: 8px;
    }

    .search-alternatives {
        margin-top: 40px;
    }

    .search-alternatives h6 {
        color: var(--gray-700);
        font-weight: 600;
        margin-bottom: 20px;
    }

    .category-suggestions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .category-suggestion {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
    }

    .category-suggestion:hover {
        color: inherit;
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .category-suggestion-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* Related Categories */
    .search-related-section {
        padding: 60px 0;
        background: var(--white);
    }

    .search-related-section h4 {
        text-align: center;
        margin-bottom: 40px;
        font-weight: 700;
        color: var(--gray-800);
    }

    .related-categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .related-category-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: var(--gray-50);
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        border: 1px solid var(--gray-200);
    }

    .related-category-card:hover {
        color: inherit;
        background: var(--white);
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .related-category-card .category-icon {
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

    .related-category-card .category-info h6 {
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--gray-800);
    }

    .related-category-card .category-info span {
        color: var(--gray-500);
        font-size: 0.9rem;
    }

    /* Loading States */
    .loading-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .loading-card {
        background: var(--white);
        border-radius: 16px;
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
        .search-hero {
            padding: 40px 0 30px;
        }

        .search-title {
            font-size: 2rem;
        }

        .search-input-group {
            flex-direction: column;
            gap: 10px;
            padding: 15px;
        }

        .search-input-main {
            font-size: 16px;
            padding: 10px;
        }

        .search-btn-main {
            width: 100%;
            margin: 0;
        }

        .popular-searches {
            flex-direction: column;
            gap: 10px;
        }

        .results-header {
            flex-direction: column;
            gap: 20px;
            align-items: flex-start;
        }

        .results-filters {
            width: 100%;
            justify-content: space-between;
        }

        .search-results-grid.view-list .search-result-item {
            flex-direction: column;
            height: auto;
        }

        .search-results-grid.view-list .search-result-image {
            width: 100%;
            height: 200px;
        }

        .search-result-meta {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .category-suggestions {
            grid-template-columns: 1fr;
        }

        .related-categories-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeSearchPage();
    });

    function initializeSearchPage() {
        setupSearchSuggestions();
        setupViewToggles();
        setupSortOptions();
        trackSearchEvent();
    }

    // Search suggestions
    function setupSearchSuggestions() {
        const searchInput = document.querySelector('.search-input-main');
        const suggestions = document.getElementById('search-suggestions');
        let suggestionTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(suggestionTimeout);

                if (query.length >= 2) {
                    suggestionTimeout = setTimeout(() => {
                        loadSearchSuggestions(query);
                    }, 300);
                } else {
                    suggestions.style.display = 'none';
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-form-main')) {
                    suggestions.style.display = 'none';
                }
            });
        }
    }

    async function loadSearchSuggestions(query) {
        try {
            const response = await fetch(`<?= SITE_URL ?>/api/search-suggestions?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.success && data.suggestions.length > 0) {
                displaySuggestions(data.suggestions);
            }
        } catch (error) {
            console.error('Error loading suggestions:', error);
        }
    }

    function displaySuggestions(suggestions) {
        const suggestionsList = document.getElementById('suggestions-list');
        const suggestionsContainer = document.getElementById('search-suggestions');

        suggestionsList.innerHTML = '';

        suggestions.forEach(suggestion => {
            const tag = document.createElement('a');
            tag.href = `<?= SITE_URL ?>/buscar?q=${encodeURIComponent(suggestion)}`;
            tag.className = 'popular-tag';
            tag.textContent = suggestion;
            suggestionsList.appendChild(tag);
        });

        suggestionsContainer.style.display = 'block';
    }

    // View toggles
    function setupViewToggles() {
        const viewToggles = document.querySelectorAll('.view-toggle');
        const resultsGrid = document.getElementById('search-results-grid');

        viewToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const view = this.dataset.view;

                viewToggles.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                resultsGrid.className = `search-results-grid view-${view}`;

                localStorage.setItem('search_view_preference', view);
            });
        });

        // Load saved preference
        const savedView = localStorage.getItem('search_view_preference');
        if (savedView) {
            const toggle = document.querySelector(`[data-view="${savedView}"]`);
            if (toggle) {
                toggle.click();
            }
        }
    }

    // Sort functionality
    function setupSortOptions() {
        const sortSelect = document.getElementById('search-sort');

        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const sortBy = this.value;
                sortSearchResults(sortBy);
            });
        }
    }

    function sortSearchResults(sortBy) {
        const resultsGrid = document.getElementById('search-results-grid');
        const results = Array.from(resultsGrid.children);

        results.sort((a, b) => {
            switch (sortBy) {
                case 'newest':
                    return new Date(b.dataset.publishedAt || 0) - new Date(a.dataset.publishedAt || 0);
                case 'oldest':
                    return new Date(a.dataset.publishedAt || 0) - new Date(b.dataset.publishedAt || 0);
                case 'popular':
                    return (parseInt(b.dataset.views) || 0) - (parseInt(a.dataset.views) || 0);
                case 'relevance':
                default:
                    return 0; // Keep original order for relevance
            }
        });

        results.forEach(result => resultsGrid.appendChild(result));
    }

    // Track search events
    function trackSearchEvent() {
        const searchTerm = '<?= $search_term ?>';
        const resultsCount = <?= isset($articles) ? count($articles['data']) : 0 ?>;

        if (searchTerm && typeof gtag !== 'undefined') {
            gtag('event', 'search', {
                'search_term': searchTerm,
                'results_count': resultsCount
            });
        }
    }

    // Share functionality (reuse from article card)
    function shareArticle(event, articleId, title, url) {
        event.stopPropagation();

        if (navigator.share) {
            navigator.share({
                title: title,
                text: `Interesante artículo: ${title}`,
                url: url
            }).catch(console.error);
        } else {
            // Fallback share menu
            const shareMenu = document.createElement('div');
            shareMenu.className = 'share-menu-overlay';
            shareMenu.innerHTML = `
            <div class="share-menu-content">
                <h6>Compartir artículo</h6>
                <div class="share-options">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" 
                       target="_blank" class="share-option facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}" 
                       target="_blank" class="share-option twitter">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}" 
                       target="_blank" class="share-option whatsapp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
                <button onclick="this.closest('.share-menu-overlay').remove()" class="close-share">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            document.body.appendChild(shareMenu);

            shareMenu.addEventListener('click', function(e) {
                if (e.target === shareMenu) {
                    shareMenu.remove();
                }
            });
        }
    }
</script>

<?php include '../templates/layout/footer.php'; ?>