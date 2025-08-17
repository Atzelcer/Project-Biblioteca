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
    <link rel="stylesheet" href="../../../public/css/form.css">
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>LECTOR - Bienvenido <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></h1>
        <a href="../../config/logout.php">
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
    <br><br>

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
                            <a href="#" class="libro-link" data-id="<?php echo $libro['id_libro']; ?>" 
                               style="color: #DF3CF7; text-decoration: none; cursor: pointer;">
                                <?php echo htmlspecialchars($libro['nombre']); ?>
                            </a>
                        </td>
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

    <!-- Modal de libro -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detalles del Libro</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <p style="color: #EAEAEA; text-align: center;">Cargando...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-secondary" onclick="closeModal()">Cerrar</button>
                <button type="button" class="btn-modal" id="reserveBtn" style="display: none;">Reservar</button>
            </div>
        </div>
    </div>

    <script>
        // Elementos del DOM
        const modal = document.getElementById('bookModal');
        const modalContent = document.getElementById('modalContent');
        const reserveBtn = document.getElementById('reserveBtn');
        const closeBtn = document.querySelector('.close');

        // Event listeners para abrir el modal
        document.querySelectorAll('.libro-link').forEach(link => {
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
            modal.classList.add('show');
            
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
                <div class="book-info">
                    <div class="book-image">
                        ${imageUrl ? 
                            `<img src="${imageUrl}" alt="${libro.nombre}" onerror="this.parentElement.innerHTML='<div class=\\'no-image\\'>Sin imagen disponible</div>'">` :
                            '<div class="no-image">Sin imagen disponible</div>'
                        }
                    </div>
                    <div class="book-details">
                        <h3>${libro.nombre}</h3>
                        <p><span class="label">Autor:</span> ${libro.autor}</p>
                        <p><span class="label">Código:</span> ${libro.codigo_libro}</p>
                        <p><span class="label">Estado:</span> 
                            <span class="copy-status status-${libro.estado.toLowerCase().replace(' ', '-')}">${libro.estado}</span>
                        </p>
                    </div>
                </div>
                
                <div class="copies-section">
                    <h4>Ejemplares Disponibles</h4>
                    <div class="copies-list">
                        ${ejemplares.map(ejemplar => `
                            <div class="copy-item">
                                <div class="copy-info">
                                    <div class="copy-code">Código: ${ejemplar.codigo}</div>
                                    <div>Ubicación: ${ejemplar.ubicacion}</div>
                                </div>
                                <span class="copy-status status-${ejemplar.estado.toLowerCase().replace(' ', '-')}">${ejemplar.estado}</span>
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
            modal.classList.remove('show');
            reserveBtn.style.display = 'none';
        }
    </script>
</body>
</html>
