<?php include 'connectionBd.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lector listado de libros</title>
</head>
<body>
<div>
<!-- creacion de la tabla -->
<table BORDER="1"CELLSPACING = "0" CELLPADING ="20">
<!-- creacion del titulo de la tabla-->
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Autor</th>
                <th>Disponibilidad</th>
                <th>Acción</th>
            </tr>
        </thead>

<tbody>
<!-- consulta a la base de datos -->
        <?php
            $sql = "SELECT codigo_libro, nombre, autor, estado, tiempo_restante FROM libros";
                $resultado = $conexion->query($sql); 
                if ($resultado === FALSE) {
                // en caso deque falla la consulta 
                echo "<tr><td colspan='5'>Error al ejecutar la consulta SQL: " . $conexion->error . "</td></tr>";
            } elseif ($resultado->num_rows > 0) {
                    // Si hay resultados, va ha recorrer la fila
                      while($row = $resultado->fetch_assoc()) {
                        $disponibilidadLibro = '';  
                         $btnReserva= ''; 
                         switch ($row["estado"]) {
                            
                            case 'Disponible':
                                $disponibilidadLibro = '<span>Disponible</span>';
                                $btnReserva = ''; // Botón habilitado
                                break;
                            case 'No Disponible':
                                $disponibilidadLibro = '<span>No disponible</span>';
                                $btnReserva  = 'disabled'; // Botón deshabilitado
                                break;
                            case 'Reservado':
                                $disponibilidadLibro = '<span>Reservado</span>';
                              $btnReserva  = 'disabled';
                               break;
                            }
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["codigo_libro"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["autor"]) . "</td>";
                        echo "<td>" . $disponibilidadLibro. "</td>";
                         echo "<td><button " . $btnReserva. ">Reservar</button></td>";
                        echo "</tr>";
                        }
                        }
                        // Cierra la conexión 
            if (isset($conexion) && $conexion instanceof mysqli) {
                $conexion->close();
            }
            
               
                ?>
</tbody>
</table>
</div>
</body>
</html>

