<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/css/form.css">
    <title>Registro</title>
</head>
<body>
    <img src="../../../public/images/fondo.webp" class="fondo">
    <div class="formulario">
        <h1>Registro</h1>
        <form method="POST" action="../../config/procesar_registro.php">
            <div class="datos">
                <div class="dato">
                    <label>Nombre:</label>
                    <input class="login-input" type="text" name="nombre" required>
                </div>
                <div class="dato">
                    <label>Apellido:</label>
                    <input class="login-input" type="text" name="apellido" required>
                </div>
            </div>
            <div class="datos">
                <div class="dato">
                    <label>CI:</label>
                    <input class="login-input" type="text" name="ci" pattern="[0-9]+" title="Solo números" maxlength="15" required>
                </div>
                <div class="dato">
                    <label>Celular:</label>
                    <input class="login-input" type="text" name="celular" pattern="[0-9]+" title="Solo números" maxlength="15">
                </div>
            </div>
            <div class="datos">
                <div class="dato">
                    <label>Correo:</label>
                    <input class="login-input" type="email" name="correo" required>
                </div>
                <div class="dato">
                    <label>Contraseña:</label>
                    <input class="login-input" type="password" name="password" required>
                </div>
            </div>
            <div class="datos">
                <div class="dato">
                    <label>Dirección:</label>
                    <input class="login-input" type="text" name="direccion">
                </div>
            </div>
            
            <p><em>Los siguientes campos son opcionales:</em></p>
            <div class="datos">
                <div class="file-upload">
                    <label for="ci_anverso" class="custom-file-label">Subir CI anverso</label>
                    <input type="file" id="ci_anverso" name="ci_anverso" onchange="mostrarNombre(this, 'file-anverso')">
                    <span id="file-anverso" class="file-name"></span>
                </div>

                <div class="file-upload">
                    <label for="ci_reverso" class="custom-file-label">Subir CI reverso</label>
                    <input type="file" id="ci_reverso" name="ci_reverso" onchange="mostrarNombre(this, 'file-reverso')">
                    <span id="file-reverso" class="file-name"></span>
                </div>

                <div class="file-upload">
                    <label for="foto_perfil" class="custom-file-label">Subir foto de perfil</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" onchange="mostrarNombre(this, 'file-perfil')">
                    <span id="file-perfil" class="file-name"></span>
                </div>
            </div>
            
            <button type="submit">Registrar</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>

