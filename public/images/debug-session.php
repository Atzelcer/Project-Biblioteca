<?php
session_start();
echo "<h3>Debug de Sesión</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Información de Usuario desde BD</h3>";
if (isset($_SESSION['CI'])) {
    require_once '../src/config/database.php';
    $ci = $_SESSION['CI'];
    $sql = "SELECT * FROM usuario WHERE CI = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $ci);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    } else {
        echo "No se encontró usuario con CI: " . $ci;
    }
} else {
    echo "No hay CI en la sesión";
}
?>
<a href="login.php">Volver al Login</a>
