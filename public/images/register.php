<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Biblioteca</title>
</head>
<body>
    <h1>Registrar Usuario</h1>
    
    <form method="POST" action="register.php">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Apellido:</label><br>
        <input type="text" name="apellido" required><br><br>

        <label>CI:</label><br>
        <input type="text" name="ci" pattern="[0-9]+" title="Solo números" required><br><br>

        <label>Celular:</label><br>
        <input type="text" name="celular" pattern="[0-9]+"><br><br>

        <label>Correo:</label><br>
        <input type="email" name="correo" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccion"><br><br>

        <button type="submit">Registrar</button>
    </form>
    
    <p>Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once '../src/config/database.php';
        
        $ci = trim($_POST['ci']);
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $celular = trim($_POST['celular']) ?? '';
        $correo = trim($_POST['correo']);
        $password = sha1($_POST['password']);
        $direccion = trim($_POST['direccion']) ?? '';

        // Validar campos obligatorios
        if (empty($ci) || empty($nombre) || empty($apellido) || empty($correo) || empty($_POST['password'])) {
            echo "<h3 style='color: red;'>Error: Todos los campos obligatorios deben ser completados</h3>";
            echo "<p><a href='register.php'>Intentar nuevamente</a></p>";
            exit();
        }

        // Verificar si el CI ya existe
        $check_ci_sql = "SELECT CI FROM usuario WHERE CI = ?";
        $check_ci_stmt = $conexion->prepare($check_ci_sql);
        $check_ci_stmt->bind_param('s', $ci);
        $check_ci_stmt->execute();
        $result_ci = $check_ci_stmt->get_result();
        
        if ($result_ci->num_rows > 0) {
            echo "<h3 style='color: red;'>Error: La cédula de identidad ya está registrada</h3>";
            echo "<p>El CI <strong>" . htmlspecialchars($ci) . "</strong> ya existe en el sistema.</p>";
            echo "<p><a href='register.php'>Intentar nuevamente</a></p>";
            exit();
        }

        // Verificar si el correo ya existe
        $check_correo_sql = "SELECT correo FROM usuario WHERE correo = ?";
        $check_correo_stmt = $conexion->prepare($check_correo_sql);
        $check_correo_stmt->bind_param('s', $correo);
        $check_correo_stmt->execute();
        $result_correo = $check_correo_stmt->get_result();
        
        if ($result_correo->num_rows > 0) {
            echo "<h3 style='color: red;'>Error: El correo electrónico ya está registrado</h3>";
            echo "<p>El correo <strong>" . htmlspecialchars($correo) . "</strong> ya existe en el sistema.</p>";
            echo "<p><a href='register.php'>Intentar nuevamente</a></p>";
            exit();
        }

        // Si no hay duplicados, proceder con el registro
        $sql = "INSERT INTO usuario (fotografia, CI, nombre, apellido, rol, celular, password, direccion, correo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        
        // Definir variables para los valores
        $foto_vacia = NULL;
        $rol = 'lector';
        
        $stmt->bind_param('sisssssss', $foto_vacia, $ci, $nombre, $apellido, $rol, $celular, $password, $direccion, $correo);
        
        if ($stmt->execute()) {
            // Redirigir a página de éxito con los datos del usuario
            $params = http_build_query([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'ci' => $ci,
                'correo' => $correo
            ]);
            header("Location: registro-exitoso.php?" . $params);
            exit();
        } else {
            echo "<h3 style='color: red;'>Error al registrar usuario</h3>";
            echo "<p>Error técnico: " . htmlspecialchars($conexion->error) . "</p>";
            echo "<p><a href='register.php'>Intentar nuevamente</a></p>";
        }
    }
    ?>
</body>
</html>
