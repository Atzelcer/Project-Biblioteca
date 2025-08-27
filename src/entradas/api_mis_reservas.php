<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['idUsuario'])) {
  echo json_encode(['ok'=>false,'msg'=>'no autorizado']); exit;
}

$db = BaseDatos::conexion();
$action = $_GET['action'] ?? '';

if ($action === 'ultima') {
  $sql = "SELECT 
            r.id_reserva AS id_reserva,
            r.fichaReserva,
            r.estado,
            r.fechaReserva,
            r.fechaDevolucion,
            r.fechaDevolucionReal,
            l.nombre AS libro,
            l.autor   AS autor
          FROM reservas r
          JOIN libros l ON l.id_libro = r.id_libro
          WHERE r.id_usuario = :u
            AND r.estado IN ('pendiente','autorizada')
          ORDER BY r.id_reserva DESC
          LIMIT 1";
  $st = $db->prepare($sql);
  $st->execute([':u'=>$_SESSION['idUsuario']]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    echo json_encode(['ok'=>true, 'data'=>$row]);
  } else {
    echo json_encode(['ok'=>true, 'data'=>null]);
  }
  exit;
}

echo json_encode(['ok'=>false,'msg'=>'accion no valida']);
