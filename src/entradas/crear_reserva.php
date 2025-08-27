<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/libroRepositorio.php';
require_once __DIR__ . '/../repositorios/reservaRepositorio.php';
require_once __DIR__ . '/../servicios/reservaServicio.php';

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['ok'=>false,'msg'=>'usa POST a esta URL']); exit;
}

if(!isset($_SESSION['idUsuario'])){
  echo json_encode(['ok'=>false,'msg'=>'sesion no valida']); exit;
}

if(($_SESSION['rol'] ?? '') !== 'lector'){
  echo json_encode(['ok'=>false,'msg'=>'solo lectores pueden reservar']); exit;
}

$idUsuario = (int)$_SESSION['idUsuario'];
$idLibro   = (int)($_POST['id_libro'] ?? 0);
if($idLibro <= 0){
  echo json_encode(['ok'=>false,'msg'=>'libro invalido']); exit;
}

$db = BaseDatos::conexion();
$libRepo = new LibroRepositorio($db);
$resRepo = new ReservaRepositorio($db);
$svc = new ReservaServicio($libRepo, $resRepo);

echo json_encode($svc->reservar($idUsuario, $idLibro));
