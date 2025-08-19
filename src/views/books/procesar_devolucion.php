<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && isset($_POST['id_reserva'])) {
        $id_reserva = $_POST['id_reserva'];
        $accion = $_POST['accion'];
        
        if ($accion === 'registrar_devolucion') {
            $fechaDevolucionReal = date('Y-m-d H:i:s');
            
            $sql_check = "SHOW COLUMNS FROM reservas LIKE 'fechaDevolucionReal'";
            $result_check = $conexion->query($sql_check);
            
            if ($result_check && $result_check->num_rows > 0) {
                $sql_devolucion = "UPDATE reservas SET estado = 'finalizada', fechaDevolucionReal = ? WHERE id_reserva = ?";
                $stmt = $conexion->prepare($sql_devolucion);
                $stmt->bind_param('si', $fechaDevolucionReal, $id_reserva);
            } else {
                $sql_devolucion = "UPDATE reservas SET estado = 'finalizada' WHERE id_reserva = ?";
                $stmt = $conexion->prepare($sql_devolucion);
                $stmt->bind_param('i', $id_reserva);
            }
            
            if ($stmt->execute()) {
          
                $sql_liberar = "UPDATE libros SET estado = 'Disponible' WHERE id_libro = (SELECT id_libro FROM reservas WHERE id_reserva = ?)";
                $stmt2 = $conexion->prepare($sql_liberar);
                $stmt2->bind_param('i', $id_reserva);
                
                if ($stmt2->execute()) {
                    $respuesta = [
                        'success' => true,
                        'mensaje' => "Libro devuelto y disponible nuevamente - " . date('d/m/Y H:i'),
                        'fechaDevolucion' => $fechaDevolucionReal
                    ];
                } else {
                    $respuesta = [
                        'success' => false,
                        'mensaje' => 'Error al liberar el libro: ' . $conexion->error
                    ];
                }
                $stmt2->close();
            } else {
                $respuesta = [
                    'success' => false,
                    'mensaje' => 'Error al registrar la devolución: ' . $conexion->error
                ];
            }
            $stmt->close();
        } else {
            $respuesta = [
                'success' => false,
                'mensaje' => 'Acción no válida'
            ];
        }
    } else {
        $respuesta = [
            'success' => false,
            'mensaje' => 'Datos incompletos'
        ];
    }
} else {
    $respuesta = [
        'success' => false,
        'mensaje' => 'Método no permitido'
    ];
}

echo json_encode($respuesta);
?>
