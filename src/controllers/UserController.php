<?php
include('../config/verificarSesion.php');

// require "..\config\autenticar.php";
class UserController {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarUsuarios() {
        $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
        
        if (!empty($buscar)) {
            $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE nombre LIKE '%$buscar%' OR CI LIKE '%$buscar%' ORDER BY nombre ASC");
        } else {
            $query = mysqli_query($this->conexion, "SELECT * FROM usuario ORDER BY nombre ASC");
        }
        
        $usuarios = [];
        while($row = mysqli_fetch_assoc($query)) {
            $usuarios[] = $row;
        }
        
        include '/../views/users/lista_usuarios.php';
    }

    public function verUsuario($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE id_usuario = $id");
        $usuario = mysqli_fetch_assoc($query);
        
        if (!$usuario) {
            echo "Usuario no encontrado";
            exit;
        }
        
        include '/../views/users/detalle_usuario.php';
    }

    public function obtenerUsuarios($buscar = '') {
        if (!empty($buscar)) {
            $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE nombre LIKE '%$buscar%' OR CI LIKE '%$buscar%' ORDER BY nombre ASC");
        } else {
            $query = mysqli_query($this->conexion, "SELECT * FROM usuario ORDER BY nombre ASC");
        }
        
        $usuarios = [];
        while($row = mysqli_fetch_assoc($query)) {
            $usuarios[] = $row;
        }
        
        return $usuarios;
    }

    public function obtenerUsuario($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE id_usuario = $id");
        return mysqli_fetch_assoc($query);
    }
}
