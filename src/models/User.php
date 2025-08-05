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
}
