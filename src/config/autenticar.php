<?php
session_start();
include("database.php");

if (isset($_POST['CI']) && isset($_POST['password'])) {
    $ci = $_POST['CI'];
    $password = sha1($_POST['password']);

    $stmt = $conexion->prepare('SELECT CI, rol FROM usuario WHERE CI = ? AND password = ?');
    $stmt->bind_param('is', $ci, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['CI'] = $ci;
        $_SESSION['rol'] = $row['rol'];
        
        if ($_SESSION['rol'] === 'lector') {
            header('Location: ../controllers/books_controller.php?action=catalog_reader');
        } elseif ($_SESSION['rol'] === 'bibliotecario') {
            header('Location: ../views/books/Bibliotecario.php');
        } else {
            echo "Rol no reconocido.";
        }
        exit();
    } else {
        echo "CI o contraseña incorrectos";
        echo "<br><a href='../views/auth/login.php'>Volver al login</a>";
    }
}
?>