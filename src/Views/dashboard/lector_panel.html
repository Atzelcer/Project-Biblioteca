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
        //$sql = "SELECT id_libro, nombre, autor, estado FROM libros";
        $sql = "SELECT id_libro, nombre, autor, estado FROM libros libros WHERE estado = 'Disponible'";
        
        if (!empty($search)) {
            $search = $this->conexion->real_escape_string($search);
            //$sql .= " WHERE nombre LIKE '%{$search}%' OR autor LIKE '%{$search}%'";
            $sql .= " AND nombre LIKE '%{$search}%' OR autor LIKE '%{$search}%'";
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function findBookById($id) {
        $id = $this->conexion->real_escape_string($id);
        $sql = "SELECT id_libro, codigo_libro, nombre, autor, estado, foto_libro FROM libros WHERE id_libro = {$id}";
        
        $result = $this->conexion->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }
    
    public function getBookCopies($id) {
        // En este sistema, cada libro es un ejemplar único
        $libro = $this->findBookById($id);
        if ($libro) {
            return [
                [
                    'codigo' => $libro['codigo_libro'],
                    'estado' => $libro['estado'],
                    'ubicacion' => 'Biblioteca Principal'
                ]
            ];
        }
        return [];
    }
}

$repository = new BookRepository($conexion);
$search = $_GET['search'] ?? '';

// Manejar peticiones AJAX para detalles del libro
if (isset($_GET['action']) && $_GET['action'] === 'get_book_details') {
    header('Content-Type: application/json');
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['error' => 'ID de libro requerido']);
        exit;
    }
    
    $libro = $repository->findBookById($id);
    if (!$libro) {
        echo json_encode(['error' => 'Libro no encontrado']);
        exit;
    }
    
    $ejemplares = $repository->getBookCopies($id);
    
    echo json_encode([
        'libro' => $libro,
        'ejemplares' => $ejemplares
    ]);
    exit;
}

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
        <a href="../../config/logout.php">
            <button type="button">Cerrar Sesión</button>
        </a>
    </div>
    
    <?php if (isset($_GET['message'])): ?>
        <div style="padding: 10px; border: 1px solid #ccc; margin: 10px 0; background-color: #e6ffe6;">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    
    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Buscar por nombre o autor" 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                   style="padding: 8px 12px; width: 300px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; cursor: pointer;">BUSCAR</button>
        </form>
    </div>

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
                        <td style="padding: 8px;">
                            <a href="#" data-id="<?php echo $libro['id_libro']; ?>" style="color: #007bff; text-decoration: none; cursor: pointer;">
                                <?php echo htmlspecialchars($libro['nombre']); ?>
                            </a>
                        </td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($libro['autor']); ?></td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($libro['estado']); ?></td>
                        <td style="padding: 8px;">
                            <!-- <?php //if ($libro['estado'] == 'Disponible'): ?> -->
                                <!-- <a href="reservation.php?id=<?php //echo $libro['id_libro']; ?>"> -->
                                    <!-- <button type="button">Reservar</button> -->
                                <!-- </a> -->
                            <!-- <?php //elseif ($libro['estado'] == 'Reservado'): ?> -->
                                <!-- <button type="button" disabled>Reservado</button> -->
                            <!-- <?php //else: ?> -->
                                <!-- <button type="button" disabled>No disponible</button> -->
                            <!-- <?php //endif; ?> -->

                             <a href="reservation.php?id=<?php echo $libro['id_libro']; ?>">
                                <button type="button" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer;">Reservar</button>
                            </a>
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

    <!-- Modal de libro -->
    <div id="bookModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;">Detalles del Libro</h2>
                <span style="font-size: 24px; cursor: pointer;" onclick="closeModal()">&times;</span>
            </div>
            <div id="modalContent">
                <p>Cargando...</p>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="closeModal()" style="background-color: #6c757d; color: white; border: none; padding: 10px 20px; margin-right: 10px; cursor: pointer; border-radius: 3px;">Cerrar</button>
                <button type="button" id="reserveBtn" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px;">Reservar</button>
            </div>
        </div>
    </div>

    <script>
        // Elementos del DOM
        const modal = document.getElementById('bookModal');
        const modalContent = document.getElementById('modalContent');
        const reserveBtn = document.getElementById('reserveBtn');
        const closeBtn = document.querySelector('span[onclick="closeModal()"]');

        // Event listeners para abrir el modal
        document.querySelectorAll('a[data-id]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const bookId = this.getAttribute('data-id');
                openBookModal(bookId);
            });
        });

        // Event listener para cerrar el modal
        closeBtn.addEventListener('click', closeModal);
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Función para abrir el modal
        function openBookModal(bookId) {
            modal.style.display = 'block';
            
            // Cargar detalles del libro
            fetch(`?action=get_book_details&id=${bookId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        modalContent.innerHTML = `<p style="color: #dc3545; text-align: center;">${data.error}</p>`;
                        return;
                    }
                    
                    displayBookDetails(data.libro, data.ejemplares);
                })
                .catch(error => {
                    modalContent.innerHTML = '<p style="color: #dc3545; text-align: center;">Error al cargar los detalles del libro</p>';
                    console.error('Error:', error);
                });
        }

        // Función para mostrar los detalles del libro
        function displayBookDetails(libro, ejemplares) {
            const imageUrl = libro.foto_libro ? `../../../public/uploads/${libro.foto_libro}` : null;
            
            modalContent.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 4px;">
                    <div style="display: flex; justify-content: center; align-items: center;">
                        ${imageUrl ? 
                            `<img src="${imageUrl}" alt="${libro.nombre}" style="max-width: 120px; max-height: 160px; border: 1px solid #ccc; border-radius: 4px;" onerror="this.parentElement.innerHTML='<div style=\\'width: 120px; height: 160px; display: flex; align-items: center; justify-content: center; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; text-align: center;\\'>Sin imagen disponible</div>'">` :
                            '<div style="width: 120px; height: 160px; display: flex; align-items: center; justify-content: center; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; text-align: center;">Sin imagen disponible</div>'
                        }
                    </div>
                    <div>
                        <h3 style="margin: 0 0 15px 0; font-size: 20px;">${libro.nombre}</h3>
                        <p style="margin: 8px 0; font-size: 14px;"><strong>Autor:</strong> ${libro.autor}</p>
                        <p style="margin: 8px 0; font-size: 14px;"><strong>Código:</strong> ${libro.codigo_libro}</p>
                        <p style="margin: 8px 0; font-size: 14px;"><strong>Estado:</strong> ${libro.estado}</p>
                    </div>
                </div>
                
                <div>
                    <h4>Ejemplares Disponibles</h4>
                    <div>
                        ${ejemplares.map(ejemplar => `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; margin-bottom: 8px; border: 1px solid #ccc; border-radius: 4px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 14px;">Código: ${ejemplar.codigo}</div>
                                    <div>Ubicación: ${ejemplar.ubicacion}</div>
                                </div>
                                <span style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; border: 1px solid #ccc;">${ejemplar.estado}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            // Mostrar/ocultar botón de reserva
            const isAvailable = libro.estado === 'Disponible';
            if (isAvailable) {
                reserveBtn.style.display = 'inline-block';
                reserveBtn.onclick = () => {
                    window.location.href = `reservation.php?id=${libro.id_libro}`;
                };
            } else {
                reserveBtn.style.display = 'none';
            }
        }

        // Función para cerrar el modal
        function closeModal() {
            modal.style.display = 'none';
            reserveBtn.style.display = 'none';
        }
    </script>
</body>
</html>
