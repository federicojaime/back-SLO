<?php
// public/index.php - Router Principal del Frontend
session_start();

// Configuración
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../api/client.php';

// Obtener la ruta actual
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace(SITE_BASE_PATH, '', $path);
$path = trim($path, '/');

// Parámetros de URL
$segments = explode('/', $path);
$page = $segments[0] ?: 'home';
$param1 = $segments[1] ?? null;
$param2 = $segments[2] ?? null;

// Variables globales para templates
$site_title = 'San Luis Opina';
$meta_description = 'Portal de noticias de San Luis, Argentina';
$current_page = $page;

// Router principal
switch ($page) {
    case '':
    case 'home':
    case 'inicio':
        // Página principal
        $featured_article = get_featured_article();
        $latest_articles = get_latest_articles(12);
        $categories = get_categories();

        $page_title = 'Inicio - ' . $site_title;
        include '../templates/pages/home.php';
        break;

    case 'categoria':
    case 'category':
        // Vista por categoría: /categoria/politica
        if (!$param1) {
            redirect_to('/');
        }

        $category_slug = $param1;
        $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $category = get_category_by_slug($category_slug);
        if (!$category) {
            show_404();
            break;
        }

        $articles = get_articles_by_category($category_slug, $page_num);
        $pagination = get_pagination_data($articles['total'], $page_num, ARTICLES_PER_PAGE);

        $page_title = $category['name'] . ' - ' . $site_title;
        $meta_description = 'Noticias de ' . $category['name'] . ' en San Luis';
        include '../templates/pages/category.php';
        break;

    case 'articulo':
    case 'noticia':
        // Vista individual: /articulo/titulo-del-articulo
        if (!$param1) {
            redirect_to('/');
        }

        $article_slug = $param1;
        $article = get_article_by_slug($article_slug);

        if (!$article) {
            show_404();
            break;
        }

        // Incrementar vistas
        increment_article_views($article['id']);

        // Artículos relacionados
        $related_articles = get_related_articles($article['id'], $article['category_id'], 4);

        $page_title = $article['title'] . ' - ' . $site_title;
        $meta_description = $article['excerpt'] ?: substr(strip_tags($article['content']), 0, 160);
        include '../templates/pages/article.php';
        break;

    case 'buscar':
    case 'search':
        // Búsqueda: /buscar?q=termino
        $search_term = $_GET['q'] ?? '';
        $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        if (strlen($search_term) < 3) {
            $articles = ['data' => [], 'total' => 0];
            $error_message = 'Ingresa al menos 3 caracteres para buscar';
        } else {
            $articles = search_articles($search_term, $page_num);
        }

        $pagination = get_pagination_data($articles['total'], $page_num, ARTICLES_PER_PAGE);

        $page_title = 'Buscar: ' . htmlspecialchars($search_term) . ' - ' . $site_title;
        include '../templates/pages/search.php';
        break;

    case 'api':
        // API endpoints para AJAX
        handle_api_request($param1, $param2);
        break;

    case 'rss':
        // Feed RSS
        generate_rss_feed();
        break;

    default:
        show_404();
        break;
}

function show_404()
{
    http_response_code(404);
    $page_title = 'Página no encontrada - San Luis Opina';
    include '../templates/pages/404.php';
}

function handle_api_request($endpoint, $param)
{
    header('Content-Type: application/json');

    switch ($endpoint) {
        case 'search':
            $term = $_GET['q'] ?? '';
            $results = search_articles($term, 1, 5);
            echo json_encode($results);
            break;

        case 'load-more':
            $category = $_GET['category'] ?? '';
            $page = (int)($_GET['page'] ?? 1);
            $articles = $category ? get_articles_by_category($category, $page) : get_latest_articles(12, $page);
            echo json_encode($articles);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
    }
    exit;
}

function generate_rss_feed()
{
    header('Content-Type: application/rss+xml; charset=utf-8');

    $articles = get_latest_articles(20);

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<rss version="2.0">';
    echo '<channel>';
    echo '<title>San Luis Opina</title>';
    echo '<description>Portal de noticias de San Luis, Argentina</description>';
    echo '<link>' . SITE_URL . '</link>';

    foreach ($articles['data'] as $article) {
        echo '<item>';
        echo '<title>' . htmlspecialchars($article['title']) . '</title>';
        echo '<description>' . htmlspecialchars($article['excerpt']) . '</description>';
        echo '<link>' . SITE_URL . '/articulo/' . $article['slug'] . '</link>';
        echo '<pubDate>' . date('r', strtotime($article['published_at'])) . '</pubDate>';
        echo '</item>';
    }

    echo '</channel>';
    echo '</rss>';
    exit;
}
