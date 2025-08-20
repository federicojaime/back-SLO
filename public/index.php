<?php
session_start();

// ========================================
// CONFIGURACIÓN DE ZONA HORARIA
// ========================================
date_default_timezone_set('America/Argentina/Buenos_Aires');

/*========================
=   Configuración DB     =
========================*/
$db_config = [
    'host'    => 'localhost',
    'dbname'  => 'san_luis_opina',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4'
];

try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset={$db_config['charset']}",
        $db_config['user'],
        $db_config['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '-03:00'"
        ]
    );
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

/*========================
=   Constantes de ruta   =
========================*/
define('BASE_URL', '/back-SLO/public');
define('TEMPLATE_PATH', __DIR__ . '/../templates/');

/*========================
=   Funciones helper     =
========================*/
function generateUniqueSlug($pdo, $title, $articleId = null)
{
    $baseSlug = strtolower(trim($title));
    $baseSlug = preg_replace('/[áàäâ]/u', 'a', $baseSlug);
    $baseSlug = preg_replace('/[éèëê]/u', 'e', $baseSlug);
    $baseSlug = preg_replace('/[íìïî]/u', 'i', $baseSlug);
    $baseSlug = preg_replace('/[óòöô]/u', 'o', $baseSlug);
    $baseSlug = preg_replace('/[úùüû]/u', 'u', $baseSlug);
    $baseSlug = preg_replace('/[ñ]/u', 'n', $baseSlug);
    $baseSlug = preg_replace('/[^a-z0-9\s-]/u', '', $baseSlug);
    $baseSlug = preg_replace('/[\s-]+/', '-', $baseSlug);
    $baseSlug = trim($baseSlug, '-');

    if (empty($baseSlug)) {
        $baseSlug = 'articulo-' . date('Y-m-d');
    }

    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $sql = "SELECT id FROM articles WHERE slug = ?";
        $params = [$slug];

        if ($articleId) {
            $sql .= " AND id != ?";
            $params[] = $articleId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if (!$stmt->fetch()) {
            break;
        }

        $slug = $baseSlug . '-' . $counter;
        $counter++;

        if ($counter > 1000) {
            $slug = $baseSlug . '-' . uniqid();
            break;
        }
    }

    return $slug;
}

// Función helper para "tiempo transcurrido"
function timeAgo($datetime)
{
    $time = time() - strtotime($datetime);

    if ($time < 60) return 'Hace unos segundos';
    if ($time < 3600) return 'Hace ' . floor($time / 60) . ' minutos';
    if ($time < 86400) return 'Hace ' . floor($time / 3600) . ' horas';
    if ($time < 2592000) return 'Hace ' . floor($time / 86400) . ' días';
    if ($time < 31536000) return 'Hace ' . floor($time / 2592000) . ' meses';
    return 'Hace ' . floor($time / 31536000) . ' años';
}

/*========================
=   Router básico        =
========================*/
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace(BASE_URL, '', $path);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/*========================
=   Middleware           =
========================*/
function requireAuth()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

/*=========================================================
=   Helper para incluir templates con variables seguras    =
=========================================================*/
function includeTemplate(string $templateRelPath, array $vars = []): void
{
    $fullPath = TEMPLATE_PATH . $templateRelPath;
    if (!file_exists($fullPath)) {
        die("Template not found: $fullPath");
    }
    if (!empty($vars)) {
        extract($vars, EXTR_SKIP);
    }
    include $fullPath;
}

/*========================
=         Rutas          =
========================*/
switch ($path) {
    case '/':
    case '':
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
        } else {
            header('Location: ' . BASE_URL . '/login');
        }
        exit;

    case '/login':
        if ($method === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $stmt = $pdo->prepare("SELECT id, username, password, role, full_name 
                                   FROM users 
                                   WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Credenciales inválidas';
            }
        }
        includeTemplate('login.php');
        break;

    case '/logout':
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;

    case '/dashboard':
        requireAuth();

        $stats = [
            'articles'   => 0,
            'published'  => 0,
            'categories' => 0,
            'users'      => 0,
        ];

        try {
            $stats['articles']   = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
            $stats['published']  = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
            $stats['categories'] = (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE status = 'active'")->fetchColumn();
            $stats['users']      = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
        } catch (Exception $e) {
            error_log('Error en estadísticas: ' . $e->getMessage());
        }

        $recent_articles = [];
        try {
            $stmt = $pdo->query("
                SELECT a.id, a.title, a.status, a.created_at, 
                       u.full_name AS author, 
                       c.name      AS category
                FROM articles a
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN categories c ON a.category_id = c.id
                ORDER BY a.created_at DESC
                LIMIT 5
            ");
            $recent_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error en artículos recientes: ' . $e->getMessage());
        }

        includeTemplate('dashboard.php', [
            'stats'           => $stats,
            'recent_articles' => $recent_articles,
        ]);
        break;

    case '/articles':
        requireAuth();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $stmt = $pdo->prepare("
            SELECT a.*, u.full_name AS author, c.name AS category_name
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total        = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $total_pages  = (int)ceil($total / $limit);

        includeTemplate('articles/index.php', [
            'articles'     => $articles,
            'page'         => $page,
            'total_pages'  => $total_pages,
        ]);
        break;

    case '/articles/create':
        requireAuth();

        if ($method === 'POST') {
            $title       = $_POST['title']       ?? '';
            $content     = $_POST['content']     ?? '';
            $excerpt     = $_POST['excerpt']     ?? '';
            $category_id = $_POST['category_id'] ?: null;
            $status      = $_POST['status']      ?? 'draft';

            $slug = generateUniqueSlug($pdo, $title);
            $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO articles (title, slug, content, excerpt, category_id, author_id, status, published_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $title,
                    $slug,
                    $content,
                    $excerpt,
                    $category_id,
                    $_SESSION['user_id'],
                    $status,
                    $published_at
                ]);

                $_SESSION['success'] = 'Artículo creado exitosamente';
                header('Location: ' . BASE_URL . '/articles');
                exit;
            } catch (PDOException $e) {
                error_log('Error al crear artículo: ' . $e->getMessage());
                $_SESSION['error'] = 'Error al crear el artículo. Por favor intenta nuevamente.';
            }
        }

        $categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);

        includeTemplate('articles/create.php', [
            'categories' => $categories,
        ]);
        break;

    case '/categories':
        requireAuth();

        if ($method === 'POST') {
            $name        = $_POST['name']        ?? '';
            $description = $_POST['description'] ?? '';
            $color       = $_POST['color']       ?? '#6c757d';
            $slug        = generateUniqueSlug($pdo, $name);

            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, color) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description, $color]);

                $_SESSION['success'] = 'Categoría creada exitosamente';
                header('Location: ' . BASE_URL . '/categories');
                exit;
            } catch (PDOException $e) {
                error_log('Error al crear categoría: ' . $e->getMessage());
                $_SESSION['error'] = 'Error al crear la categoría. Por favor intenta nuevamente.';
            }
        }

        $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        includeTemplate('categories/index.php', [
            'categories' => $categories,
        ]);
        break;

    case '/users':
        requireAuth();

        if (($_SESSION['role'] ?? '') !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($method === 'POST') {
            $username  = $_POST['username']  ?? '';
            $email     = $_POST['email']     ?? '';
            $full_name = $_POST['full_name'] ?? '';
            $password  = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
            $role      = $_POST['role']      ?? 'editor';

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, full_name, password, role)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$username, $email, $full_name, $password, $role]);

                $_SESSION['success'] = 'Usuario creado exitosamente';
                header('Location: ' . BASE_URL . '/users');
                exit;
            } catch (PDOException $e) {
                error_log('Error al crear usuario: ' . $e->getMessage());
                $_SESSION['error'] = 'Error al crear el usuario. Puede que el email o nombre de usuario ya existan.';
            }
        }

        $users = $pdo->query("
            SELECT 
                u.id, u.username, u.email, u.full_name, u.role, u.status, 
                u.created_at, u.last_login,
                (SELECT COUNT(*) FROM articles a WHERE a.author_id = u.id) AS article_count
            FROM users u
            ORDER BY u.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        includeTemplate('users/index.php', [
            'users' => $users,
        ]);
        break;

    // ==========================================
    // GESTIÓN DE SPONSORS
    // ==========================================
    case '/sponsors':
        requireAuth();

        if ($method === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $website_url = $_POST['website_url'] ?? '';
            $contact_email = $_POST['contact_email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $priority = (int)($_POST['priority'] ?? 0);
            $status = $_POST['status'] ?? 'active';

            // Manejo de archivo de logo
            $logo_url = '';
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                $filename = $_FILES['logo']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $newname = 'sponsor_' . uniqid() . '.' . $ext;
                    $dir = __DIR__ . '/uploads/sponsors/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);

                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $dir . $newname)) {
                        $logo_url = BASE_URL . '/uploads/sponsors/' . $newname;
                    }
                }
            }

            if ($logo_url) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO sponsors (name, description, logo_url, website_url, contact_email, phone, priority, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$name, $description, $logo_url, $website_url, $contact_email, $phone, $priority, $status]);
                    $_SESSION['success'] = 'Sponsor agregado exitosamente';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Error al crear sponsor: ' . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = 'Por favor sube un logo válido';
            }

            header('Location: ' . BASE_URL . '/sponsors');
            exit;
        }

        $sponsors = $pdo->query("SELECT * FROM sponsors ORDER BY priority ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        includeTemplate('sponsors/index.php', ['sponsors' => $sponsors]);
        break;

    // ==========================================
    // GESTIÓN DE MODALES PROMOCIONALES
    // ==========================================
    case '/promotional-modals':
        requireAuth();

        if ($method === 'POST') {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $button_text = $_POST['button_text'] ?? 'Cerrar';
            $button_url = $_POST['button_url'] ?? '';
            $display_frequency = $_POST['display_frequency'] ?? 'once_per_session';
            $status = $_POST['status'] ?? 'active';
            $position = $_POST['position'] ?? 'center';
            $size = $_POST['size'] ?? 'medium';
            $auto_close_seconds = $_POST['auto_close_seconds'] ? (int)$_POST['auto_close_seconds'] : null;

            // Manejo de imagen
            $image_url = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $newname = 'modal_' . uniqid() . '.' . $ext;
                    $dir = __DIR__ . '/uploads/modals/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $newname)) {
                        $image_url = BASE_URL . '/uploads/modals/' . $newname;
                    }
                }
            }

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO promotional_modals 
                    (title, content, image_url, button_text, button_url, display_frequency, status, position, size, auto_close_seconds) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $content, $image_url, $button_text, $button_url, $display_frequency, $status, $position, $size, $auto_close_seconds]);
                $_SESSION['success'] = 'Modal promocional creado exitosamente';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error al crear modal: ' . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/promotional-modals');
            exit;
        }

        $modals = $pdo->query("SELECT * FROM promotional_modals ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        includeTemplate('promotional-modals/index.php', ['modals' => $modals]);
        break;

    // ==========================================
    // APIS PÚBLICAS COMPLETAS
    // ==========================================

    // API para el frontend público - Lista de noticias
    case '/api/news':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 12)));
        $category = $_GET['category'] ?? '';
        $featured = $_GET['featured'] ?? '';
        $search = $_GET['search'] ?? '';
        $offset = ($page - 1) * $limit;

        $sql = "
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.featured_image, 
                a.featured_image_alt, a.status, a.featured, a.views,
                a.created_at, a.published_at, a.updated_at,
                u.full_name AS author_name,
                c.name AS category_name, c.slug AS category_slug, c.color AS category_color
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published'
        ";

        $params = [];

        if ($category && $category !== 'all') {
            $sql .= " AND c.slug = ?";
            $params[] = $category;
        }

        if ($featured === 'true') {
            $sql .= " AND a.featured = 1";
        }

        if ($search) {
            $sql .= " AND (a.title LIKE ? OR a.content LIKE ? OR a.excerpt LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY a.published_at DESC, a.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $i => $param) {
            $stmt->bindValue($i + 1, $param);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($articles as &$article) {
            $article['published_at_formatted'] = $article['published_at']
                ? date('d/m/Y H:i', strtotime($article['published_at']))
                : date('d/m/Y H:i', strtotime($article['created_at']));

            $article['time_ago'] = timeAgo($article['published_at'] ?: $article['created_at']);

            if (!$article['featured_image']) {
                $article['featured_image'] = "https://via.placeholder.com/400x240/" .
                    substr(md5($article['category_name'] ?: 'general'), 0, 6) . "/ffffff?text=" .
                    urlencode($article['category_name'] ?: 'Noticias');
            }

            $article['url'] = "/articulo/" . $article['slug'];
        }

        echo json_encode([
            'success' => true,
            'data' => $articles,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => count($articles)
            ]
        ]);
        break;

    // API - Artículo individual completo
    case (preg_match('#^/api/article/([^/]+)$#', $path, $matches) ? true : false):
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $slug = $matches[1];

        $stmt = $pdo->prepare("
            SELECT 
                a.id, a.title, a.slug, a.content, a.excerpt, 
                a.featured_image, a.featured_image_alt, a.meta_title, 
                a.meta_description, a.views, a.featured,
                a.published_at, a.created_at, a.updated_at,
                u.full_name AS author_name, u.bio AS author_bio,
                c.name AS category_name, c.slug AS category_slug, 
                c.color AS category_color, c.description AS category_description
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.slug = ? AND a.status = 'published'
        ");
        $stmt->execute([$slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Artículo no encontrado']);
            exit;
        }

        // Incrementar contador de vistas
        $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article['id']]);
        $article['views'] = $article['views'] + 1;

        // Obtener tags del artículo
        $stmt = $pdo->prepare("
            SELECT t.name, t.slug, t.color 
            FROM tags t 
            INNER JOIN article_tags at ON t.id = at.tag_id 
            WHERE at.article_id = ?
        ");
        $stmt->execute([$article['id']]);
        $article['tags'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Artículos relacionados
        $stmt = $pdo->prepare("
            SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.published_at
            FROM articles a
            WHERE a.category_id = ? AND a.id != ? AND a.status = 'published'
            ORDER BY a.published_at DESC
            LIMIT 3
        ");
        $stmt->execute([$article['category_id'] ?? 0, $article['id']]);
        $article['related_articles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formatear fechas
        $article['published_at_formatted'] = $article['published_at']
            ? date('d/m/Y H:i', strtotime($article['published_at']))
            : date('d/m/Y H:i', strtotime($article['created_at']));
        $article['time_ago'] = timeAgo($article['published_at'] ?: $article['created_at']);
        $article['url'] = "/articulo/" . $article['slug'];

        if (!$article['featured_image']) {
            $article['featured_image'] = "https://via.placeholder.com/800x400/" .
                substr(md5($article['category_name'] ?: 'general'), 0, 6) . "/ffffff?text=" .
                urlencode($article['title']);
        }

        echo json_encode([
            'success' => true,
            'data' => $article
        ]);
        break;

    // API - Búsqueda de artículos
    case '/api/search':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $query = $_GET['q'] ?? '';
        $limit = min(20, max(1, (int)($_GET['limit'] ?? 10)));

        if (empty($query)) {
            echo json_encode(['success' => false, 'error' => 'Query parameter required']);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.featured_image,
                a.published_at, a.created_at,
                c.name AS category_name, c.color AS category_color,
                MATCH(a.title, a.content, a.excerpt) AGAINST(? IN BOOLEAN MODE) AS relevance
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published' 
            AND MATCH(a.title, a.content, a.excerpt) AGAINST(? IN BOOLEAN MODE)
            ORDER BY relevance DESC, a.published_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(1, $query);
        $stmt->bindValue(2, $query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$result) {
            $result['time_ago'] = timeAgo($result['published_at'] ?: $result['created_at']);
            $result['url'] = "/articulo/" . $result['slug'];
        }

        echo json_encode([
            'success' => true,
            'data' => $results,
            'query' => $query,
            'total' => count($results)
        ]);
        break;

    // API - Categorías
    case '/api/categories':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $stmt = $pdo->query("
            SELECT c.*, COUNT(a.id) as article_count
            FROM categories c
            LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
            WHERE c.status = 'active'
            GROUP BY c.id
            ORDER BY c.sort_order ASC, c.name ASC
        ");

        echo json_encode([
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // API - Artículo destacado
    case '/api/featured':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $stmt = $pdo->prepare("
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.featured_image, 
                a.featured_image_alt, a.views, a.published_at, a.created_at,
                u.full_name AS author_name,
                c.name AS category_name, c.slug AS category_slug, c.color AS category_color
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published' AND a.featured = 1
            ORDER BY a.published_at DESC, a.created_at DESC
            LIMIT 1
        ");
        $stmt->execute();

        $featured = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($featured) {
            $featured['published_at_formatted'] = $featured['published_at']
                ? date('d/m/Y H:i', strtotime($featured['published_at']))
                : date('d/m/Y H:i', strtotime($featured['created_at']));

            $featured['time_ago'] = timeAgo($featured['published_at'] ?: $featured['created_at']);
            $featured['url'] = "/articulo/" . $featured['slug'];

            if (!$featured['featured_image']) {
                $featured['featured_image'] = "https://via.placeholder.com/800x400/" .
                    substr(md5($featured['category_name'] ?: 'general'), 0, 6) . "/ffffff?text=" .
                    urlencode($featured['category_name'] ?: 'Noticia+Destacada');
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $featured
        ]);
        break;

    // API - Últimas noticias
    case '/api/latest':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $limit = min(10, max(1, (int)($_GET['limit'] ?? 5)));

        $stmt = $pdo->prepare("
            SELECT 
                a.id, a.title, a.slug, a.excerpt, a.featured_image,
                a.published_at, a.created_at,
                c.name AS category_name, c.color AS category_color
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published'
            ORDER BY a.published_at DESC, a.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($articles as &$article) {
            $article['time_ago'] = timeAgo($article['published_at'] ?: $article['created_at']);
            $article['url'] = "/articulo/" . $article['slug'];

            if (!$article['featured_image']) {
                $article['featured_image'] = "https://via.placeholder.com/80x80/" .
                    substr(md5($article['category_name'] ?: 'general'), 0, 6) . "/ffffff?text=" .
                    substr($article['title'], 0, 1);
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $articles
        ]);
        break;

    // API - Estadísticas generales
    case '/api/stats':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $stats = [
            'total_articles' => (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn(),
            'total_categories' => (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE status = 'active'")->fetchColumn(),
            'total_views' => (int)$pdo->query("SELECT SUM(views) FROM articles WHERE status = 'published'")->fetchColumn(),
            'articles_today' => (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published' AND DATE(published_at) = CURDATE()")->fetchColumn()
        ];

        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
        break;

    // API - Sponsors activos
    case '/api/sponsors':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $stmt = $pdo->query("
            SELECT id, name, description, logo_url, website_url, priority
            FROM sponsors 
            WHERE status = 'active' 
            ORDER BY priority ASC, name ASC
        ");

        echo json_encode([
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // API - Modal promocional activo
    case '/api/promotional-modal':
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $stmt = $pdo->prepare("
            SELECT * FROM promotional_modals 
            WHERE status = 'active' 
            AND (start_date IS NULL OR start_date <= NOW())
            AND (end_date IS NULL OR end_date >= NOW())
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute();

        $modal = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $modal
        ]);
        break;

    // API - Newsletter subscription
    case '/api/newsletter/subscribe':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $input = json_decode(file_get_contents('php://input'), true);
        $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $name = trim($input['name'] ?? '');

        if (!$email) {
            echo json_encode(['success' => false, 'error' => 'Email inválido']);
            exit;
        }

        try {
            $verification_token = bin2hex(random_bytes(32));

            $stmt = $pdo->prepare("
                INSERT INTO newsletter_subscribers (email, name, verification_token) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                name = VALUES(name), 
                status = 'active',
                subscribed_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$email, $name, $verification_token]);

            echo json_encode([
                'success' => true,
                'message' => 'Suscripción exitosa'
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Error al procesar suscripción'
            ]);
        }
        break;

    // API - Subir imagen desde editor
    case '/api/upload-image':
        requireAuth();

        if ($method === 'POST' && isset($_FILES['image'])) {
            $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'] ?? '';
            $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed, true)) {
                $newname = uniqid('', true) . '.' . $ext;
                $dir     = __DIR__ . '/uploads/';
                $path    = $dir . $newname;

                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
                    echo json_encode(['success' => true, 'url' => BASE_URL . '/uploads/' . $newname]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al subir archivo']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Formato no permitido']);
            }
        }
        break;

    // API - Verificar disponibilidad de slug
    case '/api/check-slug':
        requireAuth();
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $slug = $input['slug'] ?? '';
            $articleId = $input['articleId'] ?? null;

            $sql = "SELECT id FROM articles WHERE slug = ?";
            $params = [$slug];

            if ($articleId) {
                $sql .= " AND id != ?";
                $params[] = $articleId;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $exists = $stmt->fetch() ? true : false;

            echo json_encode(['available' => !$exists]);
        }
        break;

    default:
        // /articles/edit/{id}
        if (preg_match('#^/articles/edit/(\d+)$#', $path, $m)) {
            requireAuth();
            $article_id = (int)$m[1];

            if ($method === 'POST') {
                $title       = $_POST['title']       ?? '';
                $content     = $_POST['content']     ?? '';
                $excerpt     = $_POST['excerpt']     ?? '';
                $category_id = $_POST['category_id'] ?: null;
                $status      = $_POST['status']      ?? 'draft';

                $slug = generateUniqueSlug($pdo, $title, $article_id);
                $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

                try {
                    $stmt = $pdo->prepare("
                        UPDATE articles SET 
                            title = ?, slug = ?, content = ?, excerpt = ?, 
                            category_id = ?, status = ?, published_at = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $title,
                        $slug,
                        $content,
                        $excerpt,
                        $category_id,
                        $status,
                        $published_at,
                        $article_id
                    ]);

                    $_SESSION['success'] = 'Artículo actualizado exitosamente';
                    header('Location: ' . BASE_URL . '/articles');
                    exit;
                } catch (PDOException $e) {
                    error_log('Error al actualizar artículo: ' . $e->getMessage());
                    $_SESSION['error'] = 'Error al actualizar el artículo. Por favor intenta nuevamente.';
                }
            }

            $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
            $stmt->execute([$article_id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$article) {
                $_SESSION['error'] = 'Artículo no encontrado';
                header('Location: ' . BASE_URL . '/articles');
                exit;
            }

            $categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name")
                ->fetchAll(PDO::FETCH_ASSOC);

            includeTemplate('articles/edit.php', [
                'article'    => $article,
                'categories' => $categories,
            ]);
            break;
        }

        // /articles/view/{id}
        if (preg_match('#^/articles/view/(\d+)$#', $path, $m)) {
            requireAuth();
            $article_id = (int)$m[1];

            $stmt = $pdo->prepare("
                SELECT a.*, u.full_name AS author, u.email AS author_email, c.name AS category_name
                FROM articles a
                LEFT JOIN users u ON a.author_id = u.id
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE a.id = ?
            ");
            $stmt->execute([$article_id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$article) {
                $_SESSION['error'] = 'Artículo no encontrado';
                header('Location: ' . BASE_URL . '/articles');
                exit;
            }

            $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")
                ->execute([$article_id]);

            includeTemplate('articles/view.php', [
                'article' => $article,
            ]);
            break;
        }

        // /articles/delete/{id}
        if (preg_match('#^/articles/delete/(\d+)$#', $path, $m)) {
            requireAuth();
            $article_id = (int)$m[1];

            $stmt = $pdo->prepare("SELECT id FROM articles WHERE id = ?");
            $stmt->execute([$article_id]);

            if ($stmt->fetch()) {
                $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$article_id]);
                $_SESSION['success'] = 'Artículo eliminado exitosamente';
            } else {
                $_SESSION['error'] = 'Artículo no encontrado';
            }

            header('Location: ' . BASE_URL . '/articles');
            exit;
        }

        // Rutas de cambio de estado de artículos
        if (preg_match('#^/articles/publish/(\d+)$#', $path, $m)) {
            requireAuth();
            $article_id = (int)$m[1];

            try {
                $stmt = $pdo->prepare("
                    UPDATE articles 
                    SET status = 'published', published_at = NOW(), updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$article_id]);

                $_SESSION['success'] = 'Artículo publicado exitosamente';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error al publicar el artículo';
            }

            header('Location: ' . BASE_URL . '/articles');
            exit;
        }

        if (preg_match('#^/articles/draft/(\d+)$#', $path, $m)) {
            requireAuth();
            $article_id = (int)$m[1];

            try {
                $stmt = $pdo->prepare("
                    UPDATE articles 
                    SET status = 'draft', published_at = NULL, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$article_id]);

                $_SESSION['success'] = 'Artículo movido a borrador exitosamente';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error al mover el artículo a borrador';
            }

            header('Location: ' . BASE_URL . '/articles');
            exit;
        }

        if (preg_match('#^/articles/archive/(\d+)$#', $path, $m)) {
            requireAuth();
            $article_id = (int)$m[1];

            try {
                $stmt = $pdo->prepare("
                    UPDATE articles 
                    SET status = 'archived', updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$article_id]);

                $_SESSION['success'] = 'Artículo archivado exitosamente';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error al archivar el artículo';
            }

            header('Location: ' . BASE_URL . '/articles');
            exit;
        }

        if (preg_match('#^/articles/feature/(\d+)$#', $path, $m)) {
            requireAuth();
            $article_id = (int)$m[1];

            try {
                $stmt = $pdo->prepare("
                    UPDATE articles 
                    SET featured = NOT featured, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$article_id]);

                $stmt = $pdo->prepare("SELECT featured FROM articles WHERE id = ?");
                $stmt->execute([$article_id]);
                $featured = $stmt->fetchColumn();

                $_SESSION['success'] = $featured ? 'Artículo destacado' : 'Artículo quitado de destacados';
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error al cambiar el estado de destacado';
            }

            header('Location: ' . BASE_URL . '/articles');
            exit;
        }

        // Rutas de categorías
        if (preg_match('#^/categories/edit/(\d+)$#', $path, $m)) {
            requireAuth();
            $category_id = (int)$m[1];

            if ($method === 'POST') {
                $name        = $_POST['name']        ?? '';
                $description = $_POST['description'] ?? '';
                $status      = $_POST['status']      ?? 'active';
                $color       = $_POST['color']       ?? '#6c757d';
                $slug        = generateUniqueSlug($pdo, $name);

                try {
                    $stmt = $pdo->prepare("
                        UPDATE categories SET 
                            name = ?, slug = ?, description = ?, status = ?, color = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$name, $slug, $description, $status, $color, $category_id]);

                    $_SESSION['success'] = 'Categoría actualizada exitosamente';
                    header('Location: ' . BASE_URL . '/categories');
                    exit;
                } catch (PDOException $e) {
                    error_log('Error al actualizar categoría: ' . $e->getMessage());
                    $_SESSION['error'] = 'Error al actualizar la categoría. Por favor intenta nuevamente.';
                }
            }

            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$category) {
                $_SESSION['error'] = 'Categoría no encontrada';
                header('Location: ' . BASE_URL . '/categories');
                exit;
            }

            includeTemplate('categories/edit.php', [
                'category' => $category,
            ]);
            break;
        }

        if (preg_match('#^/categories/delete/(\d+)$#', $path, $m)) {
            requireAuth();
            $category_id = (int)$m[1];

            $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($category) {
                $articles_count = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
                $articles_count->execute([$category_id]);
                $count = $articles_count->fetchColumn();

                if ($count > 0) {
                    $_SESSION['error'] = "No se puede eliminar la categoría '{$category['name']}' porque tiene {$count} artículo(s) asociado(s).";
                } else {
                    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$category_id]);
                    $_SESSION['success'] = "Categoría '{$category['name']}' eliminada exitosamente";
                }
            } else {
                $_SESSION['error'] = 'Categoría no encontrada';
            }

            header('Location: ' . BASE_URL . '/categories');
            exit;
        }

        if (preg_match('#^/categories/toggle/(\d+)$#', $path, $m)) {
            requireAuth();
            $category_id = (int)$m[1];

            $stmt = $pdo->prepare("
                UPDATE categories 
                SET status = CASE 
                    WHEN status = 'active' THEN 'inactive' 
                    ELSE 'active' 
                END 
                WHERE id = ?
            ");
            $stmt->execute([$category_id]);

            $_SESSION['success'] = 'Estado de categoría actualizado';
            header('Location: ' . BASE_URL . '/categories');
            exit;
        }

        // ==========================================
        // RUTAS ADICIONALES PARA SPONSORS Y MODALES
        // ==========================================

        // /sponsors/edit/{id}
        if (preg_match('#^/sponsors/edit/(\d+)$#', $path, $m)) {
            requireAuth();
            $sponsor_id = (int)$m[1];

            if ($method === 'POST') {
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $website_url = $_POST['website_url'] ?? '';
                $contact_email = $_POST['contact_email'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $priority = (int)($_POST['priority'] ?? 0);
                $status = $_POST['status'] ?? 'active';

                // Manejo de nuevo logo si se sube
                $logo_url = null;
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                    $filename = $_FILES['logo']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $newname = 'sponsor_' . uniqid() . '.' . $ext;
                        $dir = __DIR__ . '/uploads/sponsors/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);

                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $dir . $newname)) {
                            $logo_url = BASE_URL . '/uploads/sponsors/' . $newname;
                        }
                    }
                }

                try {
                    if ($logo_url) {
                        $stmt = $pdo->prepare("
                            UPDATE sponsors SET 
                                name = ?, description = ?, logo_url = ?, website_url = ?, 
                                contact_email = ?, phone = ?, priority = ?, status = ?, updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$name, $description, $logo_url, $website_url, $contact_email, $phone, $priority, $status, $sponsor_id]);
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE sponsors SET 
                                name = ?, description = ?, website_url = ?, 
                                contact_email = ?, phone = ?, priority = ?, status = ?, updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$name, $description, $website_url, $contact_email, $phone, $priority, $status, $sponsor_id]);
                    }
                    $_SESSION['success'] = 'Sponsor actualizado exitosamente';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Error al actualizar sponsor: ' . $e->getMessage();
                }

                header('Location: ' . BASE_URL . '/sponsors');
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
            $stmt->execute([$sponsor_id]);
            $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sponsor) {
                $_SESSION['error'] = 'Sponsor no encontrado';
                header('Location: ' . BASE_URL . '/sponsors');
                exit;
            }

            includeTemplate('sponsors/edit.php', ['sponsor' => $sponsor]);
            break;
        }

        // /sponsors/delete/{id}
        if (preg_match('#^/sponsors/delete/(\d+)$#', $path, $m)) {
            requireAuth();
            $sponsor_id = (int)$m[1];

            $stmt = $pdo->prepare("SELECT id, name FROM sponsors WHERE id = ?");
            $stmt->execute([$sponsor_id]);
            $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($sponsor) {
                $pdo->prepare("DELETE FROM sponsors WHERE id = ?")->execute([$sponsor_id]);
                $_SESSION['success'] = "Sponsor '{$sponsor['name']}' eliminado exitosamente";
            } else {
                $_SESSION['error'] = 'Sponsor no encontrado';
            }

            header('Location: ' . BASE_URL . '/sponsors');
            exit;
        }

        // /sponsors/toggle/{id}
        if (preg_match('#^/sponsors/toggle/(\d+)$#', $path, $m)) {
            requireAuth();
            $sponsor_id = (int)$m[1];

            $stmt = $pdo->prepare("
                UPDATE sponsors 
                SET status = CASE 
                    WHEN status = 'active' THEN 'inactive' 
                    ELSE 'active' 
                END 
                WHERE id = ?
            ");
            $stmt->execute([$sponsor_id]);

            $_SESSION['success'] = 'Estado de sponsor actualizado';
            header('Location: ' . BASE_URL . '/sponsors');
            exit;
        }

        // /promotional-modals/edit/{id}
        if (preg_match('#^/promotional-modals/edit/(\d+)$#', $path, $m)) {
            requireAuth();
            $modal_id = (int)$m[1];

            if ($method === 'POST') {
                $title = $_POST['title'] ?? '';
                $content = $_POST['content'] ?? '';
                $button_text = $_POST['button_text'] ?? 'Cerrar';
                $button_url = $_POST['button_url'] ?? '';
                $display_frequency = $_POST['display_frequency'] ?? 'once_per_session';
                $status = $_POST['status'] ?? 'active';
                $position = $_POST['position'] ?? 'center';
                $size = $_POST['size'] ?? 'medium';
                $auto_close_seconds = $_POST['auto_close_seconds'] ? (int)$_POST['auto_close_seconds'] : null;

                // Manejo de nueva imagen si se sube
                $image_url = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['image']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $newname = 'modal_' . uniqid() . '.' . $ext;
                        $dir = __DIR__ . '/uploads/modals/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $newname)) {
                            $image_url = BASE_URL . '/uploads/modals/' . $newname;
                        }
                    }
                }

                try {
                    if ($image_url) {
                        $stmt = $pdo->prepare("
                            UPDATE promotional_modals SET 
                                title = ?, content = ?, image_url = ?, button_text = ?, button_url = ?, 
                                display_frequency = ?, status = ?, position = ?, size = ?, auto_close_seconds = ?, updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$title, $content, $image_url, $button_text, $button_url, $display_frequency, $status, $position, $size, $auto_close_seconds, $modal_id]);
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE promotional_modals SET 
                                title = ?, content = ?, button_text = ?, button_url = ?, 
                                display_frequency = ?, status = ?, position = ?, size = ?, auto_close_seconds = ?, updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$title, $content, $button_text, $button_url, $display_frequency, $status, $position, $size, $auto_close_seconds, $modal_id]);
                    }
                    $_SESSION['success'] = 'Modal promocional actualizado exitosamente';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Error al actualizar modal: ' . $e->getMessage();
                }

                header('Location: ' . BASE_URL . '/promotional-modals');
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM promotional_modals WHERE id = ?");
            $stmt->execute([$modal_id]);
            $modal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$modal) {
                $_SESSION['error'] = 'Modal no encontrado';
                header('Location: ' . BASE_URL . '/promotional-modals');
                exit;
            }

            includeTemplate('promotional-modals/edit.php', ['modal' => $modal]);
            break;
        }

        // /promotional-modals/delete/{id}
        if (preg_match('#^/promotional-modals/delete/(\d+)$#', $path, $m)) {
            requireAuth();
            $modal_id = (int)$m[1];

            $stmt = $pdo->prepare("SELECT id, title FROM promotional_modals WHERE id = ?");
            $stmt->execute([$modal_id]);
            $modal = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($modal) {
                $pdo->prepare("DELETE FROM promotional_modals WHERE id = ?")->execute([$modal_id]);
                $_SESSION['success'] = "Modal '{$modal['title']}' eliminado exitosamente";
            } else {
                $_SESSION['error'] = 'Modal no encontrado';
            }

            header('Location: ' . BASE_URL . '/promotional-modals');
            exit;
        }

        // /promotional-modals/toggle/{id}
        if (preg_match('#^/promotional-modals/toggle/(\d+)$#', $path, $m)) {
            requireAuth();
            $modal_id = (int)$m[1];

            $stmt = $pdo->prepare("
                UPDATE promotional_modals 
                SET status = CASE 
                    WHEN status = 'active' THEN 'inactive' 
                    ELSE 'active' 
                END 
                WHERE id = ?
            ");
            $stmt->execute([$modal_id]);

            $_SESSION['success'] = 'Estado de modal actualizado';
            header('Location: ' . BASE_URL . '/promotional-modals');
            exit;
        }

        // 404
        http_response_code(404);
        if (isset($_SESSION['user_id'])) {
            includeTemplate('404.php');
        } else {
            echo '404 - Página no encontrada';
        }
        break;
}
