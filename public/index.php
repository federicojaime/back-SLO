<?php
session_start();

// Configuración de base de datos
$db_config = [
    'host' => 'localhost',
    'dbname' => 'san_luis_opina',
    'user' => 'root',
    'pass' => '',
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

// Router simple
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/back-SLO/public', '', $path);
$method = $_SERVER['REQUEST_METHOD'];

// Middleware de autenticación
function requireAuth()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /back-SLO/public/login');
        exit;
    }
}

// Rutas
switch ($path) {
    case '/':
    case '':
        if (isset($_SESSION['user_id'])) {
            header('Location: /back-SLO/public/dashboard');
        } else {
            header('Location: /back-SLO/public/login');
        }
        break;

    case '/login':
        if ($method === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $stmt = $pdo->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                header('Location: /back-SLO/public/dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Credenciales inválidas';
            }
        }
        include '../templates/login.php';
        break;

    case '/logout':
        session_destroy();
        header('Location: /back-SLO/public/login');
        break;

    case '/dashboard':
        requireAuth();
        // Estadísticas
        $stats = [];
        $stats['articles'] = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $stats['published'] = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
        $stats['categories'] = $pdo->query("SELECT COUNT(*) FROM categories WHERE status = 'active'")->fetchColumn();
        $stats['users'] = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();

        // Artículos recientes
        $stmt = $pdo->query("
            SELECT a.id, a.title, a.status, a.created_at, u.full_name as author, c.name as category
            FROM articles a 
            LEFT JOIN users u ON a.author_id = u.id 
            LEFT JOIN categories c ON a.category_id = c.id 
            ORDER BY a.created_at DESC 
            LIMIT 5
        ");
        $recent_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include '../templates/dashboard.php';
        break;

    case '/articles':
        requireAuth();
        $page = intval($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $stmt = $pdo->prepare("
        SELECT a.*, u.full_name as author, c.name as category_name
        FROM articles a 
        LEFT JOIN users u ON a.author_id = u.id 
        LEFT JOIN categories c ON a.category_id = c.id 
        ORDER BY a.created_at DESC 
        LIMIT :limit OFFSET :offset
    ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $total_pages = ceil($total / $limit);

        include '../templates/articles/index.php';
        break;

    case '/articles/create':
        requireAuth();
        if ($method === 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $excerpt = $_POST['excerpt'];
            $category_id = $_POST['category_id'] ?: null;
            $status = $_POST['status'];

            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($title)));
            $published_at = $status === 'published' ? date('Y-m-d H:i:s') : null;

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
            header('Location: /back-SLO/public/articles');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        include '../templates/articles/create.php';
        break;

    case '/categories':
        requireAuth();
        if ($method === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? '';
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($name)));

            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $slug, $description]);

            $_SESSION['success'] = 'Categoría creada exitosamente';
            header('Location: /back-SLO/public/categories');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        include '../templates/categories/index.php';
        break;

    case '/users':
        requireAuth();
        // Solo admins pueden gestionar usuarios
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            header('Location: /back-SLO/public/dashboard');
            exit;
        }

        if ($method === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $full_name = $_POST['full_name'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];

            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, full_name, password, role) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$username, $email, $full_name, $password, $role]);

            $_SESSION['success'] = 'Usuario creado exitosamente';
            header('Location: /back-SLO/public/users');
            exit;
        }

        $users = $pdo->query("
            SELECT id, username, email, full_name, role, status, created_at 
            FROM users 
            ORDER BY created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        include '../templates/users/index.php';
        break;

    case '/settings':
        requireAuth();
        // Solo admins pueden cambiar configuraciones
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            header('Location: /back-SLO/public/dashboard');
            exit;
        }

        if ($method === 'POST') {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'config_') === 0) {
                    $config_key = str_replace('config_', '', $key);
                    $stmt = $pdo->prepare("
                        INSERT INTO site_config (config_key, config_value) 
                        VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE config_value = ?
                    ");
                    $stmt->execute([$config_key, $value, $value]);
                }
            }
            $_SESSION['success'] = 'Configuración actualizada';
            header('Location: /back-SLO/public/settings');
            exit;
        }

        $settings = $pdo->query("SELECT * FROM site_config ORDER BY config_key")->fetchAll(PDO::FETCH_ASSOC);
        include '../templates/settings/index.php';
        break;

    case '/profile':
        requireAuth();
        if ($method === 'POST') {
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $bio = $_POST['bio'] ?? '';

            $query = "UPDATE users SET full_name = ?, email = ?, bio = ?";
            $params = [$full_name, $email, $bio];

            // Solo actualizar contraseña si se proporcionó
            if (!empty($_POST['password'])) {
                $query .= ", password = ?";
                $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $query .= " WHERE id = ?";
            $params[] = $_SESSION['user_id'];

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            // Actualizar sesión
            $_SESSION['full_name'] = $full_name;

            $_SESSION['success'] = 'Perfil actualizado exitosamente';
            header('Location: /back-SLO/public/profile');
            exit;
        }

        $user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $user->execute([$_SESSION['user_id']]);
        $user_data = $user->fetch(PDO::FETCH_ASSOC);

        include '../templates/profile/index.php';
        break;

    case '/api/upload-image':
        requireAuth();
        if ($method === 'POST' && isset($_FILES['image'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newname = uniqid() . '.' . $ext;
                $path = __DIR__ . '/uploads/' . $newname;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
                    echo json_encode(['success' => true, 'url' => '/back-SLO/public/uploads/' . $newname]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al subir archivo']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Formato no permitido']);
            }
        }
        break;

    default:
        http_response_code(404);
        if (isset($_SESSION['user_id'])) {
            include '../templates/404.php';
        } else {
            echo '404 - Página no encontrada';
        }
        break;
}
