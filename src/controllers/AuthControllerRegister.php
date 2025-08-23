<?php
require_once '../config/database.php';
require_once '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User($conexion);
    
    $data = [
        'ci' => $_POST['ci'],
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'rol' => 'lector',
        'celular' => $_POST['celular'] ?? '',
        'password' => $_POST['password'],
        'direccion' => $_POST['direccion'] ?? '',
        'correo' => $_POST['correo']
    ];

    $resultado = $user->crear($data);

    if ($resultado) {
        echo "<h2>Usuario registrado exitosamente!</h2>";
        echo "<p>Nombre: " . htmlspecialchars($data['nombre']) . " " . htmlspecialchars($data['apellido']) . "</p>";
        echo "<p>CI: " . htmlspecialchars($data['ci']) . "</p>";
        echo "<p>Rol: " . $data['rol'] . "</p>";
        echo "<br><a href='../views/auth/login.php'>Ir al Login</a>";
        echo " | <a href='../views/auth/register.php'>Registrar otro usuario</a>";
    } else {
        echo "<h2>Error al registrar usuario</h2>";
        echo "<p>Posibles causas: CI duplicado, datos incorrectos</p>";
        echo "<br><a href='../views/auth/register.php'>Volver al registro</a>";
    }
} else {
    header("Location: ../views/auth/register.php");
}
?>
