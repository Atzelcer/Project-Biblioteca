<?php
session_start();
include("../../config/verificarSesion.php");

// Verificar que se recibieron todos los parámetros necesarios
if (!isset($_GET['ticket']) || !isset($_GET['libro']) || !isset($_GET['usuario'])) {
    die("Parámetros de ticket incompletos.");
}

$ticket = htmlspecialchars($_GET['ticket']);
$libro = htmlspecialchars($_GET['libro']);
$usuario = htmlspecialchars($_GET['usuario']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Reserva</title>
    <meta http-equiv="refresh" content="5;url=LectorPanel.php">
</head>
<body>
    <div style="border: 3px solid black; padding: 30px; margin: 50px auto; max-width: 600px; text-align: center;">
        <h1>TICKET DE RESERVA</h1>
        
        <div style="font-size: 48px; font-weight: bold; margin: 30px 0; color: #000;">
            <?php echo $ticket; ?>
        </div>
        
        <h2>Información de la Reserva</h2>
        <p><strong>Libro:</strong> <?php echo $libro; ?></p>
        <p><strong>Usuario:</strong> <?php echo $usuario; ?></p>
        <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
        
        <hr>
        
        <p><strong>IMPORTANTE:</strong></p>
        <p>Presenta este código en biblioteca para retirar tu libro</p>
        <p>Horarios de atención: Lunes a Viernes 8:00-12:00 y 14:00-18:00</p>
        
        <p style="margin-top: 30px;">
            Serás redirigido al panel principal en <span id="contador">5</span> segundos...
        </p>
        
        <a href="LectorPanel.php">
            <button type="button">Cerrar</button>
        </a>
    </div>

    <script>
        let contador = 5;
        const elementoContador = document.getElementById('contador');
        
        const intervalo = setInterval(function() {
            contador--;
            elementoContador.textContent = contador;
            
            if (contador <= 0) {
                clearInterval(intervalo);
                window.location.href = 'LectorPanel.php';
            }
        }, 1000);
    </script>
</body>
</html>
