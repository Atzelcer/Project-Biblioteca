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
            
            $sql_devolucion = "UPDATE reservas SET estado = 'finalizada', fechaDevolucionReal = ? WHERE id_reserva = ?";
            $stmt = $conexion->prepare($sql_devolucion);
            $stmt->bind_param('si', $fechaDevolucionReal, $id_reserva);
            
            if ($stmt->execute()) {
                $sql_liberar = "UPDATE libros SET estado = 'Disponible' WHERE id_libro = (SELECT id_libro FROM reservas WHERE id_reserva = ?)";
                $stmt2 = $conexion->prepare($sql_liberar);
                $stmt2->bind_param('i', $id_reserva);
                
                if ($stmt2->execute()) {
                    $mensaje = "Devolución registrada exitosamente el " . date('d/m/Y H:i', strtotime($fechaDevolucionReal));
                    $mensaje_tipo = "success";
                } else {
                    $mensaje = "Error al liberar el libro";
                    $mensaje_tipo = "error";
                }
                $stmt2->close();
            } else {
                $mensaje = "Error al registrar la devolución";
                $mensaje_tipo = "error";
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
                $mensaje_tipo = "warning";
            } else {
                $mensaje = "Error al bloquear la reserva";
                $mensaje_tipo = "error";
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
    u.fotografia,
    l.codigo_libro,
    l.nombre as libro_nombre,
    l.autor,
    l.foto_libro
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
    <link rel="stylesheet" href="../../../public/css/solicitud.css">
    <title>Panel de Autorizados - Bibliotecario</title>
    <script src="../../../public/js/modalDevolucion.js"></script>
</head>
<body>
    
    <h2>BIBLIOTECARIO - Autorizados</h2>
    
    <?php if (isset($mensaje)): ?>
        <?php 
        $estilo_mensaje = "padding: 10px; border: 1px solid; margin: 10px 0; border-radius: 5px; font-weight: bold;";
        $color_fondo = "#01050B"; // verde por defecto
        $color_borde = "#28a745";
        $icono = "✅";
        
        if (isset($mensaje_tipo)) {
            switch($mensaje_tipo) {
                case 'success':
                    $color_fondo = "#01050B";
                    $color_borde = "#28a745";
                    $icono = "✅";
                    break;
                case 'error':
                    $color_fondo = "#01050B";
                    $color_borde = "#dc3545";
                    $icono = "❌";
                    break;
                case 'warning':
                    $color_fondo = "#01050B";
                    $color_borde = "#ffc107";
                    $icono = "⚠️";
                    break;
            }
        }
        ?>
        <div style="<?php echo $estilo_mensaje; ?> background-color: <?php echo $color_fondo; ?>; border-color: <?php echo $color_borde; ?>;">
            <?php echo $icono . " " . htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div>
        <form method="GET" action="">
            <div>
                <div>
                    <label for="buscar" style="font-weight: bold;">Buscar:</label>
                    <input 
                        type="text" 
                        id="buscar" 
                        name="buscar" 
                        placeholder="CI, Ticket, Apellido o Nombre..." 
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                    >
                </div>
                
                <div>
                    <label for="fecha_inicio" style="font-weight: bold;">Desde:</label>
                    <input 
                        type="date" 
                        id="fecha_inicio" 
                        name="fecha_inicio" 
                        value="<?php echo htmlspecialchars($fecha_inicio); ?>"
                    >
                </div>
                
                <div>
                    <label for="fecha_fin" style="font-weight: bold;">Hasta:</label>
                    <input 
                        type="date" 
                        id="fecha_fin" 
                        name="fecha_fin" 
                        value="<?php echo htmlspecialchars($fecha_fin); ?>"
                    >
                </div>
                
                <button type="submit">
                    Buscar
                </button>
                
                <?php if (!empty($busqueda) || !empty($fecha_inicio) || !empty($fecha_fin)): ?>
                    <a href="?">
                        <button type="button">
                            Limpiar
                        </button>
                    </a>
                <?php endif; ?>
            </div>
            
            <div>
                <span>
                <a href="?fecha_inicio=<?php echo date('Y-m-d'); ?>" style="text-decoration: none; margin-right: 8px;">
                    <button type="button">
                        Hoy
                    </button>
                </a>
                <a href="?fecha_inicio=<?php echo date('Y-m-d', strtotime('-7 days')); ?>&fecha_fin=<?php echo date('Y-m-d'); ?>" style="text-decoration: none; margin-right: 8px;">
                    <button type="button">
                        Últimos 7 días
                    </button>
                </a>
                <a href="?fecha_inicio=<?php echo date('Y-m-01'); ?>&fecha_fin=<?php echo date('Y-m-d'); ?>" style="text-decoration: none;">
                    <button type="button">
                        Este mes
                    </button>
                </a>
            </div>
        </form>
    </div>

    <h3>Lista de Solicitudes de Reserva</h3>
    
    <table border="1">
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
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['fichaReserva']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['CI']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['libro_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['autor']); ?></td>
                        <td><?php echo $reserva['fechaReserva'] ? date('d/m/Y H:i', strtotime($reserva['fechaReserva'])) : 'N/A'; ?></td>
                        <td><?php echo $reserva['fechaDevolucion'] ? date('d/m/Y H:i', strtotime($reserva['fechaDevolucion'])) : 'N/A'; ?></td>
                        <td>
                            <button type="button" 
                                onclick="abrirModalDevolucion(
                                    '<?php echo $reserva['id_reserva']; ?>',
                                    '<?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido'], ENT_QUOTES); ?>',
                                    '<?php echo $reserva['CI']; ?>',
                                    '<?php echo htmlspecialchars($reserva['libro_nombre'], ENT_QUOTES); ?>',
                                    '<?php echo htmlspecialchars($reserva['autor'], ENT_QUOTES); ?>',
                                    '<?php echo $reserva['fotografia'] ? '../../../public/images/usersFotos/' . $reserva['fotografia'] : '../../../public/images/usersFotos/default-user.svg'; ?>',
                                    '<?php echo $reserva['foto_libro'] ? '../../../public/images/booksPortada/' . $reserva['foto_libro'] : '../../../public/images/booksPortada/default-book.svg'; ?>'
                                )">
                                Registrar Devo
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                <input type="hidden" name="accion" value="bloquear">
                                <button type="submit" font-size: 12px;" onclick="return confirm('¿Bloquear esta reserva?')">Bloquear</button>
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
    
    <div id="modalDevolucion" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 500px; border-radius: 10px; position: relative;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="color: #007bff; margin-bottom: 5px;">Registrar devolución</h2>
                <p style="margin: 0; color: #666; font-size: 14px;">Datos del libro</p>
            </div>
            
            <div style="display: flex; gap: 20px; margin-bottom: 20px;">

                <div style="text-align: center;">
                    <div style="width: 80px; height: 100px; border: 2px solid #ddd; border-radius: 5px; overflow: hidden; background-color: #f8f9fa;">
                        <img id="fotoLibro" src="" alt="Portada del libro" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Foto libro</p>
                </div>
                

                <div style="flex: 1;">
                    <p style="margin: 5px 0; font-weight: bold;">Nombre: <span id="nombreLibro" style="font-weight: normal;"></span></p>
                    <p style="margin: 5px 0; font-weight: bold;">Autor: <span id="autorLibro" style="font-weight: normal;"></span></p>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <p style="margin: 5px 0; color: #666; font-size: 14px;">Datos del lector</p>
                
                <div style="display: flex; gap: 20px; align-items: center;">

                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; border: 2px solid #ddd; border-radius: 5px; overflow: hidden; background-color: #f8f9fa;">
                            <img id="fotoUsuario" src="" alt="Foto del usuario" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Foto lector</p>
                    </div>
                    

                    <div style="flex: 1;">
                        <p style="margin: 5px 0; font-weight: bold;">Nombre: <span id="nombreUsuario" style="font-weight: normal;"></span></p>
                        <p style="margin: 5px 0; font-weight: bold;">Apellido: <span id="apellidoUsuario" style="font-weight: normal;"></span></p>
                        <p style="margin: 5px 0; font-weight: bold;">CI: <span id="ciUsuario" style="font-weight: normal;"></span></p>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <form id="formDevolucion" method="POST" style="display: inline;">
                    <input type="hidden" name="id_reserva" id="idReservaModal" value="">
                    <input type="hidden" name="accion" value="registrar_devolucion">
                    <button type="submit" class="button-success" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; margin-right: 10px; cursor: pointer; border-radius: 5px; font-size: 14px;">
                        Registrar devolución
                    </button>
                </form>
                <button type="button" class="button-cancel" onclick="cerrarModalDevolucion()" style="background-color: #6c757d; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-size: 14px;">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

</body>
</html>
