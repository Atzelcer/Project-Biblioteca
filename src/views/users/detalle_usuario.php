<?php
require_once '../../../src/config/conexion.php';

if (!isset($usuario) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE id_usuario = $id");
    $usuario = mysqli_fetch_assoc($query);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Información del Usuario</title>
</head>
<body>
    <h1>Información del Usuario</h1>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <td><strong>ID Usuario:</strong></td>
            <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
        </tr>
        <tr>
            <td><strong>CI:</strong></td>
            <td><?= htmlspecialchars($usuario['CI']) ?></td>
        </tr>
        <tr>
            <td><strong>Nombre:</strong></td>
            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
        </tr>
        <tr>
            <td><strong>Apellido:</strong></td>
            <td><?= htmlspecialchars($usuario['apellido']) ?></td>
        </tr>
        <tr>
            <td><strong>Rol:</strong></td>
            <td><?= ucfirst($usuario['rol']) ?></td>
        </tr>
        <tr>
            <td><strong>Celular:</strong></td>
            <td><?= $usuario['celular'] ? htmlspecialchars($usuario['celular']) : 'No registrado' ?></td>
        </tr>
        <tr>
            <td><strong>Correo:</strong></td>
            <td><?= htmlspecialchars($usuario['correo']) ?></td>
        </tr>
        <tr>
            <td><strong>Dirección:</strong></td>
            <td><?= $usuario['direccion'] ? htmlspecialchars($usuario['direccion']) : 'No registrada' ?></td>
        </tr>
       
    </table>
    
    <br>
    <button onclick="window.history.back()">Cerrar</button>
</body>
</html>
