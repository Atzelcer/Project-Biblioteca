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
    <link rel="stylesheet" href="../../../public/css/solicitud.css">
</head>
<body>
    <h2>SOLICITUDES DE RESERVA</h2>

    <div class="buscador">
        <form method="GET" action="" class="buscador">
            <input type="text" id="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar solicitud..." class="buscador">
            <button type="submit" class="buscador">
                Buscar
            </button>
        </form>
    </div>

    <?php if (isset($busqueda) && !empty($busqueda)): ?>
        <div class="texto">
            Resultados para: "<?php echo htmlspecialchars($busqueda); ?>" - <?php echo $totalResultados; ?> reserva(s) encontrada(s)
        </div>
    <?php else: ?>
        <div class="texto">
            Total de reservas: <?php echo $totalResultados; ?>
        </div>
    <?php endif; ?>

    <h3>Lista de Solicitudes de Reserva</h3>
    
    <table border="1">
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Usuario</th>
                <th>CI</th>
                <th>Libro</th>
                <th>Autor</th>
                <th>Estado</th>
                <th>Fecha Reserva</th>
                <th>Fecha Devolucion</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($reservas)): ?>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?php echo $reserva['fichaReserva']; ?></td>
                        <td><?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?></td>
                        <td><?php echo $reserva['CI']; ?></td>
                        <td><?php echo $reserva['libro_nombre']; ?></td>
                        <td><?php echo $reserva['autor']; ?></td>
                        <td><?php echo $reserva['estado']; ?></td>
                        <td><?php echo $reserva['fechaReserva'] ? date('d/m/Y H:i', strtotime($reserva['fechaReserva'])) : '-'; ?></td>
                        <td><?php echo $reserva['fechaDevolucion'] ? date('d/m/Y H:i', strtotime($reserva['fechaDevolucion'])) : '-'; ?></td>
                        
                        <td>
                            <div class="botones">
                                <?php if ($reserva['estado'] === 'pendiente'): ?>
                                    <button type="button" 
                                            onclick="abrirModalAutorizar(
                                                '<?php echo $reserva['id_reserva']; ?>',
                                                '<?php echo $reserva['fichaReserva']; ?>',
                                                '<?php echo $reserva['libro_nombre']; ?>',
                                                '<?php echo $reserva['codigo_libro']; ?>',
                                                '<?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?>'
                                            )">
                                        Autorizar
                                    </button>
                                    
                                    <form method="POST">
                                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                        <input type="hidden" name="accion" value="cancelar">
                                        <button type="button" 
                                            onclick="return confirm(
                                                '¿Estás seguro de cancelar esta reserva?'
                                            )">
                                        Cancelar
                                    </button>
                                    
                                    </form>
                                <?php elseif ($reserva['estado'] === 'autorizada'): ?>
                                    <span>Autorizada</span>
                                <?php elseif ($reserva['estado'] === 'finalizada'): ?>
                                    <span>Completada</span>
                                <?php else: ?>
                                    <span>Cancelada</span>
                                <?php endif; ?>
                            </div>
                            
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
        <div style="background-color: #01050B; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 500px; border-radius: 5px;">
            <h3 style="margin-top: 0;">Confirmar Autorización de Reserva</h3>
            <p>¿Está seguro que desea autorizar la siguiente reserva?</p>
            
            <div style="background-color: #01050B; padding: 15px; border-radius: 3px; margin: 15px 0;">
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
</body>
</html>
