<?php
session_start();

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
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
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
        // Expone $vars como variables locales en el template (ej: ['stats'=>…] -> $stats)
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

        // Valores por defecto para evitar warnings si la DB está vacía
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

            $slug         = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($title)));
            $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

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
            $slug        = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($name)));

            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, color) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $color]);

            $_SESSION['success'] = 'Categoría creada exitosamente';
            header('Location: ' . BASE_URL . '/categories');
            exit;
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

            $stmt = $pdo->prepare("
            INSERT INTO users (username, email, full_name, password, role)
            VALUES (?, ?, ?, ?, ?)
        ");
            $stmt->execute([$username, $email, $full_name, $password, $role]);

            $_SESSION['success'] = 'Usuario creado exitosamente';
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        // ⬇️ Traemos last_login y, de yapa, el conteo de artículos por usuario
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


    case '/settings':
        requireAuth();

        if (($_SESSION['role'] ?? '') !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($method === 'POST') {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'config_') === 0) {
                    $config_key = str_replace('config_', '', $key);
                    $stmt = $pdo->prepare("
                        INSERT INTO site_config (config_key, config_value)
                        VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)
                    ");
                    $stmt->execute([$config_key, $value]);
                }
            }
            $_SESSION['success'] = 'Configuración actualizada';
            header('Location: ' . BASE_URL . '/settings');
            exit;
        }

        $settings = $pdo->query("SELECT * FROM site_config ORDER BY config_key")->fetchAll(PDO::FETCH_ASSOC);

        includeTemplate('settings/index.php', [
            'settings' => $settings,
        ]);
        break;

    case '/profile':
        requireAuth();

        if ($method === 'POST') {
            $full_name = $_POST['full_name'] ?? '';
            $email     = $_POST['email']     ?? '';
            $bio       = $_POST['bio']       ?? '';

            $query  = "UPDATE users SET full_name = ?, email = ?, bio = ?";
            $params = [$full_name, $email, $bio];

            if (!empty($_POST['password'])) {
                $query   .= ", password = ?";
                $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $query   .= " WHERE id = ?";
            $params[] = $_SESSION['user_id'];

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            $_SESSION['full_name'] = $full_name;

            $_SESSION['success'] = 'Perfil actualizado exitosamente';
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }

        $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $userStmt->execute([$_SESSION['user_id']]);
        $user_data = $userStmt->fetch(PDO::FETCH_ASSOC);

        includeTemplate('profile/index.php', [
            'user_data' => $user_data,
        ]);
        break;

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

    default:
        /*===============================
        =   Rutas dinámicas / 404       =
        ===============================*/

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

                $slug         = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($title)));
                $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

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

        // /categories/edit/{id}
        if (preg_match('#^/categories/edit/(\d+)$#', $path, $m)) {
            requireAuth();
            $category_id = (int)$m[1];

            if ($method === 'POST') {
                $name        = $_POST['name']        ?? '';
                $description = $_POST['description'] ?? '';
                $status      = $_POST['status']      ?? 'active';
                $color       = $_POST['color']       ?? '#6c757d';
                $slug        = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($name)));

                $stmt = $pdo->prepare("
                    UPDATE categories SET 
                        name = ?, slug = ?, description = ?, status = ?, color = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$name, $slug, $description, $status, $color, $category_id]);

                $_SESSION['success'] = 'Categoría actualizada exitosamente';
                header('Location: ' . BASE_URL . '/categories');
                exit;
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

        // /categories/delete/{id}
        if (preg_match('#^/categories/delete/(\d+)$#', $path, $m)) {
            requireAuth();
            $category_id = (int)$m[1];

            $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($category) {
                // Verificar si hay artículos usando esta categoría
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

        // /categories/toggle/{id}
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

        // 404
        http_response_code(404);
        if (isset($_SESSION['user_id'])) {
            includeTemplate('404.php');
        } else {
            echo '404 - Página no encontrada';
        }
        break;
}
