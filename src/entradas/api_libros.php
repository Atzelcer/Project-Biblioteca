<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';

header('Content-Type: application/json');
$db = BaseDatos::conexion();
$q = trim($_GET['q'] ?? '');
$sql = "SELECT id_libro, nombre, autor, foto_libro FROM libros WHERE 1";
$p = [];
if ($q !== '') {
  $sql .= " AND (nombre LIKE ? OR autor LIKE ?)";
  $like = "%$q%";
  $p = [$like, $like];
}
$sql .= " ORDER BY nombre ASC LIMIT 40";
$st = $db->prepare($sql); $st->execute($p);
$libros = $st->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'data'=>$libros]);
