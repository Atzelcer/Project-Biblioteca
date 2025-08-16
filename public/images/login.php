<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Biblioteca</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    
    <form method="POST" action="login.php">
        <label for="ci">CI:</label><br>
        <input type="text" name="CI" id="CI" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Iniciar sesión</button>
    </form>
    
    <p>No tienes cuenta? <a href="register.php">Regístrate</a></p>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once '../../src/config/database.php';
        
        $ci = $_POST['CI'];
        $password = sha1($_POST['password']);

        $stmt = $conexion->prepare('SELECT id_usuario, CI, nombre, apellido, rol FROM usuario WHERE CI = ? AND password = ?');
        $stmt->bind_param('is', $ci, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            session_start();
            $row = $result->fetch_assoc();
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['CI'] = $ci;
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['apellido'] = $row['apellido'];
            $_SESSION['rol'] = $row['rol'];
            
            if ($_SESSION['rol'] === 'lector') {
                header('Location: ../../src/views/books/LectorPanel.php');
                exit();
            } elseif ($_SESSION['rol'] === 'bibliotecario') {
                header('Location: ../../src/views/books/BibliotecarioPanel-Principal.php');
                exit();
            }
        } else {
            echo "<p style='color: red;'>CI o contraseña incorrectos</p>";
        }
    }
    ?>
</body>
</html>
