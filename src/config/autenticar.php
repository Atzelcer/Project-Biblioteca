<?php
session_start();
include("database.php");

if (isset($_POST['CI']) && isset($_POST['password'])) {
    $ci = $_POST['CI'];
    $password = sha1($_POST['password']);

    $stmt = $conexion->prepare('SELECT CI, rol FROM usuario WHERE ci = ? AND password = ?');
    $stmt->bind_param('ss', $ci, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['CI'] = $ci;
        $_SESSION['rol'] = $result->fetch_assoc()['rol'];
        if ($_SESSION['rol'] === 'lector') {
            header('Location: ../controllers/books_controller.php?action=catalog_reader"');
        } elseif ($_SESSION['rol'] === 'bibliotecario') {
            header('Location: ../controllers/AuthController.php?action=catalog_librarian');
        } else {
            echo "Rol no reconocido.";
        }
    } else {
        header('Location: ../views/auth/login.php');
        exit();
    }
}
?>