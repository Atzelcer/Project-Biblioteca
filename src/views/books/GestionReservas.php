<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/verificarSesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && isset($_POST['id_reserva'])) {
        $id_reserva = $_POST['id_reserva'];
        $accion = $_POST['accion'];
        
        if ($accion === 'autorizar') {
            $fechaReserva = date('Y-m-d H:i:s');
            $fechaDevolucion = date('Y-m-d H:i:s', strtotime('+2 days'));
            
            $sql_autorizar = "UPDATE reservas SET estado = 'autorizada', fechaReserva = ?, fechaDevolucion = ? WHERE id_reserva = ?";
            $stmt = $conexion->prepare($sql_autorizar);
            $stmt->bind_param('ssi', $fechaReserva, $fechaDevolucion, $id_reserva);
            
            if ($stmt->execute()) {
                $mensaje = "Reserva autorizada exitosamente";
            } else {
                $mensaje = "Error al autorizar la reserva";
            }
            $stmt->close();
        } elseif ($accion === 'cancelar') {
            $sql_cancelar = "UPDATE reservas SET estado = 'cancelada' WHERE id_reserva = ?";
            $sql_liberar = "UPDATE libros SET estado = 'Disponible' WHERE id_libro = (SELECT id_libro FROM reservas WHERE id_reserva = ?)";
            
            $stmt1 = $conexion->prepare($sql_cancelar);
            $stmt1->bind_param('i', $id_reserva);
            $stmt1->execute();
            $stmt1->close();
            
            $stmt2 = $conexion->prepare($sql_liberar);
            $stmt2->bind_param('i', $id_reserva);
            $stmt2->execute();
            $stmt2->close();
            
            $mensaje = "Reserva cancelada y libro liberado";
        }
    }
}

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

$sql = "SELECT 
    r.id_reserva,
    r.fichaReserva,
    r.fechaReserva,
    r.fechaDevolucion,
    r.estado,
    u.nombre,
    u.apellido,
    u.CI,
    l.codigo_libro,
    l.nombre as libro_nombre,
    l.autor
FROM reservas r
JOIN usuario u ON r.id_usuario = u.id_usuario
JOIN libros l ON r.id_libro = l.id_libro";

if (!empty($busqueda)) {
    $busqueda_escaped = $conexion->real_escape_string($busqueda);
    $sql .= " WHERE (
        u.nombre LIKE '%{$busqueda_escaped}%' OR 
        u.apellido LIKE '%{$busqueda_escaped}%' OR 
        CONCAT(u.nombre, ' ', u.apellido) LIKE '%{$busqueda_escaped}%' OR
        u.CI LIKE '%{$busqueda_escaped}%' OR
        r.fichaReserva LIKE '%{$busqueda_escaped}%' OR
        l.nombre LIKE '%{$busqueda_escaped}%' OR
        l.autor LIKE '%{$busqueda_escaped}%' OR
        l.codigo_libro LIKE '%{$busqueda_escaped}%'
    )";
}

$sql .= " ORDER BY 
    CASE r.estado 
        WHEN 'pendiente' THEN 1
        WHEN 'autorizada' THEN 2
        WHEN 'finalizada' THEN 3
        WHEN 'cancelada' THEN 4
    END,
    r.id_reserva DESC";

$result = $conexion->query($sql);
$reservas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$totalResultados = count($reservas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Reserva - Bibliotecario</title>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>SOLICITUDES DE RESERVA</h1>
        <div style="display: flex; gap: 15px;>
            <a href="BibliotecarioPanel-Principal.php">
                <button type="button">Volver al Panel</button>
            </a>
            <a href="../../config/logout.php">
                <button type="button">Cerrar Sesión</button>
            </a>
        </div>
    </div>
    
    <?php if (isset($mensaje)): ?>
        <div style="padding: 10px; border: 1px solid #ccc; margin: 10px 0; background-color: #e6ffe6; text-align: center;">
             <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;text-align:center; border-radius: 10px;">
        <form method="GET" action="" style="display: inline-block;">
           
            <input 
                type="text" 
                id="buscar" 
                name="buscar" 
                placeholder="Nombre, CI, Ticket, Libro o Autor..." 
                value="<?php echo htmlspecialchars($busqueda); ?>"
                style="padding: 8px 12px; width: 300px; border: 1px solid #ccc;"
            >
            <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; cursor: pointer;">
                Buscar
            </button>
            <?php if (!empty($busqueda)): ?>
                <a href="?" style="text-decoration: none;">
                    <button type="button" style="padding: 8px 15px; background-color: #6c757d; color: white; border: none; cursor: pointer;">
                        Limpiar
                    </button>
                </a>
            <?php endif; ?>
        </form>
    </div>
    <?php if (isset($busqueda) && !empty($busqueda)): ?>
        <div style="background-color: #e3f2fd; padding: 10px; margin-bottom: 15px;">
            Resultados para: "<?php echo htmlspecialchars($busqueda); ?>" - <?php echo $totalResultados; ?> reserva(s) encontrada(s)
        </div>
    <?php else: ?>
        <div style="background-color: #f5f5f5; padding: 10px; margin-bottom: 15px;">
            Total de reservas: <?php echo $totalResultados; ?>
        </div>
    <?php endif; ?>

    <h3>Lista de Solicitudes de Reserva</h3>
    
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 8px;">Ticket</th>
                <th style="padding: 8px;">Usuario</th>
                <th style="padding: 8px;">CI</th>
                <th style="padding: 8px;">Libro</th>
                <th style="padding: 8px;">Autor</th>
                <th style="padding: 8px;">Estado</th>
                <th style="padding: 8px;">Fecha Reserva</th>
                <th style="padding: 8px;">Fecha Devolucion</th>
                <th style="padding: 8px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reservas)): ?>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;"><?php echo htmlspecialchars($reserva['fichaReserva']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['CI']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['libro_nombre']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['autor']); ?></td>
                        <td style="padding: 8px; font-weight: bold;"><?php echo strtoupper($reserva['estado']); ?></td>
                        <td style="padding: 8px;"><?php echo $reserva['fechaReserva'] ? date('d/m/Y H:i', strtotime($reserva['fechaReserva'])) : '-'; ?></td>
                        <td style="padding: 8px;"><?php echo $reserva['fechaDevolucion'] ? date('d/m/Y H:i', strtotime($reserva['fechaDevolucion'])) : '-'; ?></td>
                        <td style="padding: 8px;">
                            <?php if ($reserva['estado'] === 'pendiente'): ?>
                                <button type="button" 
                                        onclick="abrirModalAutorizar(
                                            '<?php echo $reserva['id_reserva']; ?>',
                                            '<?php echo htmlspecialchars($reserva['fichaReserva'], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($reserva['libro_nombre'], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($reserva['codigo_libro'], ENT_QUOTES); ?>',
                                            '<?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido'], ENT_QUOTES); ?>'
                                        )" 
                                        style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer;">
                                    Autorizar
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                    <input type="hidden" name="accion" value="cancelar">
                                    <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer;" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">Cancelar</button>
                                </form>
                            <?php elseif ($reserva['estado'] === 'autorizada'): ?>
                                <span style="color: green;">Autorizada</span>
                            <?php elseif ($reserva['estado'] === 'finalizada'): ?>
                                <span style="color: blue;">Completada</span>
                            <?php else: ?>
                                <span style="color: red;">Cancelada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">
                        <?php if (isset($busqueda) && !empty($busqueda)): ?>
                            No se encontraron reservas que coincidan con la búsqueda "<?php echo htmlspecialchars($busqueda); ?>"
                        <?php else: ?>
                            No hay solicitudes de reservas
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal de confirmación de autorización -->
    <div id="modalAutorizar" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 500px; border-radius: 5px;">
            <h3 style="margin-top: 0;">Confirmar Autorización de Reserva</h3>
            <p>¿Está seguro que desea autorizar la siguiente reserva?</p>
            
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 3px; margin: 15px 0;">
                <p><strong>Ticket:</strong> <span id="modalTicket"></span></p>
                <p><strong>Libro:</strong> <span id="modalLibro"></span></p>
                <p><strong>Ejemplar:</strong> <span id="modalEjemplar"></span></p>
                <p><strong>Solicitante:</strong> <span id="modalSolicitante"></span></p>
            </div>
            
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="cerrarModal()" style="background-color: #6c757d; color: white; border: none; padding: 10px 20px; margin-right: 10px; cursor: pointer; border-radius: 3px;">
                    Cancelar
                </button>
                <button type="button" onclick="confirmarAutorizacion()" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px;">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para autorización -->
    <form id="formAutorizar" method="POST" style="display: none;">
        <input type="hidden" name="id_reserva" id="hiddenIdReserva">
        <input type="hidden" name="accion" value="autorizar">
    </form>

    <script src="../../../public/js/autorizarReserva.js"></script>
</body>
</html>
