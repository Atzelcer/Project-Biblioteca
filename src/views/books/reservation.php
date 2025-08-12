<?php
include("../../config/database.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de libro no válido.");
}

$libro_id = $_GET['id'];
// -------------------------

$sql = "SELECT * FROM libros WHERE id_libro = ?";

if ($stmt = mysqli_prepare($conexion, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $libro_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) == 0) {
        die("Libro no encontrado");
    }

    $libro = mysqli_fetch_assoc($resultado);
    // ----------------------------------------
    
    mysqli_stmt_close($stmt);

} else {
    die("Error al preparar la consulta: " . mysqli_error($conexion));
}
?>

<form action="" method="POST">
    <h2>Reservar Libro</h2>
    <img src="<?php echo $libro['foto_libro']; ?>">
    <p>Nombre: <?php echo $libro['nombre']; ?></p>
    <p>Autor: <?php echo $libro['autor']; ?></p>
    <h4>HORARIOS DE ATENCION</h4>
    <p>Lunes a Viernes de 8:00 a 12:00</p><br>
    <p>y de 14:00 a 18:00</p>
    <input type="hidden" value="<?php echo $libro['id']; ?>">
    <input type="submit" value="Reservar">
    <button><a href="catalog.php">Cancelar</a></button>
</form>