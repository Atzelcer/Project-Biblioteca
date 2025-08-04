<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administracion de Libros - Bibliotecario</title>
</head>
<body>
    <h1>BIBLIOTECARIO</h1>
    
    <?php if (isset($_GET['message'])): ?>
        <div style="padding: 10px; border: 1px solid #ccc; margin: 10px 0; background-color: #f0f0f0;">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    

    <form method="GET" action="">
        <input type="hidden" name="action" value="catalog_librarian">
        <input type="text" name="search" placeholder="Buscar CODE" 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">BUSCAR</button>
    </form>
    
    <table border="1">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Autor</th>
                <th>Disponibilidad</th>
                <th>Tiempo Restante</th>
                <th>Botón de reserva</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($libros) && !empty($libros)): ?>
                <?php foreach ($libros as $libro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($libro['codigo_libro']); ?></td>
                        <td><?php echo htmlspecialchars($libro['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                        <td><?php echo htmlspecialchars($libro['estado']); ?></td>
                        <td><?php echo $libro['tiempo_restante'] ?? '-'; ?></td>
                        <td>
                            <button>Autorizar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No se encontraron libros</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
