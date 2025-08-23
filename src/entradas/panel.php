<?php
// src/entradas/panel.php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../nucleo/vista.php';

session_start();
if (!isset($_SESSION['idUsuario'])) {
  header('Location: ' . BASE_URL . '/src/entradas/login.php');
  exit;
}

$rol = $_SESSION['rol'] ?? 'lector';

/* ===== BIBLIOTECARIO ===== */
if ($rol === 'bibliotecario') {
  $sec = $_GET['sec'] ?? 'principal';
  $contenido = '';

  if ($sec === 'principal') {
    $contenido =
      '<div class="cajas">
         <div class="caja" onclick="location.href=\'' . BASE_URL . '/src/entradas/panel.php?sec=usuarios\'">
           <div class="caja_titulo">Usuarios</div>
         </div>
         <div class="caja" onclick="location.href=\'' . BASE_URL . '/src/entradas/panel.php?sec=libros\'">
           <div class="caja_titulo">Libros</div>
         </div>
       </div>';
  } elseif ($sec === 'libros') {
    $contenido = Vista::render('panel/bibliotecario_libros.html', ['base' => BASE_URL]);
  } elseif ($sec === 'usuarios') {
    $contenido = Vista::render('panel/bibliotecario_usuarios.html', ['base' => BASE_URL]);
  }

  echo Vista::render('panel/bibliotecario.html', [
    'titulo'    => 'Biblioteca USFX - Bibliotecario',
    'base'      => BASE_URL,
    'sec'       => $sec,
    'contenido' => $contenido
  ]);
  exit;
}

/* ===== LECTOR ===== */
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/libroRepositorio.php';

$db   = BaseDatos::conexion();
$repo = new LibroRepositorio($db);

$q = trim($_GET['q'] ?? '');
$libros = $repo->listarDisponibles($q);

$filas = '';
foreach ($libros as $l) {
  $id     = $l->getIdLibro();
  $nombre = htmlspecialchars($l->getNombre(), ENT_QUOTES, 'UTF-8');
  $autor  = htmlspecialchars($l->getAutor(),  ENT_QUOTES, 'UTF-8');
  $foto   = htmlspecialchars($l->getFotoLibro() ?: 'default-book.png', ENT_QUOTES, 'UTF-8');

  $filas .= '<tr data-id="' . $id . '" data-nombre="' . $nombre . '" data-autor="' . $autor . '" data-foto="' . $foto . '">'
          .   '<td>' . $nombre . '</td>'
          .   '<td>' . $autor  . '</td>'
          .   '<td>Disponible</td>'
          .   '<td><button class="btn primary reservar">Reservar</button></td>'
          . '</tr>';
}

if ($filas === '') {
  $txt = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
  $filas = '<tr><td colspan="4">No hay libros disponibles para “' . $txt . '”.</td></tr>';
}

echo Vista::render('panel/lector_panel.html', [
  'titulo'         => 'Panel lector',
  'base'           => BASE_URL,
  'valor_busqueda' => htmlspecialchars($q, ENT_QUOTES, 'UTF-8'),
  'filas_libros'   => $filas,
  'ok'             => $_SESSION['flash_ok'] ?? ''
]);

unset($_SESSION['flash_ok']);
