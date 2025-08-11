<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de usuario no válido";
    exit;
}

chdir(dirname(__DIR__));
require_once 'src/config/conexion.php';
require_once 'src/controllers/UserController.php';

$controller = new UserController($conexion);
$controller->verUsuario($_GET['id']);
?>
