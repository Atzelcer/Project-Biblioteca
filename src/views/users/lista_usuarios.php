<?php
require_once '../../../src/config/conexion.php';

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if (!empty($buscar)) {
    $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE nombre LIKE '%$buscar%' OR CI LIKE '%$buscar%' ORDER BY nombre ASC");
} else {
    $query = mysqli_query($conexion, "SELECT * FROM usuario ORDER BY nombre ASC");
}

$usuarios = [];
while($row = mysqli_fetch_assoc($query)) {
    $usuarios[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Usuarios - Bibliotecario</title>
</head>
<body>
    <h1>Gestión de Usuarios</h1>
    
    <form method="GET">
        <input type="text" name="buscar" placeholder="Buscar usuario" value="<?= htmlspecialchars($buscar ?? '') ?>">
        <button type="submit">Buscar</button>
    </form>
    
    <br><br>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Estado</th>
                <th>Información</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="5">No se encontraron usuarios</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['CI']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                    <td><?= ucfirst($usuario['rol']) ?></td>
                    <td>
                        <button onclick="window.location.href='detalle_usuario.php?id=<?= $usuario['id_usuario'] ?>'">Ver más info</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
