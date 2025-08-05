<form method="POST" action="procesar-login">
    <label for="ci">CI:</label><br>
    <input type="text" name="ci" pattern="[0-9]+" title="Solo números" maxlength="15" required><br><br>

    <label for="password">Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Iniciar sesión</button>
</form>
<p>No tienes cuenta? <a href="register">Regístrate</a></p>