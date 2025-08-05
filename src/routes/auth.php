<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$controller = new AuthController($conexion);

// Obtener solo la parte de la URI sin el dominio ni parámetros
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remover la parte base del proyecto para obtener la ruta limpia
$basePath = '/Project-Biblioteca/public';
$cleanUri = str_replace($basePath, '', $uri);

// Si la URI está vacía, ponerla como '/'
if (empty($cleanUri)) {
    $cleanUri = '/';
}

switch ($cleanUri) {
    case '/':
        header("Location: " . $basePath . "/login");
        exit;
        break;
    case '/login':
        $controller->login();
        break;
    case '/procesar-login':
        $controller->procesarLogin();
        break;
    case '/register':
        $controller->register();
        break;
    case '/procesar-registro':
        $controller->procesarRegistro();
        break;
    case '/logout':
        $controller->logout();
        break;
    default:
        http_response_code(404);
        echo "Ruta no encontrada: " . $cleanUri;
}
?>
