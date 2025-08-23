<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/baseDatos.php';
require_once __DIR__ . '/../repositorios/libroRepositorio.php';

session_start();
if(!isset($_SESSION['idUsuario'])){
  header('Location: ' . BASE_URL . '/src/entradas/login.php'); exit;
}

$db = BaseDatos::conexion();
$repo = new LibroRepositorio($db);
$q = trim($_GET['q'] ?? '');
$libros = $repo->listarDisponibles($q);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Libros disponibles</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial;margin:0;padding:18px;background:#0b1223;color:#e5e7eb}
    h2{margin:6px 0 16px}
    .bar{display:flex;gap:8px;margin-bottom:12px}
    .bar input{flex:1;padding:10px;border-radius:8px;border:1px solid #334155;background:#0f172a;color:#e5e7eb}
    .bar button{padding:10px 14px;border-radius:8px;border:0;background:#22c55e;color:#06220f;font-weight:700;cursor:pointer}
    table{width:100%;border-collapse:collapse;background:#0f172a;border-radius:12px;overflow:hidden}
    th,td{padding:12px;border-bottom:1px solid #1f2937}
    thead th{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;background:#111827}
    tbody tr:hover{background:#111827}
    .btn{padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-weight:700}
    .primary{background:#60a5fa;color:#0a1424}
    /* modal */
    .overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;place-items:center}
    .modal{background:#0f172a;border:1px solid #334155;border-radius:12px;padding:16px;max-width:520px;width:92%}
    .modal h3{margin:0 0 8px}
    .modal .row{display:flex;gap:12px;align-items:flex-start}
    .modal img{width:86px;height:110px;object-fit:cover;border:1px solid #334155;border-radius:8px}
    .modal footer{display:flex;gap:10px;justify-content:flex-end;margin-top:12px}
    .ok{background:#22c55e;color:#06220f}
    .ghost{background:transparent;border:1px solid #334155;color:#e5e7eb}
    .ticket{font-size:28px;font-weight:800;letter-spacing:.06em}
  </style>
</head>
<!-- pasamos BASE_URL como data-attribute para que el JS (estático) lo lea -->
<body data-base="<?= BASE_URL ?>">
  <h2>LECTOR</h2>

  <form class="bar" method="get" action="<?= BASE_URL ?>/src/entradas/lector_libros.php">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="buscar por nombre o autor">
    <button type="submit">Buscar</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Nombre</th><th>Autor</th><th>Disponibilidad</th><th>Reservar</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($libros as $l): ?>
      <tr data-id="<?= $l->getIdLibro() ?>"
          data-nombre="<?= htmlspecialchars($l->getNombre()) ?>"
          data-autor="<?= htmlspecialchars($l->getAutor()) ?>">
        <td><?= htmlspecialchars($l->getNombre()) ?></td>
        <td><?= htmlspecialchars($l->getAutor()) ?></td>
        <td>Disponible</td>
        <td><button class="btn primary reservar">Reservar</button></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($libros)): ?>
      <tr><td colspan="4">No hay libros disponibles para “<?= htmlspecialchars($q) ?>”.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="overlay" id="mConfirm">
    <div class="modal">
      <h3>Reservar libro</h3>
      <div class="row">
        <div>
          <p><strong id="nombreLibro"></strong></p>
          <p>Autor: <span id="autorLibro"></span></p>
          <p>Horarios: Lun-Vie 8-12 y 14-18</p>
        </div>
      </div>
      <footer>
        <button class="btn ghost" id="btnCancel">Cancelar</button>
        <button class="btn ok" id="btnDo">Reservar</button>
      </footer>
    </div>
  </div>

  <div class="overlay" id="mTicket">
    <div class="modal">
      <h3>Información de la reserva</h3>
      <p class="ticket" id="tFicha">TICK----</p>
      <p id="tLibro"></p>
      <p id="tAutor"></p>
      <p>Estado: pendiente (a autorizar por bibliotecario)</p>
      <p>Las fechas se definirán cuando pase a “No disponible”.</p>
      <footer><button class="btn ok" id="btnCerrarTicket">Entendido</button></footer>
    </div>
  </div>

  <!-- JS externo -->
  <script src="<?= BASE_URL ?>/public/js/lector_libros.js"></script>
</body>
</html>
