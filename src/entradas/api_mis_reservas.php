<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['idUsuario'])) { echo json_encode(['ok'=>false]); exit; }

$db = BaseDatos::conexion();
$act = $_GET['action'] ?? 'ultima';

if ($act === 'ultima') {
  $st = $db->prepare(
    "SELECT r.fichaReserva, r.estado, r.fechaReserva, r.fechaDevolucion,
            l.nombre AS libro, l.autor AS autor
     FROM reservas r
     JOIN libros l ON l.id_libro=r.id_libro
     WHERE r.id_usuario=? AND r.estado IN ('pendiente','autorizada')
     ORDER BY r.id_reserva DESC LIMIT 1"
  );
  $st->execute([$_SESSION['idUsuario']]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if ($row) { echo json_encode(['ok'=>true,'has'=>true,'data'=>$row]); }
  else { echo json_encode(['ok'=>true,'has'=>false]); }
  exit;
}

echo json_encode(['ok'=>false]);
