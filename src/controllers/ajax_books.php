<?php
// Endpoint específico para llamadas AJAX del modal de reservas
header('Content-Type: application/json');
session_start();

// Incluir archivos necesarios
require_once __DIR__ . '/../config/database.php';

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
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
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
            echo json_encode(['success' => false, 'message' => 'Libro no encontrado']);
        }
    }
    
    private function handleReservation() {
        $id_libro = $_POST['id'] ?? '';
        
        // Verificar si la sesión está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $id_usuario = $_SESSION['id_usuario'] ?? '';
        $nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';
        
        if (empty($id_libro)) {
            echo json_encode(['success' => false, 'message' => 'ID de libro requerido']);
            return;
        }
        
        if (empty($id_usuario)) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado. Por favor, inicia sesión nuevamente.']);
            return;
        }
        
        // Obtener información del libro antes de reservar
        $libro = $this->getLibroById($id_libro);
        if (!$libro) {
            echo json_encode(['success' => false, 'message' => 'Libro no encontrado']);
            return;
        }
        
        if ($libro['estado'] !== 'Disponible') {
            echo json_encode(['success' => false, 'message' => 'El libro no está disponible para reserva']);
            return;
        }
        
        $result = $this->reserveBook($id_libro, $id_usuario);
        
        if ($result) {
            // Generar código de reserva único
            $codigo_reserva = 'RES-' . date('Ymd') . '-' . str_pad($id_libro, 4, '0', STR_PAD_LEFT) . '-' . str_pad($id_usuario, 4, '0', STR_PAD_LEFT);
            
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
            echo json_encode(['success' => false, 'message' => 'Error al reservar el libro. El libro podría no estar disponible.']);
        }
    }
    
    private function getLibroById($id) {
        $id = $this->conexion->real_escape_string($id);
        $sql = "SELECT id_libro, nombre, autor, estado FROM libros WHERE id_libro = '{$id}'";
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }
    
    private function reserveBook($id_libro, $id_usuario) {
        $id_libro = $this->conexion->real_escape_string($id_libro);
        $id_usuario = $this->conexion->real_escape_string($id_usuario);
        
        // Generar código de reserva único
        $codigo_reserva = 'RES-' . date('Ymd') . '-' . str_pad($id_libro, 4, '0', STR_PAD_LEFT) . '-' . str_pad($id_usuario, 4, '0', STR_PAD_LEFT);
        
        // Realizar la reserva
        $fecha_reserva = date('Y-m-d H:i:s');
        $sql = "INSERT INTO reservas (id_libro, id_usuario, fecha_reserva, estado, codigo_reserva) VALUES ('{$id_libro}', '{$id_usuario}', '{$fecha_reserva}', 'Activa', '{$codigo_reserva}')";
        
        if ($this->conexion->query($sql)) {
            // Actualizar el estado del libro
            $update_sql = "UPDATE libros SET estado = 'Reservado' WHERE id_libro = '{$id_libro}'";
            return $this->conexion->query($update_sql);
        }
        
        return false;
    }
}

// Procesar la petición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AjaxBookController();
    $controller->handleRequest();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
