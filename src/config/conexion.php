<?php
$conexion = mysqli_connect("localhost", "root", "", "biblioteca_bd");
if(mysqli_connect_errno()){
    echo "Error al conectarse con la base de datos";
    exit();
}
?>
