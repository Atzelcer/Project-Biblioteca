<?php
class User {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function crear($data) {
        $sql = "INSERT INTO usuario (CI, nombre, apellido, rol, celular, password, direccion, correo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $hash = sha1($data['password']);
        $stmt->bind_param("isssisss", $data['ci'], $data['nombre'], $data['apellido'], $data['rol'],
                          $data['celular'], $hash, $data['direccion'], $data['correo']);
        return $stmt->execute();
    }

    public function autenticarPorCI($ci, $password) {
        $sql = "SELECT * FROM usuario WHERE CI = ? AND password = ?";
        $stmt = $this->db->prepare($sql);
        $hash = sha1($password);
        $stmt->bind_param("is", $ci, $hash);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function obtenerTodos($buscar = '') {
        $sql = "SELECT * FROM usuario";
        if (!empty($buscar)) {
            $sql .= " WHERE CI LIKE ? OR nombre LIKE ? OR apellido LIKE ?";
        }
        $sql .= " ORDER BY nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($buscar)) {
            $search = "%{$buscar}%";
            $stmt->bind_param("sss", $search, $search, $search);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
