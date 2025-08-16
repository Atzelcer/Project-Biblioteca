<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Bibliotecario</title>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>BIBLIOTECARIO</h1>
        <a href="../../config/logout.php">
            <button type="button">Cerrar Sesión</button>
        </a>
    </div>
    
    <div style="display: flex; justify-content: center; gap: 50px; margin-top: 100px;">
        <a href="GestionReservas.php">
            <button style="padding: 20px 40px; font-size: 18px;">Solicitudes de Reserva</button>
        </a>
        
        <a href="../users/GestionLectores.php">
            <button style="padding: 20px 40px; font-size: 18px;">Gestion de Lectores</button>
        </a>
    </div>
</body>
</html>
