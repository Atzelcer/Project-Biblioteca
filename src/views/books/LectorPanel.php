<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/verificarSesion.php';

class BookRepository {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    public function findBooks($search = '') {
        $sql = "SELECT id_libro, nombre, autor, estado FROM libros WHERE estado = 'Disponible'";
        
        if (!empty($search)) {
            $search = $this->conexion->real_escape_string($search);
            $sql .= " AND (nombre LIKE '%{$search}%' OR autor LIKE '%{$search}%')";
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

$repository = new BookRepository($conexion);
$search = $_GET['search'] ?? '';

$libros = $repository->findBooks($search);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Lector</title>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>LECTOR - Bienvenido <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></h1>
        <a href="../../config/logout.php">
            <button type="button">Cerrar Sesión</button>
        </a>
    </div>
    
    <?php if (isset($_GET['message'])): ?>
        <div style="padding: 10px; border: 1px solid #ccc; margin: 10px 0; background-color: #e6ffe6;">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    
    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Buscar por nombre o autor" 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                   style="padding: 8px 12px; width: 300px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; cursor: pointer;">BUSCAR</button>
        </form>
    </div>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 8px;">Nombre</th>
                <th style="padding: 8px;">Autor</th>
                <th style="padding: 8px;">Disponibilidad</th>
                <th style="padding: 8px;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($libros)): ?>
                <?php foreach ($libros as $libro): ?>
                    <tr>
                        <td style="padding: 8px;">
                            <?php echo htmlspecialchars($libro['nombre']); ?>
                        </td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($libro['autor']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($libro['estado']); ?></td>
                        <td style="padding: 8px;">
                            <!-- <?php //if ($libro['estado'] == 'Disponible'): ?> -->
                                <!-- <a href="reservation.php?id=<?php //echo $libro['id_libro']; ?>"> -->
                                    <!-- <button type="button">Reservar</button> -->
                                <!-- </a> -->
                            <!-- <?php //elseif ($libro['estado'] == 'Reservado'): ?> -->
                                <!-- <button type="button" disabled>Reservado</button> -->
                            <!-- <?php //else: ?> -->
                                <!-- <button type="button" disabled>No disponible</button> -->
                            <!-- <?php //endif; ?> -->

                            <button type="button" onclick="abrirModalReserva(<?php echo $libro['id_libro']; ?>)" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;">Reservar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 8px; text-align: center;">No se encontraron libros</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal de Reserva -->
    <div id="reservaModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 500px; border-radius: 5px;">
            <h3 style="margin-top: 0;">Reservar Libro</h3>
            
            <div id="infoLibro" style="background-color: #f8f9fa; padding: 15px; border-radius: 3px; margin: 15px 0;">
                <p>Cargando información del libro...</p>
            </div>
            
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 3px; margin: 15px 0;">
                <h4 style="margin: 0 0 10px 0;">HORARIOS DE ATENCIÓN</h4>
                <p style="margin: 5px 0;">Lunes a Viernes de 8:00 a 12:00</p>
                <p style="margin: 5px 0;">y de 14:00 a 18:00</p>
                <p style="margin: 10px 0 0 0; font-weight: bold;">Horario válido: Podrás recoger el libro hoy o en días hábiles empezando desde el 20/08/2025</p>
            </div>
            
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="cerrarModalReserva()" style="background-color: #6c757d; color: white; border: none; padding: 10px 20px; margin-right: 10px; cursor: pointer; border-radius: 3px;">
                    Cancelar
                </button>
                <button type="button" id="confirmarReserva" onclick="confirmarReserva()" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px;">
                    Reservar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal del Ticket -->
    <div id="ticketModal" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 600px; border-radius: 5px; max-height: 90%; overflow-y: auto;">
            <div id="ticketContent">
                <!-- El contenido del ticket se generará dinámicamente -->
            </div>
            
            <div style="text-align: center; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="button" onclick="imprimirTicket()" style="background-color: #007bff; color: white; border: none; padding: 10px 20px; margin-right: 10px; cursor: pointer; border-radius: 3px;">
                    📄 Imprimir
                </button>
                <button type="button" onclick="cerrarTicketModal()" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px;">
                    ✅ Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Script externo para el modal -->
    <script src="../../../public/js/modalReserva.js"></script>
</body>
</html>
