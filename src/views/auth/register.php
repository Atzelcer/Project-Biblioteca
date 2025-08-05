<form method="POST" action="../../controllers/AuthController.php">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br>

    <label>Apellido:</label><br>
    <input type="text" name="apellido" required><br>

    <label>CI:</label><br>
    <input type="text" name="ci" required><br>

    <label>Celular:</label><br>
    <input type="text" name="celular"><br>

    <label>Correo:</label><br>
    <input type="email" name="correo" required><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br>

    <label>Dirección:</label><br>
    <input type="text" name="direccion"><br><br>

    <button type="submit">Registrar</button>
</form>
<p>Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
