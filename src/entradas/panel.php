<?php
// src/entradas/panel.php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../nucleo/vista.php';
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/libroRepositorio.php';

session_start();
if (!isset($_SESSION['idUsuario'])) {
    header('Location: ' . BASE_URL . '/src/entradas/login.php');
    exit;
}

/**
 * Devuelve una URL absoluta para la foto del usuario.
 * - Si la cadena ya es una URL http(s), se respeta tal cual.
 * - Si es nombre de archivo, busca en /public/images/users.
 * - Si no existe, usa el default.
 */
function fotoUsuarioUrl(string $base, ?string $fotoRaw): string {
    $fotoRaw = trim((string)$fotoRaw);
    $defaultRel = '/public/images/users/default-user.png';

    if ($fotoRaw !== '' && preg_match('~^https?://~i', $fotoRaw)) {
        return $fotoRaw; // URL absoluta externa
    }

    $file = basename($fotoRaw);
    $rel  = $file ? ('/public/images/users/' . $file) : $defaultRel;
    $root = dirname(__DIR__, 2);
    $fs   = $root . $rel;

    if (!is_file($fs)) {
        $rel = $defaultRel;
    }
    return rtrim($base, '/') . $rel;
}

/**
 * Estima un slug de categoría a partir de título/autor cuando
 * no viene desde la BD. Esto ayuda a que los filtros funcionen.
 */
function guessCategoriaSlug(string $titulo, string $autor): string {
    $t = mb_strtolower($titulo . ' ' . $autor, 'UTF-8');

    $tests = [
        'programacion' => ['program', 'code', 'python', 'java ', 'javascript', 'algoritmo', 'algorithms', 'pattern', 'refactor', 'arquitectura', 'architecture', 'clean', 'pragmatic', 'c++', 'c#', 'php'],
        'matematicas'  => ['álgebra','algebra','cálculo','calculo','lineal','matem','topología','topologia','ecuaciones','probabilidad','estadística','estadistica','analysis','analisis','geometría','geometria'],
        'fisica'       => ['física','fisica','electromagnetismo','mecánica','mecanica','óptica','optica','termodinámica','termodinamica','moderna','universitaria','mechanics','physics'],
        'historia'     => ['historia','universal','américa','latina','roma','argentina','contemporánea','contemporanea'],
        'idiomas'      => ['english','inglés','ingles','grammar','business english','idioma','ielts','cambridge'],
        'arquitectura' => ['arquitectura','architecture','construcción','construccion','edificio','espacio','orden'],
        'quimica'      => ['química','quimica','orgánica','organica','inorgánica','inorganica','fisicoquimica','general','quimica analitica','analítica'],
        'literatura'   => ['quijote','soledad','crónica','cronica','literatura','novela','cuento','arte de escribir'],
        'ingenieria'   => ['ingenier','engineer','industrial','civil','sistemas','control','materiales','fluid','power system'],
        'electronica'  => ['electrón','electron','digital','circuit','microcontrol','microelectronic','signals','devices'],
        'biologia'     => ['biología','biologia','genética','genetica','microbiologia','molecular'],
        'arte'         => ['arte','pintura','escultura','historia del arte'],
    ];

    foreach ($tests as $slug => $keys) {
        foreach ($keys as $k) {
            if (mb_strpos($t, $k, 0, 'UTF-8') !== false) {
                return $slug;
            }
        }
    }
    return 'otros';
}

$rol = $_SESSION['rol'] ?? 'lector';
$db  = BaseDatos::conexion();
$repo = new LibroRepositorio($db);

/* =========================
 *  ROL: BIBLIOTECARIO
 * ========================= */
if ($rol === 'bibliotecario') {
    $sec = $_GET['sec'] ?? 'principal';
    $contenido = '';

    if ($sec === 'principal') {
        $libros = $repo->listarLibros();
        $contenidoLibros = '';
        foreach ($libros as $libro) {
            $foto = $libro->getFotoLibro() ?: 'default-book.png';
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


$q = trim((string)($_GET['q'] ?? ''));

$nombre = trim((string)($_SESSION['nombre'] ?? ''));
$apellido = trim((string)($_SESSION['apellido'] ?? ''));
$fullName = trim($nombre . ' ' . $apellido);

$fotoUsuario = fotoUsuarioUrl(BASE_URL, $_SESSION['foto'] ?? '');

/** Libros disponibles (por búsqueda si llega ?q=) */
$libros = $repo->listarDisponibles($q);

/** Construcción de filas para “fuente de datos” de la galería (lector_reservas.js) */
$filas = '';
foreach ($libros as $l) {
    $id     = (int)$l->getIdLibro();
    $nombreL= htmlspecialchars($l->getNombre(), ENT_QUOTES, 'UTF-8');
    $autorL = htmlspecialchars($l->getAutor(), ENT_QUOTES, 'UTF-8');
    $fotoL  = htmlspecialchars($l->getFotoLibro() ?: 'default-book.png', ENT_QUOTES, 'UTF-8');

    // slug estimado para filtros (mejor UX)
    $catSlug = guessCategoriaSlug($nombreL, $autorL);
    $catAttr = htmlspecialchars($catSlug, ENT_QUOTES, 'UTF-8');

    $filas .=
        '<tr data-id="' . $id . '" data-nombre="' . $nombreL . '" data-autor="' . $autorL . '" data-foto="' . $fotoL . '" data-categoria="' . $catAttr . '">' .
            '<td>' . $nombreL . '</td>' .
            '<td>' . $autorL . '</td>' .
            '<td>Disponible</td>' .
            '<td><button class="btn primary reservar">Reservar</button></td>' .
        '</tr>';
}

if ($filas === '') {
    $txt = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
    $filas = '<tr><td colspan="4">No hay libros disponibles para “' . $txt . '”.</td></tr>';
}

/**
 * Render del layout de panel del lector.
 * Importante: en la plantilla HTML (panel/lector_panel.html), carga
 * primero public/js/lector_reservas.js y luego public/js/lector_panel.js
 * para que se muestren los filtros y la barra centrada en “Reservar”.
 */
echo Vista::render('panel/lector_panel.html', [
    'titulo'         => 'Panel lector',
    'base'           => BASE_URL,
    'valor_busqueda' => htmlspecialchars($q, ENT_QUOTES, 'UTF-8'),
    'filas_libros'   => $filas,
    'nombre_usuario' => htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'),
    'foto_usuario'   => htmlspecialchars($fotoUsuario, ENT_QUOTES, 'UTF-8'),
]);
