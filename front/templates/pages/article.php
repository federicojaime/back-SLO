<?php
// templates/pages/article.php - Vista individual de artículo
include '../templates/layout/header.php';

// Breadcrumbs
$breadcrumbs = [];
if ($article['category_name']) {
    $breadcrumbs[] = ['title' => $article['category_name'], 'url' => "/categoria/{$article['category_slug']}"];
}
$breadcrumbs[] = ['title' => $article['title'], 'url' => ''];
?>

<main class="main-content">
    <!-- Article Header -->
    <article class="article-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <!-- Breadcrumbs -->
                    <?= get_breadcrumbs($breadcrumbs) ?>
                    
                    <!-- Article Header -->
                    <header class="article-header">
                        <?php if ($article['category_name']): ?>
                            <div class="article-category">
                                <a href="<?= SITE_URL ?>/categoria/<?= $article['category_slug'] ?>" 
                                   class="category-badge"
                                   style="background-color: <?= $article['category_color'] ?? '#6b7280' ?>">
                                    <?php if (isset($GLOBALS['main_categories'][$article['category_slug']])): ?>
                                        <i class="<?= $GLOBALS['main_categories'][$article['category_slug']]['icon'] ?>"></i>
                                    <?php endif; ?>
                                    <?= safe_html($article['category_name']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <h1 class="article-title"><?= safe_html($article['title']) ?></h1>
                        
                        <?php if ($article['excerpt']): ?>
                            <div class="article-excerpt">
                                <?= safe_html($article['excerpt']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Article Meta -->
                        <div class="article-meta">
                            <div class="meta-left">
                                <div class="author-info">
                                    <div class="author-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="author-details">
                                        <span class="author-name">Por <?= safe_html($article['author_name']) ?></span>
                                        <span class="publish-date">
                                            <?= format_date($article['published_at'] ?: $article['created_at'], 'full') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="meta-right">
                                <div class="article-stats">
                                    <span class="stat-item">
                                        <i class="fas fa-eye"></i>
                                        <?= format_number($article['views']) ?> vistas
                                    </span>
                                    <span class="stat-item">
                                        <i class="fas fa-clock"></i>
                                        <?= max(1, ceil(str_word_count(strip_tags($article['content'])) / 200)) ?> min lectura
                                    </span>
                                </div>
                                
                                <!-- Share Buttons -->
                                <div class="share-buttons">
                                    <button class="share-btn facebook" onclick="shareOn('facebook')">
                                        <i class="fab fa-facebook-f"></i>
                                    </button>
                                    <button class="share-btn twitter" onclick="shareOn('twitter')">
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                    <button class="share-btn whatsapp" onclick="shareOn('whatsapp')">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="share-btn copy" onclick="copyArticleUrl()">
                                        <i class="fas fa-link"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </header>
                    
                    <!-- Featured Image -->
                    <?php if ($article['featured_image']): ?>
                        <div class="article-featured-image">
                            <img src="<?= get_image_url($article['featured_image']) ?>" 
                                 alt="<?= safe_html($article['featured_image_alt'] ?: $article['title']) ?>"
                                 class="featured-image">
                            <?php if ($article['featured_image_alt']): ?>
                                <figcaption class="image-caption">
                                    <?= safe_html($article['featured_image_alt']) ?>
                                </figcaption>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Article Content -->
                    <div class="article-content">
                        <?= $article['content'] ?>
                    </div>
                    
                    <!-- Article Footer -->
                    <footer class="article-footer">
                        <!-- Tags -->
                        <?php if (!empty($article['tags'])): ?>
                            <div class="article-tags">
                                <h6>Etiquetas:</h6>
                                <div class="tags-list">
                                    <?php foreach ($article['tags'] as $tag): ?>
                                        <a href="<?= SITE_URL ?>/tag/<?= $tag['slug'] ?>" 
                                           class="tag-link"
                                           style="background-color: <?= $tag['color'] ?? '#6b7280' ?>">
                                            #<?= safe_html($tag['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Share Again -->
                        <div class="share-section">
                            <h6>¿Te gustó este artículo? ¡Compártelo!</h6>
                            <div class="share-buttons-large">
                                <a href="#" onclick="shareOn('facebook')" class="share-btn-large facebook">
                                    <i class="fab fa-facebook-f"></i>
                                    <span>Compartir en Facebook</span>
                                </a>
                                <a href="#" onclick="shareOn('twitter')" class="share-btn-large twitter">
                                    <i class="fab fa-twitter"></i>
                                    <span>Compartir en Twitter</span>
                                </a>
                                <a href="#" onclick="shareOn('whatsapp')" class="share-btn-large whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>Compartir en WhatsApp</span>
                                </a>
                                <a href="#" onclick="copyArticleUrl()" class="share-btn-large copy">
                                    <i class="fas fa-link"></i>
                                    <span>Copiar enlace</span>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Author Bio -->
                        <div class="author-bio">
                            <div class="author-avatar-large">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info-detailed">
                                <h5>Sobre el autor</h5>
                                <h6><?= safe_html($article['author_name']) ?></h6>
                                <p><?= safe_html($article['author_bio'] ?? 'Periodista de San Luis Opina, comprometido con brindar información veraz y actualizada sobre los acontecimientos más relevantes de nuestra provincia.') ?></p>
                                <div class="author-social">
                                    <!-- Add author social links if available -->
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </article>
    
    <!-- Related Articles -->
    <?php if (!empty($related_articles)): ?>
        <section class="related-articles">
            <div class="container">
                <div class="section-header">
                    <h3 class="section-title">Artículos Relacionados</h3>
                    <p class="section-subtitle">Más noticias que podrían interesarte</p>
                </div>
                
                <div class="related-grid">
                    <?php foreach ($related_articles as $index => $relatedArticle): ?>
                        <article class="related-card">
                            <a href="<?= SITE_URL ?>/articulo/<?= $relatedArticle['slug'] ?>" class="related-link">
                                <div class="related-image">
                                    <img src="<?= get_image_url($relatedArticle['featured_image']) ?>" 
                                         alt="<?= safe_html($relatedArticle['title']) ?>"
                                         loading="lazy">
                                    
                                    <?php if ($relatedArticle['category_name']): ?>
                                        <span class="related-category" 
                                              style="background-color: <?= $relatedArticle['category_color'] ?? '#6b7280' ?>">
                                            <?= safe_html($relatedArticle['category_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="related-content">
                                    <h4 class="related-title"><?= safe_html($relatedArticle['title']) ?></h4>
                                    <div class="related-meta">
                                        <span class="related-date">
                                            <?= time_ago($relatedArticle['published_at'] ?: $relatedArticle['created_at']) ?>
                                        </span>
                                        <span class="related-views">
                                            <i class="fas fa-eye"></i>
                                            <?= format_number($relatedArticle['views'] ?? 0) ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?= SITE_URL ?>/categoria/<?= $article['category_slug'] ?>" 
                       class="btn btn-primary-custom">
                        <i class="fas fa-arrow-right"></i>
                        Ver más de <?= safe_html($article['category_name']) ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Newsletter Subscription -->
    <section class="newsletter-cta">
        <div class="container">
            <div class="newsletter-card">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h4>¿Te gustó este artículo?</h4>
                        <p>Suscríbete a nuestro newsletter y recibe las últimas noticias de San Luis directamente en tu email.</p>
                    </div>
                    <div class="col-lg-4">
                        <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                            <div class="input-group">
                                <input type="email" 
                                       class="form-control" 
                                       placeholder="Tu email"
                                       required>
                                <button type="submit" class="btn btn-accent">
                                    <i class="fas fa-paper-plane"></i>
                                    Suscribirse
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Article Page Styles */
.article-page {
    padding: 40px 0;
    background: var(--white);
}

.article-header {
    margin-bottom: 40px;
}

.article-category {
    margin-bottom: 20px;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.category-badge:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.article-title {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 900;
    color: var(--dark);
    line-height: 1.2;
    margin-bottom: 20px;
    letter-spacing: -0.02em;
}

.article-excerpt {
    font-size: 1.25rem;
    color: var(--gray-600);
    line-height: 1.6;
    margin-bottom: 30px;
    padding: 20px;
    background: var(--gray-50);
    border-left: 4px solid var(--primary);
    border-radius: 0 8px 8px 0;
}

.article-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-top: 1px solid var(--gray-200);
    border-bottom: 1px solid var(--gray-200);
    margin-bottom: 30px;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.author-details {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-weight: 700;
    color: var(--dark);
    font-size: 1rem;
}

.publish-date {
    color: var(--gray-500);
    font-size: 0.9rem;
}

.article-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--gray-500);
    font-size: 0.9rem;
    font-weight: 500;
}

.share-buttons {
    display: flex;
    gap: 10px;
}

.share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.share-btn.facebook { background: #1877f2; }
.share-btn.twitter { background: #1da1f2; }
.share-btn.whatsapp { background: #25d366; }
.share-btn.copy { background: var(--gray-600); }

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.article-featured-image {
    margin-bottom: 40px;
    text-align: center;
}

.featured-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    border-radius: 16px;
    box-shadow: var(--shadow-xl);
}

.image-caption {
    margin-top: 15px;
    font-size: 0.9rem;
    color: var(--gray-500);
    font-style: italic;
}

.article-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--gray-800);
    margin-bottom: 50px;
}

.article-content h2,
.article-content h3,
.article-content h4 {
    color: var(--dark);
    margin-top: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.article-content h2 {
    font-size: 1.8rem;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 10px;
}

.article-content h3 {
    font-size: 1.5rem;
}

.article-content h4 {
    font-size: 1.3rem;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 2rem 0;
    box-shadow: var(--shadow-md);
}

.article-content blockquote {
    border-left: 4px solid var(--accent);
    padding: 1.5rem;
    margin: 2rem 0;
    background: var(--gray-50);
    border-radius: 0 12px 12px 0;
    font-style: italic;
    font-size: 1.15rem;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-footer {
    border-top: 2px solid var(--gray-200);
    padding-top: 40px;
}

.article-tags {
    margin-bottom: 40px;
}

.article-tags h6 {
    color: var(--gray-600);
    font-weight: 600;
    margin-bottom: 15px;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.tag-link {
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.tag-link:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.share-section {
    margin-bottom: 40px;
}

.share-section h6 {
    color: var(--gray-800);
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 1.1rem;
}

.share-buttons-large {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.share-btn-large {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.share-btn-large.facebook { background: #1877f2; }
.share-btn-large.twitter { background: #1da1f2; }
.share-btn-large.whatsapp { background: #25d366; }
.share-btn-large.copy { background: var(--gray-600); }

.share-btn-large:hover {
    color: white;
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.author-bio {
    background: var(--gray-50);
    border-radius: 16px;
    padding: 30px;
    display: flex;
    gap: 20px;
    margin-bottom: 40px;
}

.author-avatar-large {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    flex-shrink: 0;
}

.author-info-detailed h5 {
    color: var(--gray-600);
    font-size: 0.9rem;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.author-info-detailed h6 {
    color: var(--dark);
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.author-info-detailed p {
    color: var(--gray-600);
    line-height: 1.6;
    margin: 0;
}

/* Related Articles */
.related-articles {
    padding: 80px 0;
    background: var(--gray-50);
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.related-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.related-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.related-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.related-image {
    position: relative;
    height: 180px;
    overflow: hidden;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.related-card:hover .related-image img {
    transform: scale(1.05);
}

.related-category {
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

.related-content {
    padding: 20px;
}

.related-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 10px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--gray-500);
    font-size: 0.85rem;
}

.related-views {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Newsletter CTA */
.newsletter-cta {
    padding: 60px 0;
    background: var(--gradient-primary);
    color: var(--white);
}

.newsletter-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 40px;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.newsletter-card h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.newsletter-card p {
    opacity: 0.9;
    margin-bottom: 0;
}

.newsletter-form .form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: var(--white);
    backdrop-filter: blur(10px);
}

.newsletter-form .form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.newsletter-form .form-control:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
    background: rgba(255, 255, 255, 0.15);
}

/* Responsive Design */
@media (max-width: 768px) {
    .article-title {
        font-size: 2rem;
    }
    
    .article-meta {
        flex-direction: column;
        gap: 20px;
        align-items: flex-start;
    }
    
    .meta-right {
        width: 100%;
    }
    
    .article-stats {
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    
    .share-buttons-large {
        grid-template-columns: 1fr;
    }
    
    .author-bio {
        flex-direction: column;
        text-align: center;
    }
    
    .author-avatar-large {
        align-self: center;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .newsletter-card {
        padding: 30px 20px;
        text-align: center;
    }
    
    .newsletter-form {
        margin-top: 20px;
    }
    
    .newsletter-form .input-group {
        flex-direction: column;
        gap: 10px;
    }
}

@media (max-width: 576px) {
    .article-excerpt {
        padding: 15px;
        font-size: 1.1rem;
    }
    
    .article-content {
        font-size: 1rem;
    }
    
    .share-buttons {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .tags-list {
        justify-content: center;
    }
    
    .author-bio {
        padding: 20px;
    }
}
</style>

<script>
// Article page functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeArticlePage();
});

function initializeArticlePage() {
    setupReadingProgress();
    setupTableOfContents();
    trackReadingTime();
    setupStickyShare();
}

// Reading progress indicator
function setupReadingProgress() {
    const progressBar = document.createElement('div');
    progressBar.className = 'reading-progress';
    progressBar.innerHTML = '<div class="reading-progress-bar"></div>';
    
    const style = document.createElement('style');
    style.textContent = `
        .reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(99, 102, 241, 0.1);
            z-index: 1000;
        }
        
        .reading-progress-bar {
            height: 100%;
            background: var(--gradient-accent);
            width: 0%;
            transition: width 0.3s ease;
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(progressBar);
    
    const progressBarFill = progressBar.querySelector('.reading-progress-bar');
    const articleContent = document.querySelector('.article-content');
    
    if (articleContent) {
        window.addEventListener('scroll', function() {
            const articleTop = articleContent.offsetTop;
            const articleHeight = articleContent.offsetHeight;
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset;
            
            const start = articleTop - windowHeight / 2;
            const end = articleTop + articleHeight - windowHeight / 2;
            
            if (scrollTop >= start && scrollTop <= end) {
                const progress = (scrollTop - start) / (end - start);
                progressBarFill.style.width = Math.min(100, Math.max(0, progress * 100)) + '%';
            } else if (scrollTop < start) {
                progressBarFill.style.width = '0%';
            } else {
                progressBarFill.style.width = '100%';
            }
        });
    }
}

// Auto-generate table of contents
function setupTableOfContents() {
    const articleContent = document.querySelector('.article-content');
    const headings = articleContent?.querySelectorAll('h2, h3, h4');
    
    if (headings && headings.length > 2) {
        const toc = document.createElement('div');
        toc.className = 'table-of-contents';
        toc.innerHTML = '<h6>Índice de contenidos</h6><ul class="toc-list"></ul>';
        
        const tocList = toc.querySelector('.toc-list');
        
        headings.forEach((heading, index) => {
            const id = `heading-${index}`;
            heading.id = id;
            
            const li = document.createElement('li');
            li.className = `toc-item toc-${heading.tagName.toLowerCase()}`;
            li.innerHTML = `<a href="#${id}" class="toc-link">${heading.textContent}</a>`;
            
            tocList.appendChild(li);
        });
        
        // Insert TOC after first paragraph
        const firstParagraph = articleContent.querySelector('p');
        if (firstParagraph) {
            firstParagraph.after(toc);
        }
        
        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .table-of-contents {
                background: var(--gray-50);
                border: 1px solid var(--gray-200);
                border-radius: 12px;
                padding: 20px;
                margin: 30px 0;
            }
            
            .table-of-contents h6 {
                color: var(--gray-800);
                font-weight: 700;
                margin-bottom: 15px;
                font-size: 1rem;
            }
            
            .toc-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .toc-item {
                margin-bottom: 8px;
            }
            
            .toc-h3 { padding-left: 20px; }
            .toc-h4 { padding-left: 40px; }
            
            .toc-link {
                color: var(--gray-600);
                text-decoration: none;
                font-size: 0.9rem;
                transition: color 0.3s ease;
            }
            
            .toc-link:hover {
                color: var(--primary);
            }
        `;
        document.head.appendChild(style);
        
        // Smooth scroll for TOC links
        toc.addEventListener('click', function(e) {
            if (e.target.classList.contains('toc-link')) {
                e.preventDefault();
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    }
}

// Track reading time
function trackReadingTime() {
    let startTime = Date.now();
    let timeSpent = 0;
    let isVisible = true;
    
    // Track visibility
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            isVisible = false;
        } else {
            isVisible = true;
            startTime = Date.now();
        }
    });
    
    // Track time spent every 5 seconds
    setInterval(function() {
        if (isVisible) {
            timeSpent += 5;
            
            // Send analytics event every 30 seconds
            if (timeSpent % 30 === 0) {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'reading_time', {
                        'event_category': 'engagement',
                        'event_label': '<?= $article['slug'] ?>',
                        'value': timeSpent
                    });
                }
            }
        }
    }, 5000);
    
    // Track when user finishes reading
    window.addEventListener('beforeunload', function() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'article_read', {
                'event_category': 'engagement',
                'event_label': '<?= $article['slug'] ?>',
                'value': timeSpent
            });
        }
    });
}

// Sticky share buttons for mobile
function setupStickyShare() {
    if (window.innerWidth <= 768) {
        const stickyShare = document.createElement('div');
        stickyShare.className = 'sticky-share-mobile';
        stickyShare.innerHTML = `
            <button class="sticky-share-btn" onclick="shareOn('whatsapp')">
                <i class="fab fa-whatsapp"></i>
            </button>
            <button class="sticky-share-btn" onclick="shareOn('facebook')">
                <i class="fab fa-facebook-f"></i>
            </button>
            <button class="sticky-share-btn" onclick="copyArticleUrl()">
                <i class="fas fa-link"></i>
            </button>
        `;
        
        const style = document.createElement('style');
        style.textContent = `
            .sticky-share-mobile {
                position: fixed;
                bottom: 20px;
                right: 20px;
                display: flex;
                flex-direction: column;
                gap: 10px;
                z-index: 1000;
            }
            
            .sticky-share-btn {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: none;
                color: white;
                background: var(--primary);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: var(--shadow-lg);
            }
            
            .sticky-share-btn:hover {
                transform: scale(1.1);
            }
            
            @media (min-width: 769px) {
                .sticky-share-mobile {
                    display: none;
                }
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(stickyShare);
    }
}

// Share functions
const articleUrl = window.location.href;
const articleTitle = '<?= safe_html($article['title']) ?>';

function shareOn(platform) {
    const urls = {
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(articleUrl)}`,
        twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(articleTitle)}&url=${encodeURIComponent(articleUrl)}`,
        whatsapp: `https://wa.me/?text=${encodeURIComponent(articleTitle + ' ' + articleUrl)}`
    };
    
    if (urls[platform]) {
        window.open(urls[platform], '_blank', 'width=600,height=400');
    }
    
    // Track share event
    if (typeof gtag !== 'undefined') {
        gtag('event', 'share', {
            'method': platform,
            'content_type': 'article',
            'item_id': '<?= $article['slug'] ?>'
        });
    }
}

function copyArticleUrl() {
    navigator.clipboard.writeText(articleUrl).then(() => {
        showToast('¡Enlace copiado al portapapeles!', 'success');
    }).catch(err => {
        console.error('Error copying to clipboard:', err);
        showToast('Error al copiar el enlace', 'error');
    });
    
    // Track copy event
    if (typeof gtag !== 'undefined') {
        gtag('event', 'share', {
            'method': 'copy_link',
            'content_type': 'article',
            'item_id': '<?= $article['slug'] ?>'
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    
    const style = document.createElement('style');
    style.textContent = `
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 500;
            z-index: 10000;
            animation: slideUp 0.3s ease;
        }
        
        .toast-success {
            background: var(--green);
        }
        
        .toast-error {
            background: var(--red);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
    `;
    
    if (!document.querySelector('style[data-toast]')) {
        style.setAttribute('data-toast', 'true');
        document.head.appendChild(style);
    }
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Newsletter subscription
function subscribeNewsletter(event) {
    event.preventDefault();
    
    const form = event.target;
    const email = form.querySelector('input[type="email"]').value;
    const btn = form.querySelector('button');
    
    // Simulate subscription (implement with your backend)
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Suscrito!';
        form.reset();
        showToast('¡Te has suscrito exitosamente!', 'success');
        
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Suscribirse';
            btn.disabled = false;
        }, 2000);
    }, 1000);
    
    // Track subscription
    if (typeof gtag !== 'undefined') {
        gtag('event', 'newsletter_signup', {
            'event_category': 'engagement',
            'event_label': 'article_page'
        });
    }
}

// Track article view
if (typeof gtag !== 'undefined') {
    gtag('event', 'article_view', {
        'event_category': 'engagement',
        'event_label': '<?= $article['slug'] ?>',
        'value': 1
    });
}
</script>

<?php include '../templates/layout/footer.php'; ?>