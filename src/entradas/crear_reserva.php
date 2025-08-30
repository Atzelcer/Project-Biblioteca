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

$res = $svc->reservar($idUsuario, $idLibro); // <-- asegúrate que incluya id_reserva

// Si por cualquier razón el servicio no trae id_reserva, intenta recuperarlo
if (!empty($res['ok']) && empty($res['id_reserva']) && !empty($res['ficha'])) {
  // Buscar la reserva recién creada por ficha y usuario
  $st = $db->prepare("SELECT id_reserva FROM reservas WHERE id_usuario=? AND fichaReserva=? ORDER BY id_reserva DESC LIMIT 1");
  $st->execute([$idUsuario, $res['ficha']]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if ($row) $res['id_reserva'] = (int)$row['id_reserva'];
}

echo json_encode($res);
