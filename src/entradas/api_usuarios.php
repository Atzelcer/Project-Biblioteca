<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['idUsuario']) || ($_SESSION['rol'] ?? '') !== 'bibliotecario') {
  echo json_encode(['ok'=>false,'msg'=>'no autorizado']); exit;
}

$db = BaseDatos::conexion();
$act = $_GET['action'] ?? 'list';

if ($act === 'list') {
  $rol = $_GET['rol'] ?? 'todos';
  $q   = trim($_GET['q'] ?? '');

  $sql = "SELECT id_usuario,CI,nombre,apellido,rol,correo,celular FROM usuario WHERE 1=1 ";
  $params = [];
  if ($rol !== 'todos') { $sql .= "AND rol=? "; $params[] = $rol; }
  if ($q !== '') {
    $sql .= "AND (CI LIKE ? OR nombre LIKE ? OR apellido LIKE ?) ";
    $like = '%'.$q.'%';
    array_push($params,$like,$like,$like);
  }
  $sql .= "ORDER BY nombre,apellido";
  $st = $db->prepare($sql); $st->execute($params);
  echo json_encode(['ok'=>true,'data'=>$st->fetchAll(PDO::FETCH_ASSOC)]); exit;
}

if ($act === 'bloqueados') {
    $q = trim($_GET['q'] ?? '');
    
    try {
        $sql = "SELECT 
            r.id_reserva,
            r.fichaReserva AS ticket,
            u.CI,
            u.nombre,
            u.apellido,
            l.nombre AS libro,
            l.autor,
            DATE_FORMAT(r.fechaDevolucion, '%d-%m-%Y') AS fechaDevolucion,
            DATEDIFF(CURDATE(), r.fechaDevolucion) AS diasExpiracion
        FROM reservas r
        INNER JOIN usuario u ON r.id_usuario = u.id_usuario
        INNER JOIN libros l ON r.id_libro = l.id_libro
        WHERE r.estado = 'autorizado'
            AND r.fechaDevolucionReal IS NULL
            AND r.fechaDevolucion < CURDATE()";

        $params = [];
        if ($q !== '') {
            $sql .= " AND (u.CI LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ? OR l.nombre LIKE ?)";
            $like = '%'.$q.'%';
            $params = array_fill(0, 4, $like);
        }

        $sql .= " ORDER BY diasExpiracion DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'ok' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'ok' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}




if ($act === 'detalle') {
  $id = (int)($_GET['id'] ?? 0);
  $st = $db->prepare("SELECT id_usuario,CI,nombre,apellido,rol,correo,celular,direccion,fotografia FROM usuario WHERE id_usuario=?");
  $st->execute([$id]);
  $d = $st->fetch(PDO::FETCH_ASSOC);
  echo json_encode(['ok'=> (bool)$d, 'data'=>$d]); exit;
}

echo json_encode(['ok'=>false,'msg'=>'accion no valida']);
