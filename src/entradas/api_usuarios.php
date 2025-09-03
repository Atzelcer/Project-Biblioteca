<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';

session_start();
header('Content-Type: application/json');

function ok(array $data = []): void { echo json_encode(array_merge(['ok'=>true], $data)); exit; }
function fail(string $msg, array $extra = []): void { echo json_encode(array_merge(['ok'=>false,'msg'=>$msg], $extra)); exit; }

if (!isset($_SESSION['idUsuario']) || (($_SESSION['rol'] ?? '') !== 'bibliotecario')) {
  fail('no autorizado');
}

$db = BaseDatos::conexion();
$act = strtolower($_GET['action'] ?? 'list');

switch ($act) {

  case 'list': {
    $rol = $_GET['rol'] ?? 'todos';
    $q = trim($_GET['q'] ?? '');

    $sql = 'SELECT id_usuario, CI, nombre, apellido, rol, estado, correo, celular FROM usuario WHERE 1=1';
    $prm = [];
    if ($rol !== 'todos') { $sql .= ' AND rol = ?'; $prm[] = $rol; }
    if ($q !== '') {
      $sql .= ' AND (CI LIKE ? OR nombre LIKE ? OR apellido LIKE ?)';
      $like = '%' . $q . '%';
      $prm = array_merge($prm, [$like, $like, $like]);
    }
    $sql .= ' ORDER BY nombre, apellido';

    $st = $db->prepare($sql);
    $st->execute($prm);
    ok(['data'=>$st->fetchAll(PDO::FETCH_ASSOC)]);
  break; }

  case 'bloqueados': {
    $q = trim($_GET['q'] ?? '');
    try {
      $sql = <<<SQL
        SELECT
          r.id_reserva                               AS id_reserva,
          r.fichaReserva                             AS ticket,
          u.id_usuario                               AS id_usuario,
          u.nombre                                   AS nombre,
          u.apellido                                 AS apellido,
          u.CI                                       AS CI,
          u.rol                                      AS rol,
          u.estado                                   AS estado,
          u.fotografia                               AS fotografia,
          l.nombre                                   AS libro,
          l.autor                                    AS autor,
          l.foto_libro                               AS foto_libro,
          DATE_FORMAT(r.fechaReserva, '%d-%m-%Y')    AS fechaReserva,
          DATE_FORMAT(r.fechaDevolucion, '%d-%m-%Y') AS fechaDevolucion,
          CASE
            WHEN r.fechaDevolucionReal IS NOT NULL AND r.fechaDevolucionReal > r.fechaDevolucion
              THEN DATEDIFF(r.fechaDevolucionReal, r.fechaDevolucion)
            WHEN r.fechaDevolucionReal IS NULL AND r.fechaDevolucion < CURDATE()
              THEN DATEDIFF(CURDATE(), r.fechaDevolucion)
            ELSE 0
          END AS diasExpiracion
        FROM reservas r
        INNER JOIN usuario u ON r.id_usuario = u.id_usuario
        INNER JOIN libros  l ON r.id_libro   = l.id_libro
        WHERE r.estado = 'autorizada'
          AND (
                (r.fechaDevolucionReal IS NOT NULL AND r.fechaDevolucionReal > r.fechaDevolucion)
             OR (r.fechaDevolucionReal IS NULL AND r.fechaDevolucion < CURDATE())
          )
      SQL;

      $prm = [];
      if ($q !== '') {
        $sql .= ' AND (u.CI LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ? OR l.nombre LIKE ?)';
        $like = '%' . $q . '%';
        $prm = [$like, $like, $like, $like];
      }
      $sql .= ' ORDER BY diasExpiracion DESC';

      $st = $db->prepare($sql);
      $st->execute($prm);
      $rows = $st->fetchAll(PDO::FETCH_ASSOC);
      ok(['data'=>$rows,'count'=>count($rows)]);
    } catch (PDOException $e) {
      fail('error en consulta', ['error'=>$e->getMessage()]);
    }
  break; }

  case 'detalle': {
    $id = (int)($_GET['id'] ?? 0);
    $st = $db->prepare('SELECT id_usuario, CI, nombre, apellido, rol, estado, correo, celular, direccion, fotografia FROM usuario WHERE id_usuario=?');
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) fail('no encontrado');
    ok(['data'=>$row]);
  break; }

  case 'desbloquear': {
    $idReserva = (int)($_POST['id']
      ?? $_POST['id_reserva']
      ?? $_GET['id']
      ?? $_GET['id_reserva']
      ?? 0);
    if ($idReserva <= 0) { fail('id invalido'); }

    try {
      $db->beginTransaction();

      $st = $db->prepare('SELECT r.id_usuario FROM reservas r WHERE r.id_reserva=? LIMIT 1');
      $st->execute([$idReserva]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      if (!$row) { $db->rollBack(); fail('reserva no encontrada'); }

      $idUsuario = (int)$row['id_usuario'];

      $db->prepare("UPDATE usuario SET estado='normal' WHERE id_usuario=?")->execute([$idUsuario]);
      $db->prepare("UPDATE reservas SET estado='finalizada', fechaDevolucionReal = COALESCE(fechaDevolucionReal, NOW()) WHERE id_reserva=?")->execute([$idReserva]);

      $db->commit();
      ok(['msg'=>'usuario desbloqueado y reserva finalizada','id_usuario'=>$idUsuario,'id_reserva'=>$idReserva]);
    } catch (PDOException $e) {
      if ($db->inTransaction()) { $db->rollBack(); }
      fail('error en servidor', ['error'=>$e->getMessage()]);
    }
  break; }

  case 'bloquear': {
    $idUsuario = (int)($_POST['id']
      ?? $_POST['id_usuario']
      ?? $_GET['id']
      ?? $_GET['id_usuario']
      ?? 0);
    if ($idUsuario <= 0) { fail('id invalido'); }

    try {
      // Verificar que el usuario existe y no está ya bloqueado
      $st = $db->prepare('SELECT estado, nombre, apellido FROM usuario WHERE id_usuario=? LIMIT 1');
      $st->execute([$idUsuario]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      if (!$row) { fail('usuario no encontrado'); }
      
      if ($row['estado'] === 'bloqueado') { 
        fail('el usuario ya está bloqueado'); 
      }

      // Bloquear el usuario
      $db->prepare("UPDATE usuario SET estado='bloqueado' WHERE id_usuario=?")->execute([$idUsuario]);

      ok(['msg'=>'usuario bloqueado exitosamente','id_usuario'=>$idUsuario,'nombre'=>$row['nombre'].' '.$row['apellido']]);
    } catch (PDOException $e) {
      fail('error en servidor', ['error'=>$e->getMessage()]);
    }
  break; }

  default:
    fail('accion no valida');
}
