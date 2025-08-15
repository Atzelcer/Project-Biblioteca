<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso</title>
    <meta http-equiv="refresh" content="3;url=login.php">
</head>
<body>
    <h1>¡Usuario registrado exitosamente!</h1>
    
    <h3>Datos del usuario:</h3>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($_GET['nombre'] ?? 'No especificado'); ?> <?php echo htmlspecialchars($_GET['apellido'] ?? ''); ?></p>
    <p><strong>CI:</strong> <?php echo htmlspecialchars($_GET['ci'] ?? 'No especificado'); ?></p>
    <p><strong>Correo:</strong> <?php echo htmlspecialchars($_GET['correo'] ?? 'No especificado'); ?></p>
    
    <p>Serás redirigido al login en <span id="contador">3</span> segundos...</p>
    <p>O puedes hacer clic <a href="login.php">aquí para ir al login ahora</a></p>

    <script>
        let contador = 3;
        const elementoContador = document.getElementById('contador');
        
        const intervalo = setInterval(function() {
            contador--;
            elementoContador.textContent = contador;
            
            if (contador <= 0) {
                clearInterval(intervalo);
                window.location.href = 'login.php';
            }
        }, 1000);
    </script>
</body>
</html>
