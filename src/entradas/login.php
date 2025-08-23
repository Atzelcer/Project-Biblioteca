<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/usuarioRepositorio.php';
require_once __DIR__ . '/../servicios/autenticacionServicio.php';
require_once __DIR__ . '/../controladores/autenticacionControlador.php';

$db   = BaseDatos::conexion();
$repo = new UsuarioRepositorio($db);
$svc  = new AutenticacionServicio($repo);
$ctl  = new AutenticacionControlador($svc);

if($_SERVER['REQUEST_METHOD']==='POST'){
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = trim($_POST['clave'] ?? '');
    $ctl->login($usuario,$clave);
} else {
    $ctl->formulario();
}
