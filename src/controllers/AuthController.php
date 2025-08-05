<?php
session_start();
include("../config/database.php");
include("../config/verificarSesion.php");

$nombres = $_POST["nombre"];
$apellidos = $_POST["apellido"];
$ci = $_POST["ci"];
$celular = $_POST["celular"];
$correo = $_POST["correo"];
$password = sha1($_POST["password"]);
$direccion = $_POST["direccion"];
$rol = "lector";

$stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido, CI, celular, correo, password, direccion, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $nombres, $apellidos, $ci, $celular, $correo, $password, $direccion, $rol);

if ($stmt->execute()) {
    header("Location: ../views/auth/login.php");
    exit();
}