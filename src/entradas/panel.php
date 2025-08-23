<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../nucleo/vista.php';

session_start();
if(!isset($_SESSION['idUsuario'])){
  header('Location: ' . BASE_URL . '/src/entradas/login.php'); exit;
}

$rol = $_SESSION['rol'] ?? 'lector';

if ($rol === 'bibliotecario') {
  echo Vista::render('panel/bibliotecario.html', ['titulo'=>'Panel bibliotecario']);
  exit;
}

/* === LECTOR: construir tabla SOLO con libros Disponibles === */
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/libroRepositorio.php';

$db   = BaseDatos::conexion();
$repo = new LibroRepositorio($db);

$q = trim($_GET['q'] ?? '');
$libros = $repo->listarDisponibles($q);

$filas = '';
foreach ($libros as $l) {
  $id = $l->getIdLibro();
  $nombre = htmlspecialchars($l->getNombre(), ENT_QUOTES, 'UTF-8');
  $autor  = htmlspecialchars($l->getAutor(),  ENT_QUOTES, 'UTF-8');
  $foto   = htmlspecialchars($l->getFotoLibro() ?: 'default-book.png', ENT_QUOTES, 'UTF-8');

  $filas .= '<tr data-id="'.$id.'" data-nombre="'.$nombre.'" data-autor="'.$autor.'" data-foto="'.$foto.'">'
          .   '<td>'.$nombre.'</td>'
          .   '<td>'.$autor.'</td>'
          .   '<td>Disponible</td>'
          .   '<td><button class="btn primary reservar">Reservar</button></td>'
          . '</tr>';
}

if ($filas === '') {
  $txt = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
  $filas = '<tr><td colspan="4">No hay libros disponibles para “'.$txt.'”.</td></tr>';
}

echo Vista::render('panel/lector_panel.html', [
  'titulo'          => 'Panel lector',
  'valor_busqueda'  => htmlspecialchars($q, ENT_QUOTES, 'UTF-8'),
  'filas_libros'    => $filas,
  'ok'              => $_SESSION['flash_ok'] ?? ''
]);

unset($_SESSION['flash_ok']);
