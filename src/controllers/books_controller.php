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
                
            default:
                $this->showReaderCatalog();
                break;
        }
    }
    
    private function showReaderCatalog() {
        $search = $_GET['search'] ?? '';
        $libros = $this->service->getReaderCatalog($search);
        include '../views/books/catalog.php';
    }
    
    private function showLibrarianCatalog() {
        $search = $_GET['search'] ?? '';
        $libros = $this->service->getLibrarianCatalog($search);
        include '../views/books/admin.php';
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
