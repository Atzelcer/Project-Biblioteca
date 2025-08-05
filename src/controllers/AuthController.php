<?php
require_once __DIR__ . '/../models/User.php';
session_start();

class AuthController {
    private $user;

    public function __construct($conexion) {
        $this->user = new User($conexion);
    }

    public function login() {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function procesarLogin() {
        $ci = $_POST['ci'];
        $password = $_POST['password'];
        $usuario = $this->user->autenticarPorCI($ci, $password);

        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: /Project-Biblioteca/public/dashboard");
        } else {
            echo "CI o contraseña incorrectos.";
        }
    }

    public function register() {
        require __DIR__ . '/../views/auth/register.php';
    }

    public function procesarRegistro() {
        $data = [
            'ci' => $_POST['ci'],
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'rol' => 'lector',
            'celular' => $_POST['celular'],
            'password' => $_POST['password'],
            'direccion' => $_POST['direccion'],
            'correo' => $_POST['correo']
        ];

        $ok = $this->user->crear($data);

        if ($ok) {
            header("Location: /Project-Biblioteca/public/login");
        } else {
            echo "Error al registrar usuario.";
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /Project-Biblioteca/public/login");
    }
}
