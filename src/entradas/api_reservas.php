<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';

session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['idUsuario']) || ($_SESSION['rol'] ?? '') !== 'bibliotecario') {
  echo json_encode(['ok'=>false]); exit;
}
$db = BaseDatos::conexion();
$act = $_GET['action'] ?? $_POST['action'] ?? 'list';

if ($act === 'list') {
  $estado = $_GET['estado'] ?? 'pendiente';
  $q = trim($_GET['q'] ?? '');
  $sql = "SELECT r.id_reserva,r.fichaReserva,r.fechaReserva,r.fechaDevolucion,
                 u.nombre,u.apellido,u.CI,
                 l.nombre AS libro,l.autor,l.id_libro
          FROM reservas r
          JOIN usuario u ON u.id_usuario=r.id_usuario
          JOIN libros l  ON l.id_libro=r.id_libro
          WHERE r.estado=? ";
  $p = [$estado];
  if ($q !== '') {
    $sql .= "AND (r.fichaReserva LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ? OR l.nombre LIKE ? OR l.autor LIKE ?)";
    $like = '%'.$q.'%'; array_push($p,$like,$like,$like,$like,$like);
  }
  $sql .= " ORDER BY r.id_reserva DESC";
  $st=$db->prepare($sql); $st->execute($p);
  echo json_encode(['ok'=>true,'data'=>$st->fetchAll(PDO::FETCH_ASSOC)]); exit;
}

if ($act === 'detalle') {
  $id = (int)($_GET['id'] ?? 0);
  $st=$db->prepare("SELECT r.id_reserva,r.fichaReserva,
                           u.nombre,u.apellido,u.CI,u.fotografia,
                           l.nombre AS libro_nombre,l.autor AS libro_autor,l.foto_libro
                    FROM reservas r
                    JOIN usuario u ON u.id_usuario=r.id_usuario
                    JOIN libros l  ON l.id_libro=r.id_libro
                    WHERE r.id_reserva=?");
  $st->execute([$id]);
  $d=$st->fetch(PDO::FETCH_ASSOC);
  echo json_encode(['ok'=>(bool)$d,'data'=>$d]); exit;
}

if ($act === 'autorizar') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id<=0){ echo json_encode(['ok'=>false]); exit; }
  $db->beginTransaction();
  $st=$db->prepare("SELECT id_libro FROM reservas WHERE id_reserva=? FOR UPDATE"); $st->execute([$id]);
  $row=$st->fetch(PDO::FETCH_ASSOC); if(!$row){ $db->rollBack(); echo json_encode(['ok'=>false]); exit; }
  $idLibro=(int)$row['id_libro'];
  $inicio=new DateTime('now'); $fin=clone $inicio; $dias=2;
  while($dias>0){ $fin->modify('+1 day'); $dow=(int)$fin->format('N'); if($dow===6||$dow===7) continue; $dias--; }
  $db->prepare("UPDATE reservas SET estado='autorizada', fechaReserva=?, fechaDevolucion=? WHERE id_reserva=?")
     ->execute([$inicio->format('Y-m-d H:i:s'), $fin->format('Y-m-d H:i:s'), $id]);
  $db->prepare("UPDATE libros SET estado='No disponible' WHERE id_libro=?")->execute([$idLibro]);
  $db->commit(); echo json_encode(['ok'=>true]); exit;
}

if ($act === 'rechazar') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id<=0){ echo json_encode(['ok'=>false]); exit; }
  $db->beginTransaction();
  $st=$db->prepare("SELECT id_libro FROM reservas WHERE id_reserva=? FOR UPDATE"); $st->execute([$id]);
  $row=$st->fetch(PDO::FETCH_ASSOC); if(!$row){ $db->rollBack(); echo json_encode(['ok'=>false]); exit; }
  $db->prepare("UPDATE reservas SET estado='cancelada' WHERE id_reserva=?")->execute([$id]);
  $db->prepare("UPDATE libros SET estado='Disponible' WHERE id_libro=?")->execute([(int)$row['id_libro']]);
  $db->commit(); echo json_encode(['ok'=>true]); exit;
}

if ($act === 'finalizar') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id<=0){ echo json_encode(['ok'=>false]); exit; }
  $db->beginTransaction();
  $st=$db->prepare("SELECT id_libro FROM reservas WHERE id_reserva=? AND estado='autorizada' FOR UPDATE"); $st->execute([$id]);
  $row=$st->fetch(PDO::FETCH_ASSOC); if(!$row){ $db->rollBack(); echo json_encode(['ok'=>false]); exit; }
  $db->prepare("UPDATE reservas SET estado='finalizada', fechaDevolucionReal=NOW() WHERE id_reserva=?")->execute([$id]);
  $db->prepare("UPDATE libros SET estado='Disponible' WHERE id_libro=?")->execute([(int)$row['id_libro']]);
  $db->commit(); echo json_encode(['ok'=>true]); exit;
}

echo json_encode(['ok'=>false]);
