<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/verificarSesion.php';

// Procesar acciones del bibliotecario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && isset($_POST['id_reserva'])) {
        $id_reserva = $_POST['id_reserva'];
        $accion = $_POST['accion'];
        
        if ($accion === 'autorizar') {
            // Autorizar reserva: establecer fechaReserva y fechaDevolucion
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
            // Cancelar reserva y liberar el libro
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

// Obtener todas las reservas con información del usuario y libro
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
JOIN libros l ON r.id_libro = l.id_libro
ORDER BY 
    CASE r.estado 
        WHEN 'pendiente' THEN 1
        WHEN 'autorizada' THEN 2
        WHEN 'finalizada' THEN 3
        WHEN 'cancelada' THEN 4
    END,
    r.id_reserva DESC";

$result = $conexion->query($sql);
$reservas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
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
        <div>
            <a href="BibliotecarioPanel-Principal.php">
                <button type="button">Volver al Panel</button>
            </a>
            <a href="../../../public/logout.php">
                <button type="button">Cerrar Sesión</button>
            </a>
        </div>
    </div>
    
    <?php if (isset($mensaje)): ?>
        <div style="padding: 10px; border: 1px solid #ccc; margin: 10px 0; background-color: #e6ffe6;">
            <?php echo htmlspecialchars($mensaje); ?>
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
                    <tr style="background-color: <?php 
                        echo $reserva['estado'] === 'pendiente' ? '#fff3cd' : 
                            ($reserva['estado'] === 'autorizada' ? '#d4edda' : 
                            ($reserva['estado'] === 'finalizada' ? '#d1ecf1' : '#f8d7da')); 
                    ?>">
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
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                    <input type="hidden" name="accion" value="autorizar">
                                    <button type="submit" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer;">Autorizar</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                    <input type="hidden" name="accion" value="cancelar">
                                    <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer;" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">Cancelar</button>
                                </form>
                            <?php elseif ($reserva['estado'] === 'autorizada'): ?>
                                <span style="color: green;">✓ Autorizada</span>
                            <?php elseif ($reserva['estado'] === 'finalizada'): ?>
                                <span style="color: blue;">✓ Completada</span>
                            <?php else: ?>
                                <span style="color: red;">✗ Cancelada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="padding: 20px; text-align: center;">No hay solicitudes de reservas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        <h4>Leyenda de Estados:</h4>
        <ul>
            <li><strong>PENDIENTE:</strong> Solicitud nueva, esperando autorización</li>
            <li><strong>AUTORIZADA:</strong> Reserva aprobada, lector puede recoger el libro</li>
            <li><strong>FINALIZADA:</strong> Libro devuelto correctamente</li>
            <li><strong>CANCELADA:</strong> Reserva rechazada o cancelada</li>
        </ul>
    </div>
</body>
</html>
