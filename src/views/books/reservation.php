<?php
session_start();
include("../../config/database.php");
include("../../config/verificarSesion.php");

// Verificar que hay un ID de libro válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de libro no válido.");
}

$libro_id = $_GET['id'];

// Obtener información del libro
$sql = "SELECT * FROM libros WHERE id_libro = ?";
if ($stmt = mysqli_prepare($conexion, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $libro_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) == 0) {
        die("Libro no encontrado");
    }

    $libro = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
} else {
    die("Error al preparar la consulta: " . mysqli_error($conexion));
}

// Procesar la reserva si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generar código de ficha reserva aleatorio
    function generarFichaReserva() {
        $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeros = '0123456789';
        return substr(str_shuffle($letras), 0, 3) . substr(str_shuffle($numeros), 0, 2) . substr(str_shuffle($letras), 0, 1);
    }
    
    $fichaReserva = generarFichaReserva();
    
    // Obtener el id_usuario desde la base de datos usando el CI de la sesión
    $ci_usuario = $_SESSION['CI'];
    $sql_usuario = "SELECT id_usuario FROM usuario WHERE CI = ?";
    $stmt_usuario = mysqli_prepare($conexion, $sql_usuario);
    mysqli_stmt_bind_param($stmt_usuario, "i", $ci_usuario);
    mysqli_stmt_execute($stmt_usuario);
    $result_usuario = mysqli_stmt_get_result($stmt_usuario);
    
    if ($row_usuario = mysqli_fetch_assoc($result_usuario)) {
        $id_usuario = $row_usuario['id_usuario'];
    } else {
        die("Error: Usuario no encontrado en la sesión.");
    }
    mysqli_stmt_close($stmt_usuario);
    
    // Insertar la reserva en estado pendiente
    $sql_reserva = "INSERT INTO reservas (id_usuario, id_libro, fichaReserva, estado) VALUES (?, ?, ?, 'pendiente')";
    if ($stmt_reserva = mysqli_prepare($conexion, $sql_reserva)) {
        mysqli_stmt_bind_param($stmt_reserva, "iis", $id_usuario, $libro_id, $fichaReserva);
        
        if (mysqli_stmt_execute($stmt_reserva)) {
            // Actualizar estado del libro a Reservado
            $sql_update = "UPDATE libros SET estado = 'Reservado' WHERE id_libro = ?";
            $stmt_update = mysqli_prepare($conexion, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "i", $libro_id);
            mysqli_stmt_execute($stmt_update);
            
            // Redirigir a la página de ticket
            $nombre_completo = ($_SESSION['nombre'] ?? 'Usuario') . ' ' . ($_SESSION['apellido'] ?? '');
            $params = http_build_query([
                'ticket' => $fichaReserva,
                'libro' => $libro['nombre'],
                'usuario' => $nombre_completo
            ]);
            header("Location: ticket-reserva.php?" . $params);
            exit();
        } else {
            $error_message = "Error al procesar la reserva: " . mysqli_error($conexion);
        }
        mysqli_stmt_close($stmt_reserva);
    }
}

$hora_actual = date('H:i');
$es_horario_valido = ($hora_actual >= '08:00' && $hora_actual <= '18:00');

function obtenerProximoDiaHabil() {
    $fecha = new DateTime();
    $dia_semana = $fecha->format('N'); 
    
    if ($dia_semana == 6) { // Sábado
        $fecha->add(new DateInterval('P2D')); 
    } elseif ($dia_semana == 7) { // Domingo
        $fecha->add(new DateInterval('P1D')); 
    } else {
        $hora_actual = date('H:i');
        if ($hora_actual > '18:00') {
            $fecha->add(new DateInterval('P1D')); 
  
            $nuevo_dia = $fecha->format('N');
            if ($nuevo_dia == 6) {
                $fecha->add(new DateInterval('P2D'));
            } elseif ($nuevo_dia == 7) {
                $fecha->add(new DateInterval('P1D'));
            }
        }
    }
    
    return $fecha->format('Y-m-d');
}

$proximo_dia_habil = obtenerProximoDiaHabil();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Libro</title>
</head>
<body>
    <h1>Reservar Libro</h1>
    
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    
    <h2>Información del Libro:</h2>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($libro['nombre']); ?></p>
    <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
    <p><strong>Estado:</strong> <?php echo htmlspecialchars($libro['estado']); ?></p>
    
    <h3>HORARIOS DE ATENCION</h3>
    <p>Lunes a Viernes de 8:00 a 12:00</p>
    <p>y de 14:00 a 18:00</p>
    
    <?php if ($hora_actual > '18:00'): ?>
        <p style="color: orange;"><strong>Nota:</strong> Son más de las 18:00. Podrás recoger el libro en días hábiles empezando desde el <?php echo date('d/m/Y', strtotime($proximo_dia_habil)); ?></p>
    <?php else: ?>
        <p style="color: green;"><strong>Horario válido:</strong> Podrás recoger el libro hoy o en días hábiles empezando desde el <?php echo date('d/m/Y', strtotime($proximo_dia_habil)); ?></p>
    <?php endif; ?>
    
    <?php if ($libro['estado'] == 'Disponible'): ?>
        <form action="" method="POST">
            <input type="hidden" name="libro_id" value="<?php echo $libro['id_libro']; ?>">
            <button type="submit">Reservar</button>
            <a href="../../../src/views/books/LectorPanel.php">Cancelar</a>
        </form>
    <?php else: ?>
        <p style="color: red;">Este libro no está disponible para reserva.</p>
        <a href="../../../src/views/books/LectorPanel.php">Volver al Panel</a>
    <?php endif; ?>
</body>
</html>