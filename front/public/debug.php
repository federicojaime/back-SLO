<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico del Sistema</h1>";

// Verificar archivos requeridos
$files_to_check = [
    '../includes/config.php',
    '../includes/functions.php',
    '../api/client.php'
];

echo "<h2>1. Verificación de archivos:</h2>";
foreach ($files_to_check as $file) {
    $full_path = realpath($file);
    if (file_exists($file)) {
        echo "✅ {$file} → Existe en: {$full_path}<br>";
        
        // Verificar permisos
        if (is_readable($file)) {
            echo "&nbsp;&nbsp;&nbsp;✅ Legible<br>";
        } else {
            echo "&nbsp;&nbsp;&nbsp;❌ No legible<br>";
        }
    } else {
        echo "❌ {$file} → No existe<br>";
    }
}

echo "<h2>2. Información del servidor:</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "Current Directory: " . getcwd() . "<br>";

echo "<h2>3. Variables de entorno:</h2>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'No definida') . "<br>";

echo "<h2>4. Intentar incluir config.php:</h2>";
try {
    if (file_exists('../includes/config.php')) {
        echo "Intentando incluir config.php...<br>";
        include_once '../includes/config.php';
        echo "✅ config.php incluido exitosamente<br>";
        
        // Verificar constantes definidas
        $constants = ['SITE_BASE_PATH', 'SITE_URL', 'ARTICLES_PER_PAGE'];
        foreach ($constants as $const) {
            if (defined($const)) {
                echo "✅ Constante {$const}: " . constant($const) . "<br>";
            } else {
                echo "❌ Constante {$const}: No definida<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Error al incluir config.php: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Intentar incluir functions.php:</h2>";
try {
    if (file_exists('../includes/functions.php')) {
        echo "Intentando incluir functions.php...<br>";
        include_once '../includes/functions.php';
        echo "✅ functions.php incluido exitosamente<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al incluir functions.php: " . $e->getMessage() . "<br>";
}

echo "<h2>6. Test de conexión a base de datos:</h2>";
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        echo "✅ Conexión a base de datos exitosa<br>";
    } catch (PDOException $e) {
        echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Constantes de base de datos no definidas<br>";
}
?>