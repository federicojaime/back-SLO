<?php
// includes/functions.php - Funciones Helper del Frontend

// ==========================================
// FUNCIONES DE UTILIDAD GENERAL
// ==========================================

/**
 * Redirecciona a una URL
 */
function redirect_to($url, $permanent = false)
{
    $status_code = $permanent ? 301 : 302;

    if (strpos($url, 'http') !== 0) {
        $url = SITE_URL . $url;
    }

    header("Location: $url", true, $status_code);
    exit;
}

/**
 * Sanitiza texto para mostrar en HTML
 */
function safe_html($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Trunca texto a un número específico de palabras
 */
function truncate_words($text, $words = 30, $suffix = '...')
{
    $text = strip_tags($text);
    $word_array = explode(' ', $text);

    if (count($word_array) <= $words) {
        return $text;
    }

    return implode(' ', array_slice($word_array, 0, $words)) . $suffix;
}

/**
 * Convierte fecha a formato legible en español
 */
function format_date($date, $format = 'full')
{
    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '';
    }

    switch ($format) {
        case 'short':
            return date('d/m/Y', $timestamp);
        case 'time':
            return date('H:i', $timestamp);
        case 'datetime':
            return date('d/m/Y H:i', $timestamp);
        case 'full':
        default:
            $months = [
                1 => 'enero',
                2 => 'febrero',
                3 => 'marzo',
                4 => 'abril',
                5 => 'mayo',
                6 => 'junio',
                7 => 'julio',
                8 => 'agosto',
                9 => 'septiembre',
                10 => 'octubre',
                11 => 'noviembre',
                12 => 'diciembre'
            ];

            $day = date('j', $timestamp);
            $month = $months[(int)date('n', $timestamp)];
            $year = date('Y', $timestamp);
            $time = date('H:i', $timestamp);

            return "$day de $month de $year, $time hs";
    }
}

/**
 * Calcula tiempo transcurrido (ej: "hace 2 horas")
 */
function time_ago($date)
{
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'hace unos segundos';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "hace $minutes " . ($minutes == 1 ? 'minuto' : 'minutos');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "hace $hours " . ($hours == 1 ? 'hora' : 'horas');
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return "hace $days " . ($days == 1 ? 'día' : 'días');
    } else {
        return format_date($date, 'short');
    }
}

/**
 * Genera URL de imagen con fallback
 */
function get_image_url($image, $default = null)
{
    if (empty($image)) {
        return $default ?: (SITE_URL . DEFAULT_IMAGE);
    }

    // Si ya es una URL completa, devolverla
    if (strpos($image, 'http') === 0) {
        return $image;
    }

    // Si es una ruta relativa, agregar el dominio
    return SITE_URL . '/' . ltrim($image, '/');
}

/**
 * Formatea números grandes (ej: 1.2K, 1.5M)
 */
function format_number($number)
{
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }

    return number_format($number);
}

// ==========================================
// FUNCIONES DE SEO Y META TAGS
// ==========================================

/**
 * Genera meta tags para SEO
 */
function generate_meta_tags($title = null, $description = null, $image = null, $url = null)
{
    global $page_title, $meta_description;

    $title = $title ?: $page_title ?: SITE_NAME;
    $description = $description ?: $meta_description ?: SITE_DESCRIPTION;
    $image = get_image_url($image, SITE_URL . OG_IMAGE);
    $url = $url ?: SITE_URL . $_SERVER['REQUEST_URI'];

    $meta_tags = [
        // Básicos
        "<title>$title</title>",
        "<meta name=\"description\" content=\"$description\">",
        "<meta name=\"keywords\" content=\"" . SITE_KEYWORDS . "\">",

        // Open Graph (Facebook)
        "<meta property=\"og:title\" content=\"$title\">",
        "<meta property=\"og:description\" content=\"$description\">",
        "<meta property=\"og:image\" content=\"$image\">",
        "<meta property=\"og:url\" content=\"$url\">",
        "<meta property=\"og:type\" content=\"article\">",
        "<meta property=\"og:site_name\" content=\"" . SITE_NAME . "\">",

        // Twitter Cards
        "<meta name=\"twitter:card\" content=\"summary_large_image\">",
        "<meta name=\"twitter:title\" content=\"$title\">",
        "<meta name=\"twitter:description\" content=\"$description\">",
        "<meta name=\"twitter:image\" content=\"$image\">",

        // Robots
        "<meta name=\"robots\" content=\"index, follow\">",
        "<link rel=\"canonical\" href=\"$url\">"
    ];

    return implode("\n    ", $meta_tags);
}

/**
 * Genera structured data (JSON-LD) para artículos
 */
function get_article_structured_data($article)
{
    $data = [
        "@context" => "https://schema.org",
        "@type" => "NewsArticle",
        "headline" => $article['title'],
        "description" => $article['excerpt'],
        "image" => get_image_url($article['featured_image']),
        "datePublished" => date('c', strtotime($article['published_at'])),
        "dateModified" => date('c', strtotime($article['updated_at'])),
        "author" => [
            "@type" => "Person",
            "name" => $article['author_name']
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => SITE_NAME,
            "logo" => [
                "@type" => "ImageObject",
                "url" => SITE_URL . LOGO_URL
            ]
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => SITE_URL . "/articulo/" . $article['slug']
        ]
    ];

    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES) . '</script>';
}

// ==========================================
// FUNCIONES DE NAVEGACIÓN Y MENÚS
// ==========================================

/**
 * Genera breadcrumbs
 */
function get_breadcrumbs($items = [])
{
    $breadcrumbs = [
        ['title' => 'Inicio', 'url' => '/']
    ];

    $breadcrumbs = array_merge($breadcrumbs, $items);

    $html = '<nav aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb">';

    foreach ($breadcrumbs as $index => $item) {
        $is_last = ($index === count($breadcrumbs) - 1);

        if ($is_last) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . safe_html($item['title']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . safe_html($item['title']) . '</a></li>';
        }
    }

    $html .= '</ol>';
    $html .= '</nav>';

    return $html;
}

/**
 * Genera menú de navegación principal
 */
function get_main_navigation($current_page = '')
{
    global $main_categories;

    $nav_items = [
        'inicio' => ['title' => 'Inicio', 'url' => '/', 'icon' => 'fas fa-home']
    ];

    // Agregar categorías principales
    foreach ($main_categories as $slug => $category) {
        $nav_items[$slug] = [
            'title' => $category['name'],
            'url' => "/categoria/$slug",
            'icon' => $category['icon']
        ];
    }

    $html = '<ul class="nav-main">';

    foreach ($nav_items as $key => $item) {
        $active_class = ($current_page === $key) ? ' active' : '';
        $html .= '<li class="nav-item">';
        $html .= '<a href="' . $item['url'] . '" class="nav-link' . $active_class . '">';

        if (isset($item['icon'])) {
            $html .= '<i class="' . $item['icon'] . '"></i> ';
        }

        $html .= $item['title'];
        $html .= '</a>';
        $html .= '</li>';
    }

    $html .= '</ul>';

    return $html;
}

/**
 * Genera paginación
 */
function render_pagination($pagination, $base_url = '')
{
    if ($pagination['total_pages'] <= 1) {
        return '';
    }

    $current = $pagination['current_page'];
    $total = $pagination['total_pages'];

    $html = '<nav class="pagination-nav" aria-label="Paginación de artículos">';
    $html .= '<ul class="pagination">';

    // Botón anterior
    if ($pagination['has_prev']) {
        $prev_url = $base_url . '?page=' . $pagination['prev_page'];
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $prev_url . '" aria-label="Página anterior">';
        $html .= '<i class="fas fa-chevron-left"></i> Anterior';
        $html .= '</a>';
        $html .= '</li>';
    }

    // Números de página
    $start = max(1, $current - 2);
    $end = min($total, $current + 2);

    // Primera página si no está en el rango
    if ($start > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $base_url . '?page=1">1</a>';
        $html .= '</li>';

        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Páginas del rango
    for ($i = $start; $i <= $end; $i++) {
        $active_class = ($i === $current) ? ' active' : '';
        $page_url = $base_url . '?page=' . $i;

        $html .= '<li class="page-item' . $active_class . '">';

        if ($i === $current) {
            $html .= '<span class="page-link" aria-current="page">' . $i . '</span>';
        } else {
            $html .= '<a class="page-link" href="' . $page_url . '">' . $i . '</a>';
        }

        $html .= '</li>';
    }

    // Última página si no está en el rango
    if ($end < $total) {
        if ($end < $total - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $base_url . '?page=' . $total . '">' . $total . '</a>';
        $html .= '</li>';
    }

    // Botón siguiente
    if ($pagination['has_next']) {
        $next_url = $base_url . '?page=' . $pagination['next_page'];
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $next_url . '" aria-label="Página siguiente">';
        $html .= 'Siguiente <i class="fas fa-chevron-right"></i>';
        $html .= '</a>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    $html .= '</nav>';

    return $html;
}

// ==========================================
// FUNCIONES DE CONTENIDO
// ==========================================

/**
 * Genera extracto inteligente de contenido HTML
 */
function smart_excerpt($content, $length = 160)
{
    // Remover etiquetas HTML
    $text = strip_tags($content);

    // Decodificar entidades HTML
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    // Normalizar espacios
    $text = preg_replace('/\s+/', ' ', trim($text));

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    // Truncar en el último espacio antes del límite
    $truncated = mb_substr($text, 0, $length);
    $last_space = mb_strrpos($truncated, ' ');

    if ($last_space !== false) {
        $truncated = mb_substr($truncated, 0, $last_space);
    }

    return $truncated . '...';
}

/**
 * Resalta términos de búsqueda en el texto
 */
function highlight_search_terms($text, $search_term)
{
    if (empty($search_term)) {
        return $text;
    }

    $terms = explode(' ', $search_term);

    foreach ($terms as $term) {
        $term = trim($term);
        if (strlen($term) >= 3) {
            $text = preg_replace(
                '/(' . preg_quote($term, '/') . ')/i',
                '<mark>$1</mark>',
                $text
            );
        }
    }

    return $text;
}

/**
 * Obtiene el primer párrafo de un artículo
 */
function get_first_paragraph($content)
{
    // Buscar el primer párrafo con contenido
    preg_match('/<p[^>]*>(.*?)<\/p>/s', $content, $matches);

    if (isset($matches[1])) {
        return trim(strip_tags($matches[1]));
    }

    // Si no hay párrafos, tomar los primeros 200 caracteres
    return smart_excerpt($content, 200);
}

// ==========================================
// FUNCIONES DE VALIDACIÓN
// ==========================================

/**
 * Valida email
 */
function is_valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida slug de URL
 */
function is_valid_slug($slug)
{
    return preg_match('/^[a-z0-9\-]+$/', $slug);
}

/**
 * Sanitiza parámetros de búsqueda
 */
function sanitize_search_term($term)
{
    // Remover caracteres especiales peligrosos
    $term = preg_replace('/[<>"\']/', '', $term);

    // Normalizar espacios
    $term = preg_replace('/\s+/', ' ', trim($term));

    return $term;
}

// ==========================================
// FUNCIONES DE DEBUGGING (solo desarrollo)
// ==========================================

/**
 * Debug dump (solo en desarrollo)
 */
function debug_dump($var, $label = '')
{
    if (ENVIRONMENT !== 'development') {
        return;
    }

    echo '<div style="background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; padding: 16px; margin: 16px 0; font-family: monospace; font-size: 14px;">';

    if ($label) {
        echo '<strong style="color: #374151;">' . $label . ':</strong><br>';
    }

    echo '<pre style="margin: 8px 0 0 0; white-space: pre-wrap; word-wrap: break-word;">';
    print_r($var);
    echo '</pre>';
    echo '</div>';
}

/**
 * Log de errores personalizado
 */
function log_error($message, $context = [])
{
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context,
        'url' => $_SERVER['REQUEST_URI'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];

    error_log('SLO Frontend: ' . json_encode($log_entry));
}
