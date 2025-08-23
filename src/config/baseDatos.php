<?php
$conexion = new mysqli("localhost", "root", "", "biblioteca_bd");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
