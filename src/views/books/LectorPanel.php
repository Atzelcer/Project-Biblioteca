<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/verificarSesion.php';

class BookRepository {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    public function findBooks($search = '') {
        $sql = "SELECT id_libro, nombre, autor, estado FROM libros";
        
        if (!empty($search)) {
            $search = $this->conexion->real_escape_string($search);
            $sql .= " WHERE nombre LIKE '%{$search}%' OR autor LIKE '%{$search}%'";
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

$repository = new BookRepository($conexion);
$search = $_GET['search'] ?? '';
$libros = $repository->findBooks($search);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Lector</title>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>LECTOR - Bienvenido <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></h1>
        <a href="../../../public/logout.php">
            <button type="button">Cerrar Sesión</button>
        </a>
    </div>
    
    <?php if (isset($_GET['message'])): ?>
        <div style="padding: 10px; border: 1px solid #ccc; margin: 10px 0; background-color: #f0f0f0;">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Buscar por nombre o autor" 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">BUSCAR</button>
    </form>
    

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 8px;">Nombre</th>
                <th style="padding: 8px;">Autor</th>
                <th style="padding: 8px;">Disponibilidad</th>
                <th style="padding: 8px;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($libros)): ?>
                <?php foreach ($libros as $libro): ?>
                    <tr>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($libro['nombre']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($libro['autor']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($libro['estado']); ?></td>
                        <td style="padding: 8px;">
                            <?php if ($libro['estado'] == 'Disponible'): ?>
                                <a href="reservation.php?id=<?php echo $libro['id_libro']; ?>">
                                    <button type="button">Reservar</button>
                                </a>
                            <?php elseif ($libro['estado'] == 'Reservado'): ?>
                                <button type="button" disabled>Reservado</button>
                            <?php else: ?>
                                <button type="button" disabled>No disponible</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 8px; text-align: center;">No se encontraron libros</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
