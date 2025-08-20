<?php
// includes/config.php - Configuración del Frontend

// ==========================================
// CONFIGURACIÓN PRINCIPAL
// ==========================================

define('SITE_URL', 'https://sanluisopina.com');
define('SITE_BASE_PATH', '/');
define('API_BASE_URL', 'http://localhost/back-SLO/public/api');

// ==========================================
// CONFIGURACIÓN DE PAGINACIÓN
// ==========================================

define('ARTICLES_PER_PAGE', 12);
define('LATEST_ARTICLES_SIDEBAR', 5);
define('RELATED_ARTICLES_COUNT', 4);

// ==========================================
// CONFIGURACIÓN DE CACHE
// ==========================================

define('CACHE_ENABLED', true);
define('CACHE_DURATION', 300); // 5 minutos

// ==========================================
// CONFIGURACIÓN DE SEO
// ==========================================

define('SITE_NAME', 'San Luis Opina');
define('SITE_DESCRIPTION', 'Portal de noticias líder de San Luis, Argentina');
define('SITE_KEYWORDS', 'San Luis, noticias, Argentina, portal, radio FM 97.1');

// ==========================================
// CONFIGURACIÓN DE REDES SOCIALES
// ==========================================

define('FACEBOOK_URL', 'https://facebook.com/SanLuisOpina');
define('TWITTER_URL', 'https://twitter.com/SanLuisOpinaCom');
define('INSTAGRAM_URL', 'https://instagram.com/sanluisopina');
define('YOUTUBE_URL', 'https://youtube.com/c/SanLuisOpina');
define('WHATSAPP_NUMBER', '+5492664207943');

// ==========================================
// CONFIGURACIÓN DE RADIO
// ==========================================

define('RADIO_STREAM_URL', 'https://stream.sanluisopina.com:8000/radio');
define('RADIO_FREQUENCY', 'FM 97.1');
define('RADIO_PHONE', '(2664) 207-943');

// ==========================================
// CONFIGURACIÓN DE IMÁGENES
// ==========================================

define('DEFAULT_IMAGE', '/assets/images/default-news.jpg');
define('LOGO_URL', '/assets/images/logo.png');
define('OG_IMAGE', '/assets/images/og-image.jpg');

// ==========================================
// CONFIGURACIÓN DE CONTACTO
// ==========================================

define('CONTACT_EMAIL', 'contacto@sanluisopina.com');
define('CONTACT_PHONE', '(2664) 207-943');
define('CONTACT_ADDRESS', 'Av. Lafinur 1234, San Luis Capital, CP 5700');

// ==========================================
// TIMEZONE
// ==========================================

date_default_timezone_set('America/Argentina/Buenos_Aires');

// ==========================================
// CONFIGURACIÓN DE ERROR REPORTING
// ==========================================

if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ==========================================
// CONFIGURACIÓN DE SESIÓN
// ==========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// CATEGORÍAS PREDEFINIDAS (para menú)
// ==========================================

$GLOBALS['main_categories'] = [
    'politica' => [
        'name' => 'Política',
        'icon' => 'fas fa-landmark',
        'color' => '#dc2626'
    ],
    'deportes' => [
        'name' => 'Deportes',
        'icon' => 'fas fa-futbol',
        'color' => '#059669'
    ],
    'economia' => [
        'name' => 'Economía',
        'icon' => 'fas fa-chart-line',
        'color' => '#d97706'
    ],
    'cultura' => [
        'name' => 'Cultura',
        'icon' => 'fas fa-theater-masks',
        'color' => '#7c3aed'
    ],
    'sociedad' => [
        'name' => 'Sociedad',
        'icon' => 'fas fa-users',
        'color' => '#0891b2'
    ]
];

// ==========================================
// FUNCIONES DE UTILIDAD
// ==========================================

function get_env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

function is_production() {
    return get_env('ENVIRONMENT') === 'production';
}

function get_cache_key($key) {
    return 'slo_' . md5($key);
}

function set_cache($key, $data, $duration = null) {
    if (!CACHE_ENABLED) return false;
    
    $duration = $duration ?: CACHE_DURATION;
    $cache_key = get_cache_key($key);
    $cache_data = [
        'data' => $data,
        'expires' => time() + $duration
    ];
    
    return file_put_contents(
        sys_get_temp_dir() . '/' . $cache_key,
        serialize($cache_data)
    );
}

function get_cache($key) {
    if (!CACHE_ENABLED) return false;
    
    $cache_key = get_cache_key($key);
    $cache_file = sys_get_temp_dir() . '/' . $cache_key;
    
    if (!file_exists($cache_file)) return false;
    
    $cache_data = unserialize(file_get_contents($cache_file));
    
    if (time() > $cache_data['expires']) {
        unlink($cache_file);
        return false;
    }
    
    return $cache_data['data'];
}

function clear_cache() {
    $temp_dir = sys_get_temp_dir();
    $cache_files = glob($temp_dir . '/slo_*');
    
    foreach ($cache_files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

// ==========================================
// AUTO-CONFIGURACIÓN SEGÚN ENTORNO
// ==========================================

// Detectar si estamos en desarrollo
if (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
    strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false) {
    define('ENVIRONMENT', 'development');
    
    // URLs para desarrollo
    if (!defined('SITE_URL')) {
        define('SITE_URL', 'http://localhost/san-luis-frontend/public');
    }
    if (!defined('API_BASE_URL')) {
        define('API_BASE_URL', 'http://localhost/back-SLO/public/api');
    }
} else {
    define('ENVIRONMENT', 'production');
}