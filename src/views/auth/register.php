<form method="POST" action="procesar-registro">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br>

    <label>Apellido:</label><br>
    <input type="text" name="apellido" required><br>

    <label>CI:</label><br>
    <input type="text" name="ci" pattern="[0-9]+" title="Solo números" maxlength="15" required><br>

    <label>Celular:</label><br>
    <input type="text" name="celular" pattern="[0-9]+" title="Solo números" maxlength="15"><br>

    <label>Correo:</label><br>
    <input type="email" name="correo" required><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br>

    <label>Dirección:</label><br>
    <input type="text" name="direccion"><br><br>

    <button type="submit">Registrar</button>
</form>
<p>Ya tienes cuenta? <a href="login">Inicia sesión</a></p>
