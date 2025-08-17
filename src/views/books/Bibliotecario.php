<?php
session_start();
include('../../config/verificarSesion.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/css/bibliotecario.css">
    <title>Bibliotecario</title>
</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li><a href="javascript:cargar('PanelBibliotecario.php')">MENÚ PRINCIPAL</a></li>
                <li><a href="javascript:cargar('GestionReservas.php')">SOLICITUDES DE RESERVA</a></li>
                <li><a href="javascript:cargar('GestionUsuarios.php')">GESTION DE USUARIOS</a></li>
            </ul>
        </nav>
    </div>
    <div class="contenido">
        <header>
            <nav class="nav-head">
                <ul class="nav-head">
                    <li><a href="javascript:cargar('LibrosBibliotecario.php')">MENÚ PRINCIPAL</a></li>
                    <li><a href="javascript:cargar('GestionReservas.php')">SOLICITUDES DE RESERVA</a></li>
                    <li><a href="javascript:cargar('GestionUsuarios.php')">GESTION DE USUARIOS</a></li>
                    <form action="" method="get">
                        <input type="text" name="action" value="buscar libro">
                    </form>
                    <li><a href="../controllers/auth_controller.php?    action=logout">CERRAR SESIÓN</a></li>
                </ul>
            </nav>
        </header>

        <main id="contenedor">

        </main>
    </div>
    
    <footer>

    </footer>
    <script src="../../../public/js/bibliotecario.js"></script>
</body>
</html>