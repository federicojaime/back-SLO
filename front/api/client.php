<?php
// api/client.php - Cliente para consumir tu backend existente

class ApiClient {
    private $base_url;
    private $timeout;
    
    public function __construct($base_url = null, $timeout = 10) {
        $this->base_url = $base_url ?: API_BASE_URL;
        $this->timeout = $timeout;
    }
    
    /**
     * Realiza una petición GET a la API
     */
    private function request($endpoint, $params = []) {
        $cache_key = $endpoint . '_' . serialize($params);
        
        // Intentar obtener desde cache
        if ($cached = get_cache($cache_key)) {
            return $cached;
        }
        
        $url = $this->base_url . '/' . ltrim($endpoint, '/');
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $context = stream_context_create([
            'http' => [
                'timeout' => $this->timeout,
                'header' => [
                    'User-Agent: San Luis Opina Frontend/1.0',
                    'Accept: application/json'
                ]
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            error_log("API Error: Failed to fetch $url");
            return ['success' => false, 'data' => [], 'error' => 'Error de conexión'];
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("API Error: Invalid JSON from $url");
            return ['success' => false, 'data' => [], 'error' => 'Respuesta inválida'];
        }
        
        // Guardar en cache si la respuesta es exitosa
        if (isset($data['success']) && $data['success']) {
            set_cache($cache_key, $data, CACHE_DURATION);
        }
        
        return $data;
    }
    
    /**
     * Obtiene artículos destacados
     */
    public function getFeaturedArticle() {
        return $this->request('featured');
    }
    
    /**
     * Obtiene artículos más recientes
     */
    public function getLatestArticles($limit = 12, $page = 1) {
        return $this->request('latest', [
            'limit' => $limit,
            'page' => $page
        ]);
    }
    
    /**
     * Obtiene noticias por categoría
     */
    public function getNewsByCategory($category = 'all', $page = 1, $limit = 12) {
        return $this->request('news', [
            'category' => $category,
            'page' => $page,
            'limit' => $limit
        ]);
    }
    
    /**
     * Obtiene todas las categorías
     */
    public function getCategories() {
        return $this->request('categories');
    }
    
    /**
     * Obtiene un artículo por slug
     */
    public function getArticleBySlug($slug) {
        // Como tu backend no tiene este endpoint, simulamos la búsqueda
        $articles = $this->request('news', ['limit' => 100]);
        
        if (!$articles['success']) {
            return $articles;
        }
        
        foreach ($articles['data'] as $article) {
            if ($article['slug'] === $slug) {
                return ['success' => true, 'data' => $article];
            }
        }
        
        return ['success' => false, 'data' => null, 'error' => 'Artículo no encontrado'];
    }
    
    /**
     * Busca artículos
     */
    public function searchArticles($term, $page = 1, $limit = 12) {
        // Implementar búsqueda simulada hasta que tengas endpoint de búsqueda
        $articles = $this->request('news', ['limit' => 100]);
        
        if (!$articles['success']) {
            return $articles;
        }
        
        $filtered = array_filter($articles['data'], function($article) use ($term) {
            return stripos($article['title'], $term) !== false || 
                   stripos($article['excerpt'], $term) !== false;
        });
        
        // Paginar resultados
        $total = count($filtered);
        $offset = ($page - 1) * $limit;
        $paginated = array_slice($filtered, $offset, $limit);
        
        return [
            'success' => true,
            'data' => array_values($paginated),
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }
    
    /**
     * Obtiene estadísticas generales
     */
    public function getStats() {
        return $this->request('stats');
    }
    
    /**
     * Incrementa las vistas de un artículo (simulado)
     */
    public function incrementViews($articleId) {
        // Esto requeriría un endpoint POST en tu backend
        // Por ahora solo registramos el evento
        error_log("View registered for article ID: $articleId");
        return ['success' => true];
    }
}

// Instancia global del cliente API
$api = new ApiClient();

// ==========================================
// FUNCIONES HELPER PARA LOS TEMPLATES
// ==========================================

function get_featured_article() {
    global $api;
    $response = $api->getFeaturedArticle();
    return $response['success'] ? $response['data'] : null;
}

function get_latest_articles($limit = 12, $page = 1) {
    global $api;
    $response = $api->getLatestArticles($limit, $page);
    return $response['success'] ? $response : ['data' => [], 'total' => 0];
}

function get_articles_by_category($category, $page = 1, $limit = 12) {
    global $api;
    $response = $api->getNewsByCategory($category, $page, $limit);
    return $response['success'] ? $response : ['data' => [], 'total' => 0];
}

function get_categories() {
    global $api;
    $response = $api->getCategories();
    return $response['success'] ? $response['data'] : [];
}

function get_category_by_slug($slug) {
    $categories = get_categories();
    foreach ($categories as $category) {
        if ($category['slug'] === $slug) {
            return $category;
        }
    }
    return null;
}

function get_article_by_slug($slug) {
    global $api;
    $response = $api->getArticleBySlug($slug);
    return $response['success'] ? $response['data'] : null;
}

function search_articles($term, $page = 1, $limit = 12) {
    global $api;
    $response = $api->searchArticles($term, $page, $limit);
    return $response['success'] ? $response : ['data' => [], 'total' => 0];
}

function get_related_articles($articleId, $categoryId, $limit = 4) {
    // Obtener artículos de la misma categoría
    $articles = get_latest_articles(20);
    
    $related = array_filter($articles['data'], function($article) use ($articleId, $categoryId) {
        return $article['id'] != $articleId && 
               $article['category_id'] == $categoryId;
    });
    
    return array_slice(array_values($related), 0, $limit);
}

function increment_article_views($articleId) {
    global $api;
    return $api->incrementViews($articleId);
}

function get_pagination_data($total, $current_page, $per_page) {
    $total_pages = ceil($total / $per_page);
    
    return [
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'per_page' => $per_page,
        'total' => $total,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'prev_page' => $current_page - 1,
        'next_page' => $current_page + 1
    ];
}