<?php
session_start();
require_once '../config/database.php';
include('../config/verificarSesion.php');

class BookRepository {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    public function findBooks($search = '', $includeCode = false) {
        $fields = $includeCode 
            ? "id_libro, codigo_libro, nombre, autor, estado, tiempo_restante"
            : "id_libro, nombre, autor, estado";
            
        $sql = "SELECT {$fields} FROM libros";
        
        if (!empty($search)) {
            $search = $this->conexion->real_escape_string($search);
            $searchFields = $includeCode 
                ? "codigo_libro LIKE '%{$search}%' OR nombre LIKE '%{$search}%' OR autor LIKE '%{$search}%'"
                : "nombre LIKE '%{$search}%' OR autor LIKE '%{$search}%'";
            $sql .= " WHERE {$searchFields}";
        }
        
        $orderBy = $includeCode ? "codigo_libro" : "nombre";
        $sql .= " ORDER BY {$orderBy} ASC";
        
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function getBooks($search = '') {
        return $this->findBooks($search, false);
    }
    
    public function getBookById($id) {
        $id = $this->conexion->real_escape_string($id);
        $sql = "SELECT id_libro, nombre, autor, estado FROM libros WHERE id_libro = '{$id}'";
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }
    
    public function reserveBook($id_libro, $id_usuario) {
        $id_libro = $this->conexion->real_escape_string($id_libro);
        $id_usuario = $this->conexion->real_escape_string($id_usuario);
        
        // Verificar que el libro esté disponible
        $libro = $this->getBookById($id_libro);
        if (!$libro || $libro['estado'] !== 'Disponible') {
            return false;
        }
        
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

class BookService {
    private $repository;
    
    public function __construct(BookRepository $repository) {
        $this->repository = $repository;
    }
    
    public function getReaderCatalog($search = '') {
        return $this->repository->findBooks($search, false);
    }
    
    public function getLibrarianCatalog($search = '') {
        return $this->repository->findBooks($search, true);
    }
}

class BooksController {
    private $service;
    
    public function __construct() {
        global $conexion;
        $repository = new BookRepository($conexion);
        $this->service = new BookService($repository);
    }
    
    public function handleRequest() {
        $action = $_GET['action'] ?? $_POST['action'] ?? 'catalog_reader';
        
        switch ($action) {
            case 'catalog_reader':
                $this->showReaderCatalog();
                break;
                
            case 'catalog_librarian':
                $this->showLibrarianCatalog();
                break;
                
            case 'getBookInfo':
                $this->getBookInfo();
                break;
                
            case 'reservar':
                $this->handleReservation();
                break;
                
            default:
                $this->showReaderCatalog();
                break;
        }
    }
    
    private function showReaderCatalog() {
        $search = $_GET['search'] ?? '';
        $libros = $this->service->getReaderCatalog($search);
        include '../views/books/LectorPanel-Principal.php';
    }
    
    private function showLibrarianCatalog() {
        $search = $_GET['search'] ?? '';
        $libros = $this->service->getLibrarianCatalog($search);
        include '../views/books/GestionReservas.php';
    }
    
    private function getBookInfo() {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de libro requerido']);
            return;
        }
        
        global $conexion;
        $repository = new BookRepository($conexion);
        $libro = $repository->getBookById($id);
        
        if ($libro) {
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
        header('Content-Type: application/json');
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
        
        global $conexion;
        $repository = new BookRepository($conexion);
        
        // Obtener información del libro antes de reservar
        $libro = $repository->getBookById($id_libro);
        if (!$libro) {
            echo json_encode(['success' => false, 'message' => 'Libro no encontrado']);
            return;
        }
        
        $result = $repository->reserveBook($id_libro, $id_usuario);
        
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
    
    
    private function redirectWithMessage($action, $message) {
        $search = $_GET['search'] ?? '';
        $url = "books_controller.php?action={$action}&search=" . urlencode($search) . "&message=" . urlencode($message);
        header("Location: {$url}");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new BooksController();
    $controller->handleRequest();
}
?>
