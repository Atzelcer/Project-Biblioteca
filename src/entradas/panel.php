<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../nucleo/vista.php';
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/libroRepositorio.php';

session_start();
if (!isset($_SESSION['idUsuario'])) {
    header('Location: ' . BASE_URL . '/src/entradas/login.php');
    exit;
}

$rol = $_SESSION['rol'] ?? 'lector';
$db = BaseDatos::conexion();
$repo = new LibroRepositorio($db);

if ($rol === 'bibliotecario') {
    $sec = $_GET['sec'] ?? 'principal';
    $contenido = '';

    if ($sec === 'principal') {
        $libros = $repo->listarLibros();
        $contenidoLibros = '';
        foreach ($libros as $libro) {
            $fotoLibro = $libro->getFotoLibro() ?: 'default-book.png';
            $nombre = htmlspecialchars($libro->getNombre(), ENT_QUOTES, 'UTF-8');
            $autor = htmlspecialchars($libro->getAutor(), ENT_QUOTES, 'UTF-8');
            $contenidoLibros .= '
                <div class="libro">
                    <img src="' . BASE_URL . '/public/images/books/' . htmlspecialchars($fotoLibro, ENT_QUOTES, 'UTF-8') . '" alt="Portada">
                    <h3>'.$nombre.'</h3>
                    <p>'.$autor.'</p>
                </div>';
        }
        if ($contenidoLibros === '') {
            $contenidoLibros = '<p>No hay libros disponibles en este momento.</p>';
        }
        $contenido = Vista::render('panel/bibliotecario_principal.html', [
            'base' => BASE_URL,
            'libros' => $contenidoLibros
        ]);
    } elseif ($sec === 'libros') {
        $contenido = Vista::render('panel/bibliotecario_libros.html', ['base' => BASE_URL]);
    } elseif ($sec === 'usuarios') {
        $contenido = Vista::render('panel/bibliotecario_usuarios.html', ['base' => BASE_URL]);
    }

    echo Vista::render('panel/bibliotecario.html', [
        'titulo' => 'Biblioteca USFX - Bibliotecario',
        'base' => BASE_URL,
        'sec' => $sec,
        'contenido' => $contenido
    ]);
    exit;
}

$q = trim($_GET['q'] ?? '');

$nombre = $_SESSION['nombre'] ?? '';
$apellido = $_SESSION['apellido'] ?? '';
$fullName = trim($nombre . ' ' . $apellido);

$fotoRaw = trim((string)($_SESSION['foto'] ?? ''));
$defaultRel = '/public/images/users/default-user.png';
$fotoUsuario = '';

if ($fotoRaw !== '' && preg_match('~^https?://~i', $fotoRaw)) {
    $fotoUsuario = $fotoRaw;
} else {
    $file = basename($fotoRaw);
    $rel = $file ? ('/public/images/users/' . $file) : $defaultRel;
    $root = dirname(__DIR__, 2);
    $fs = $root . $rel;
    if (!is_file($fs)) {
        $rel = $defaultRel;
    }
    $fotoUsuario = rtrim(BASE_URL, '/') . $rel;
}

$libros = $repo->listarDisponibles($q);

$filas = '';
foreach ($libros as $l) {
    $id = $l->getIdLibro();
    $nombreL = htmlspecialchars($l->getNombre(), ENT_QUOTES, 'UTF-8');
    $autorL = htmlspecialchars($l->getAutor(), ENT_QUOTES, 'UTF-8');
    $fotoL = htmlspecialchars($l->getFotoLibro() ?: 'default-book.png', ENT_QUOTES, 'UTF-8');
    $filas .= '<tr data-id="' . $id . '" data-nombre="' . $nombreL . '" data-autor="' . $autorL . '" data-foto="' . $fotoL . '">'
            . '<td>' . $nombreL . '</td>'
            . '<td>' . $autorL . '</td>'
            . '<td>Disponible</td>'
            . '<td><button class="btn primary reservar">Reservar</button></td>'
            . '</tr>';
}

if ($filas === '') {
    $txt = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
    $filas = '<tr><td colspan="4">No hay libros disponibles para “' . $txt . '”.</td></tr>';
}

echo Vista::render('panel/lector_panel.html', [
    'titulo' => 'Panel lector',
    'base' => BASE_URL,
    'valor_busqueda' => htmlspecialchars($q, ENT_QUOTES, 'UTF-8'),
    'filas_libros' => $filas,
    'nombre_usuario' => $fullName,
    'foto_usuario' => $fotoUsuario
]);
