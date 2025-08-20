<?php
// Endpoint público para llamadas AJAX del modal de reservas
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Incluir archivos necesarios con rutas absolutas
require_once __DIR__ . '/../src/config/database.php';

class AjaxBookController {
    private $conexion;
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }
    
    public function handleRequest() {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'getBookInfo':
                $this->getBookInfo();
                break;
                
            case 'reservar':
                $this->handleReservation();
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida: ' . $action]);
                break;
        }
    }
    
    private function getBookInfo() {
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de libro requerido']);
            return;
        }
        
        $id = $this->conexion->real_escape_string($id);
        $sql = "SELECT id_libro, nombre, autor, estado FROM libros WHERE id_libro = '{$id}'";
        $result = $this->conexion->query($sql);
        
        if ($result && $libro = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'libro' => [
                    'nombre' => $libro['nombre'],
                    'autor' => $libro['autor'],
                    'estado' => $libro['estado']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Libro no encontrado con ID: ' . $id]);
        }
    }
    
    private function handleReservation() {
        $id_libro = $_POST['id'] ?? '';
        
        // Debug de sesión
        error_log('Sesión actual: ' . print_r($_SESSION, true));
        
        // Intentar múltiples variables de sesión para el ID del usuario
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        $ci_usuario = $_SESSION['CI'] ?? '';
        $nombre_usuario = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? $_SESSION['username'] ?? 'Usuario';
        
        if (empty($id_libro)) {
            echo json_encode(['success' => false, 'message' => 'ID de libro requerido']);
            return;
        }
        
        // Si no tenemos id_usuario, intentar obtenerlo usando CI
        if (empty($id_usuario) && !empty($ci_usuario)) {
            $id_usuario = $this->getUserIdByCI($ci_usuario);
        }
        
        if (empty($id_usuario)) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado. Sesión: ' . session_id() . '. Variables disponibles: ' . implode(', ', array_keys($_SESSION)) . '. Valores: ' . json_encode($_SESSION)]);
            return;
        }
        
        // Obtener información del libro antes de reservar
        $libro = $this->getLibroById($id_libro);
        if (!$libro) {
            echo json_encode(['success' => false, 'message' => 'Libro no encontrado']);
            return;
        }
        
        if ($libro['estado'] !== 'Disponible') {
            echo json_encode(['success' => false, 'message' => 'El libro no está disponible para reserva. Estado actual: ' . $libro['estado']]);
            return;
        }
        
        $result = $this->reserveBook($id_libro, $id_usuario);
        
        if ($result) {
            // $result contiene la ficha de reserva
            $codigo_reserva = $result;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Libro reservado exitosamente',
                'ticket' => [
                    'codigo_reserva' => $codigo_reserva,
                    'libro_nombre' => $libro['nombre'],
                    'libro_autor' => $libro['autor'],
                    'usuario_nombre' => $nombre_usuario,
                    'fecha_reserva' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al reservar el libro en la base de datos.']);
        }
    }
    
    private function getLibroById($id) {
        $id = $this->conexion->real_escape_string($id);
        $sql = "SELECT id_libro, nombre, autor, estado FROM libros WHERE id_libro = '{$id}'";
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }
    
    private function getUserIdByCI($ci) {
        $ci = $this->conexion->real_escape_string($ci);
        $sql = "SELECT id_usuario FROM usuario WHERE CI = '{$ci}'";
        $result = $this->conexion->query($sql);
        if ($result && $user = $result->fetch_assoc()) {
            return $user['id_usuario'];
        }
        return null;
    }
    
    private function reserveBook($id_libro, $id_usuario) {
        $id_libro = $this->conexion->real_escape_string($id_libro);
        $id_usuario = $this->conexion->real_escape_string($id_usuario);
        
        // Generar código de reserva único (fichaReserva)
        $ficha_reserva = 'R' . date('ymd') . str_pad($id_libro, 3, '0', STR_PAD_LEFT);
        
        // Realizar la reserva usando la estructura correcta de la tabla
        $fecha_reserva = date('Y-m-d H:i:s');
        
        // Insertar en reservas con la estructura correcta
        $sql = "INSERT INTO reservas (id_usuario, id_libro, fichaReserva, fechaReserva, estado) VALUES ('{$id_usuario}', '{$id_libro}', '{$ficha_reserva}', '{$fecha_reserva}', 'pendiente')";
        
        if ($this->conexion->query($sql)) {
            // Actualizar el estado del libro
            $update_sql = "UPDATE libros SET estado = 'Reservado' WHERE id_libro = '{$id_libro}'";
            if ($this->conexion->query($update_sql)) {
                return $ficha_reserva; // Devolver la ficha de reserva
            } else {
                error_log('Error al actualizar estado del libro: ' . $this->conexion->error);
                return false;
            }
        } else {
            error_log('Error al insertar reserva: ' . $this->conexion->error);
            return false;
        }
    }
}

// Procesar la petición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = new AjaxBookController();
        $controller->handleRequest();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido. Solo POST permitido.']);
}
?>
