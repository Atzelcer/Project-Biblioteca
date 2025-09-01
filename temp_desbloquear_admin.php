<?php
require_once __DIR__ . '/src/config/baseDatos.php';

try {
    $db = BaseDatos::conexion();
    
    // Verificar estado actual del bibliotecario
    $st = $db->prepare("SELECT CI, nombre, apellido, estado FROM usuario WHERE rol = 'bibliotecario' AND CI = 12345");
    $st->execute();
    $user = $st->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "Usuario bibliotecario encontrado:\n";
        echo "CI: " . $user['CI'] . "\n";
        echo "Nombre: " . $user['nombre'] . " " . $user['apellido'] . "\n";
        echo "Estado actual: " . $user['estado'] . "\n\n";
        
        if ($user['estado'] === 'bloqueado') {
            // Desbloquear usuario
            $st = $db->prepare("UPDATE usuario SET estado = 'normal' WHERE CI = 12345 AND rol = 'bibliotecario'");
            $result = $st->execute();
            
            if ($result) {
                echo "✅ Usuario bibliotecario desbloqueado exitosamente.\n";
            } else {
                echo "❌ Error al desbloquear usuario.\n";
            }
        } else {
            echo "✅ El usuario ya está en estado normal.\n";
        }
    } else {
        echo "❌ Usuario bibliotecario no encontrado.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nCredenciales para acceder:\n";
echo "Usuario: 12345 (CI) o bibliotecario@biblioteca.edu (correo)\n";
echo "Contraseña: 12345\n";
echo "\nPuedes eliminar este archivo después de usarlo.\n";
?>
