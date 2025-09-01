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
$db   = BaseDatos::conexion();
$repo = new LibroRepositorio($db);

/* ===== BIBLIOTECARIO ===== */
if ($rol === 'bibliotecario') {
    $sec = $_GET['sec'] ?? 'principal';
    $contenido = '';

    if ($sec === 'principal') {
        $libros = $repo->listarLibros();

        $contenidoLibros = '';
        foreach ($libros as $libro) {
            $foto = htmlspecialchars($libro->getFotoLibro() ?: BASE_URL . '/public/images/default-book.png', ENT_QUOTES, 'UTF-8');
            $nombre = htmlspecialchars($libro->getNombre(), ENT_QUOTES, 'UTF-8');
            $autor = htmlspecialchars($libro->getAutor(), ENT_QUOTES, 'UTF-8');
            $codigo = htmlspecialchars($libro->getCodigoLibro(), ENT_QUOTES, 'UTF-8');
            $idLibro = (int)$libro->getIdLibro();

            $contenidoLibros .= '
                <div class="libro" data-codigo="'.$codigo.'" data-nombre="'.$nombre.'" data-autor="'.$autor.'" data-id="'.$idLibro.'">
                    <img src="../../public/images/books/'.$foto.'" alt="Portada">
                    <h3>'.$nombre.'</h3>
                    <p>'.$autor.'</p>
                </div>';
        }

        if ($contenidoLibros === '') {
            $contenidoLibros = '<p>No hay libros disponibles en este momento.</p>';
        }

        $contenido = Vista::render('panel/bibliotecario_principal.html', [
            'base'   => BASE_URL,
            'libros' => $contenidoLibros
        ]);
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
