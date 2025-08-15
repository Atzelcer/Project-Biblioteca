<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/css/form.css">
    <title>Document</title>
</head>
<body>
    <img src="../../../public/images/fondo.webp" class="fondo">
    <div class="formulario">
        <form method="POST" action="../../config/autenticar.php" class="login">
            <h1>Iniciar Sesión</h1>
            <label for="ci">CI:</label><br>
            <input class="login-input" type="text" name="CI" id="CI" required><br><br>
    
            <label for="password">Contraseña:</label><br>
            <input class="login-input" type="password" name="password" id="password" required><br><br>
    
            <button type="submit">Iniciar sesión</button>
        </form>
        <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
    </div>
</body>
</html>
