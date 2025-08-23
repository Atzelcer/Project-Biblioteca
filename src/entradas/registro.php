<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/usuarioRepositorio.php';
require_once __DIR__ . '/../servicios/registroServicio.php';
require_once __DIR__ . '/../controladores/registroControlador.php';

$db   = BaseDatos::conexion();
$repo = new UsuarioRepositorio($db);
$svc  = new RegistroServicio($repo);
$ctl  = new RegistroControlador($svc);

if($_SERVER['REQUEST_METHOD']==='POST'){
    $ctl->guardar($_POST, $_FILES);
} else {
    $ctl->formulario();
}
