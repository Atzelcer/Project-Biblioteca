<?php
session_start();
require_once '../../config/database.php';
// require_once '../../config/verificarSesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && isset($_POST['id_reserva'])) {
        $id_reserva = $_POST['id_reserva'];
        $accion = $_POST['accion'];
        
        if ($accion === 'registrar_devolucion') {
            $fechaDevolucionReal = date('Y-m-d H:i:s');
            
            $sql_devolucion = "UPDATE reservas SET estado = 'finalizada' WHERE id_reserva = ?";
            $stmt = $conexion->prepare($sql_devolucion);
            $stmt->bind_param('i', $id_reserva);
            
            if ($stmt->execute()) {
                $sql_liberar = "UPDATE libros SET estado = 'Disponible' WHERE id_libro = (SELECT id_libro FROM reservas WHERE id_reserva = ?)";
                $stmt2 = $conexion->prepare($sql_liberar);
                $stmt2->bind_param('i', $id_reserva);
                $stmt2->execute();
                $stmt2->close();
                
                $mensaje = "Devolución registrada exitosamente";
            } else {
                $mensaje = "Error al registrar la devolución";
            }
            $stmt->close();
        } elseif ($accion === 'bloquear') {
            $sql_bloquear = "UPDATE reservas SET estado = 'cancelada' WHERE id_reserva = ?";
            $stmt = $conexion->prepare($sql_bloquear);
            $stmt->bind_param('i', $id_reserva);
            
            if ($stmt->execute()) {
                $sql_liberar = "UPDATE libros SET estado = 'Disponible' WHERE id_libro = (SELECT id_libro FROM reservas WHERE id_reserva = ?)";
                $stmt2 = $conexion->prepare($sql_liberar);
                $stmt2->bind_param('i', $id_reserva);
                $stmt2->execute();
                $stmt2->close();
                
                $mensaje = "Reserva bloqueada y libro liberado";
            } else {
                $mensaje = "Error al bloquear la reserva";
            }
            $stmt->close();
        }
    }
}

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

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
WHERE r.estado = 'autorizada'";

if (!empty($busqueda)) {
    $busqueda_escaped = $conexion->real_escape_string($busqueda);
    $sql .= " AND (
        u.nombre LIKE '%{$busqueda_escaped}%' OR 
        u.apellido LIKE '%{$busqueda_escaped}%' OR 
        CONCAT(u.nombre, ' ', u.apellido) LIKE '%{$busqueda_escaped}%' OR
        u.CI LIKE '%{$busqueda_escaped}%' OR
        r.fichaReserva LIKE '%{$busqueda_escaped}%'
    )";
}

if (!empty($fecha_inicio)) {
    $fecha_inicio_escaped = $conexion->real_escape_string($fecha_inicio . ' 00:00:00');
    $sql .= " AND r.fechaReserva >= '{$fecha_inicio_escaped}'";
}

if (!empty($fecha_fin)) {
    $fecha_fin_escaped = $conexion->real_escape_string($fecha_fin . ' 23:59:59');
    $sql .= " AND r.fechaReserva <= '{$fecha_fin_escaped}'";
}

$sql .= " ORDER BY r.fechaReserva DESC";

$result = $conexion->query($sql);
$reservas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$totalResultados = count($reservas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Autorizados - Bibliotecario</title>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>BIBLIOTECARIO - Autorizados</h1>
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

    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
        <form method="GET" action="">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="buscar" style="font-weight: bold;">Buscar:</label>
                    <input 
                        type="text" 
                        id="buscar" 
                        name="buscar" 
                        placeholder="CI, Ticket, Apellido o Nombre..." 
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                        style="padding: 8px 12px; width: 250px; border: 1px solid #ccc;"
                    >
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="fecha_inicio" style="font-weight: bold;">Desde:</label>
                    <input 
                        type="date" 
                        id="fecha_inicio" 
                        name="fecha_inicio" 
                        value="<?php echo htmlspecialchars($fecha_inicio); ?>"
                        style="padding: 8px; border: 1px solid #ccc;"
                    >
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="fecha_fin" style="font-weight: bold;">Hasta:</label>
                    <input 
                        type="date" 
                        id="fecha_fin" 
                        name="fecha_fin" 
                        value="<?php echo htmlspecialchars($fecha_fin); ?>"
                        style="padding: 8px; border: 1px solid #ccc;"
                    >
                </div>
                
                <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; cursor: pointer;">
                    Buscar
                </button>
                
                <?php if (!empty($busqueda) || !empty($fecha_inicio) || !empty($fecha_fin)): ?>
                    <a href="?" style="text-decoration: none;">
                        <button type="button" style="padding: 8px 15px; background-color: #6c757d; color: white; border: none; cursor: pointer;">
                            Limpiar
                        </button>
                    </a>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 15px; text-align: center; padding-top: 10px; border-top: 1px solid #ddd;">
                <span style="font-weight: bold; margin-right: 15px;">Filtros rápidos:</span>
                <a href="?fecha_inicio=<?php echo date('Y-m-d'); ?>" style="text-decoration: none; margin-right: 8px;">
                    <button type="button" style="padding: 4px 8px; background-color: #17a2b8; color: white; border: none; cursor: pointer; font-size: 12px;">
                        Hoy
                    </button>
                </a>
                <a href="?fecha_inicio=<?php echo date('Y-m-d', strtotime('-7 days')); ?>&fecha_fin=<?php echo date('Y-m-d'); ?>" style="text-decoration: none; margin-right: 8px;">
                    <button type="button" style="padding: 4px 8px; background-color: #17a2b8; color: white; border: none; cursor: pointer; font-size: 12px;">
                        Últimos 7 días
                    </button>
                </a>
                <a href="?fecha_inicio=<?php echo date('Y-m-01'); ?>&fecha_fin=<?php echo date('Y-m-d'); ?>" style="text-decoration: none;">
                    <button type="button" style="padding: 4px 8px; background-color: #17a2b8; color: white; border: none; cursor: pointer; font-size: 12px;">
                        Este mes
                    </button>
                </a>
            </div>
        </form>
    </div>

    <h3>Lista de Solicitudes de Reserva</h3>
    
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 8px;">Ticket</th>
                <th style="padding: 8px;">Usuario</th>
                <th style="padding: 8px;">CI</th>
                <th style="padding: 8px;">Libro</th>
                <th style="padding: 8px;">Autor</th>
                <th style="padding: 8px;">F. Reserva</th>
                <th style="padding: 8px;">F. Devolucion</th>
                <th style="padding: 8px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($reservas) > 0): ?>
                <?php foreach ($reservas as $reserva): ?>
                    <tr style="background-color: #d4edda;">
                        <td style="padding: 8px; font-weight: bold;"><?php echo htmlspecialchars($reserva['fichaReserva']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['CI']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['libro_nombre']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($reserva['autor']); ?></td>
                        <td style="padding: 8px;"><?php echo $reserva['fechaReserva'] ? date('d/m/Y H:i', strtotime($reserva['fechaReserva'])) : 'N/A'; ?></td>
                        <td style="padding: 8px;"><?php echo $reserva['fechaDevolucion'] ? date('d/m/Y H:i', strtotime($reserva['fechaDevolucion'])) : 'N/A'; ?></td>
                        <td style="padding: 8px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                <input type="hidden" name="accion" value="registrar_devolucion">
                                <button type="submit" style="background-color: #28a745; color: white; border: none; padding: 4px 8px; cursor: pointer; margin-right: 5px; font-size: 12px;" onclick="return confirm('¿Confirmar devolución del libro?')">Registrar Devo</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                <input type="hidden" name="accion" value="bloquear">
                                <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 4px 8px; cursor: pointer; font-size: 12px;" onclick="return confirm('¿Bloquear esta reserva?')">Bloquear</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        <?php if (!empty($busqueda) || !empty($fecha_inicio) || !empty($fecha_fin)): ?>
                            No se encontraron reservas autorizadas que coincidan con los filtros aplicados
                        <?php else: ?>
                            No hay reservas autorizadas
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
</body>
</html>
