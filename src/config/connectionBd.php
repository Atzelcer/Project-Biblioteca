<?php
// parametros 
$servername = "localhost"; 
$username = "root";        // Nombre de usuario 
$password = "";            // sin contraseña
$dbname = "biblioteca_bd";    // El nombre de la base de datos 

// Crear la conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// verificar la conexion 
if($conexion->connect_error) {
    die("Fallo en la conexión a la base de datos: " . $conexion->connect_error);
}

?>