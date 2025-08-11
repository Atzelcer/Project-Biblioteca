<?php
chdir(dirname(__DIR__));
require_once 'src/config/conexion.php';
require_once 'src/controllers/UserController.php';

$controller = new UserController($conexion);
$controller->listarUsuarios();
?>
