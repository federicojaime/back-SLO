<?php
// templates/components/article-card.php - Tarjeta reutilizable para artículos
// Variables esperadas: $article, $index (opcional)

$imageUrl = get_image_url($article['featured_image']);
$categoryColor = $article['category_color'] ?? '#6b7280';
$articleUrl = SITE_URL . '/articulo/' . $article['slug'];
$timeAgo = time_ago($article['published_at'] ?: $article['created_at']);
$excerpt = smart_excerpt($article['excerpt'] ?: $article['content'], 120);
$animationDelay = isset($index) ? ($index * 0.1) : 0;
?>

<article class="news-card"
    style="animation-delay: <?= $animationDelay ?>s"
    data-article-id="<?= $article['id'] ?>"
    onclick="navigateToArticle('<?= $articleUrl ?>')">

    <!-- Featured Image -->
    <div class="news-card-image-container">
        <img src="<?= $imageUrl ?>"
            alt="<?= safe_html($article['title']) ?>"
            class="news-card-image"
            loading="lazy"
            onerror="this.src='<?= SITE_URL . DEFAULT_IMAGE ?>'">

        <!-- Category Badge -->
        <?php if ($article['category_name']): ?>
            <span class="news-category" style="background-color: <?= $categoryColor ?>">
                <?= safe_html($article['category_name']) ?>
            </span>
        <?php endif; ?>

        <!-- Reading Time Estimate -->
        <div class="reading-time">
            <i class="fas fa-clock"></i>
            <?= max(1, ceil(str_word_count($article['content'] ?? '') / 200)) ?> min
        </div>
    </div>

    <!-- Card Content -->
    <div class="news-card-content">
        <!-- Title -->
        <h3 class="news-title">
            <a href="<?= $articleUrl ?>" class="article-link">
                <?= safe_html($article['title']) ?>
            </a>
        </h3>

        <!-- Excerpt -->
        <?php if (!empty($excerpt)): ?>
            <p class="news-excerpt"><?= safe_html($excerpt) ?></p>
        <?php endif; ?>

        <!-- Meta Information -->
        <div class="news-meta">
            <div class="meta-left">
                <span class="news-date">
                    <i class="fas fa-calendar-alt"></i>
                    <?= $timeAgo ?>
                </span>

                <?php if (isset($article['author_name'])): ?>
                    <span class="news-author">
                        <i class="fas fa-user"></i>
                        <?= safe_html($article['author_name']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="meta-right">
                <?php if (isset($article['views']) && $article['views'] > 0): ?>
                    <span class="news-views">
                        <i class="fas fa-eye"></i>
                        <?= format_number($article['views']) ?>
                    </span>
                <?php endif; ?>

                <!-- Share Button -->
                <button class="share-btn"
                    onclick="shareArticle(event, '<?= $article['id'] ?>', '<?= safe_html($article['title']) ?>', '<?= $articleUrl ?>')"
                    aria-label="Compartir artículo">
                    <i class="fas fa-share-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Hover Overlay -->
    <div class="card-hover-overlay">
        <div class="hover-content">
            <i class="fas fa-arrow-right"></i>
            <span>Leer más</span>
        </div>
    </div>
</article>

<style>
    /* Estilos específicos para las tarjetas de artículos */
    .news-card {
        background: var(--white);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        transition: all 0.4s ease;
        height: 100%;
        border: 1px solid var(--gray-200);
        cursor: pointer;
        position: relative;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.6s ease forwards;
    }

    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-2xl);
        border-color: var(--primary);
    }

    .news-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-accent);
        transform: scaleX(0);
        transition: transform 0.3s ease;
        z-index: 2;
    }

    .news-card:hover::before {
        transform: scaleX(1);
    }

    .news-card-image-container {
        position: relative;
        overflow: hidden;
        height: 250px;
    }

    .news-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .news-card:hover .news-card-image {
        transform: scale(1.05);
    }

    .news-category {
        position: absolute;
        top: 15px;
        left: 15px;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .reading-time {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 500;
        backdrop-filter: blur(10px);
    }

    .news-card-content {
        padding: 25px;
        display: flex;
        flex-direction: column;
        height: calc(100% - 250px);
    }

    .news-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--gray-900);
        margin-bottom: 12px;
        line-height: 1.3;
        letter-spacing: -0.01em;
    }

    .article-link {
        color: inherit;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .article-link:hover {
        color: var(--primary);
    }

    .news-excerpt {
        color: var(--gray-600);
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .news-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        color: var(--gray-500);
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid var(--gray-200);
    }

    .meta-left {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .meta-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .news-date,
    .news-author,
    .news-views {
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
    }

    .share-btn {
        background: var(--gray-100);
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

    .card-hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(99, 102, 241, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .news-card:hover .card-hover-overlay {
        opacity: 1;
    }

    .hover-content {
        color: white;
        text-align: center;
        font-weight: 600;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .hover-content i {
        font-size: 24px;
        animation: bounce 1s infinite;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .news-card-content {
            padding: 20px;
        }

        .news-title {
            font-size: 1.1rem;
        }

        .news-card-image-container {
            height: 200px;
        }

        .news-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .meta-right {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 576px) {
        .news-card-content {
            padding: 15px;
        }

        .news-card-image-container {
            height: 180px;
        }

        .reading-time {
            display: none;
        }
    }
</style>

<script>
    // Navigation function
    function navigateToArticle(url) {
        // Add some analytics tracking if needed
        if (typeof gtag !== 'undefined') {
            gtag('event', 'article_click', {
                'event_category': 'engagement',
                'event_label': url
            });
        }

        window.location.href = url;
    }

    // Share function
    function shareArticle(event, articleId, title, url) {
        event.stopPropagation(); // Prevent card click

        if (navigator.share) {
            navigator.share({
                title: title,
                text: `Interesante artículo: ${title}`,
                url: url
            }).then(() => {
                console.log('Shared successfully');
            }).catch((error) => {
                console.log('Error sharing:', error);
                fallbackShare(title, url);
            });
        } else {
            fallbackShare(title, url);
        }
    }

    // Fallback share function
    function fallbackShare(title, url) {
        const shareMenu = document.createElement('div');
        shareMenu.className = 'share-menu';
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
                <button onclick="copyToClipboard('${url}')" class="share-option copy">
                    <i class="fas fa-link"></i> Copiar enlace
                </button>
            </div>
            <button onclick="closeShareMenu()" class="close-share">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

        document.body.appendChild(shareMenu);

        // Add styles
        const style = document.createElement('style');
        style.textContent = `
        .share-menu {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        }
        
        .share-menu-content {
            background: white;
            padding: 20px;
            border-radius: 12px;
            max-width: 300px;
            width: 90%;
            position: relative;
            animation: slideUp 0.3s ease;
        }
        
        .share-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }
        
        .share-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .share-option.facebook { background: #1877f2; }
        .share-option.twitter { background: #1da1f2; }
        .share-option.whatsapp { background: #25d366; }
        .share-option.copy { background: #6b7280; }
        
        .share-option:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        
        .close-share {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: #6b7280;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
        document.head.appendChild(style);

        // Close on outside click
        shareMenu.addEventListener('click', function(e) {
            if (e.target === shareMenu) {
                closeShareMenu();
            }
        });
    }

    function closeShareMenu() {
        const shareMenu = document.querySelector('.share-menu');
        if (shareMenu) {
            shareMenu.remove();
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'copy-toast';
            toast.textContent = '¡Enlace copiado!';
            toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 500;
            z-index: 10001;
            animation: slideUp 0.3s ease;
        `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
                closeShareMenu();
            }, 2000);
        }).catch(err => {
            console.error('Error copying to clipboard:', err);
            alert('Error al copiar el enlace');
        });
    }
</script>