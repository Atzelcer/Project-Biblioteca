<?php
require_once '../src/config/database.php';

echo "<h2>Usuarios en la Base de Datos</h2>";

$query = "SELECT CI, nombre, apellido, rol FROM usuario ORDER BY rol, CI";
$result = $conexion->query($query);

if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>CI</th><th>Nombre</th><th>Apellido</th><th>Rol</th><th>Login Test</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['CI'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['apellido'] . "</td>";
        echo "<td>" . $row['rol'] . "</td>";
        echo "<td>CI=" . $row['CI'] . ", Pass=" . $row['CI'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><p>Total usuarios: " . $result->num_rows . "</p>";
} else {
    echo "<p>Error: " . $conexion->error . "</p>";
}

echo "<br><a href='login.php'>Ir al Login</a> | <a href='register.php'>Registrar Usuario</a>";
?>
