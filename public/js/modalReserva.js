/**
 * Modal de Reserva de Libros
 * Maneja la funcionalidad del modal para reservar libros
 */

let libroSeleccionado = null;

/**
 * Abre el modal de reserva para un libro específico
 * @param {number} idLibro - ID del libro a reservar
 */
function abrirModalReserva(idLibro) {
    libroSeleccionado = idLibro;
    // Mostrar el modal
    document.getElementById('reservaModal').style.display = 'block';
    
    // Obtener información del libro con fetch
    fetch('../../controllers/books_controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=getBookInfo&id=' + idLibro
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('infoLibro').innerHTML = `
                <h4 style="margin: 0 0 10px 0;">Información del Libro:</h4>
                <p><strong>Nombre:</strong> ${data.libro.nombre}</p>
                <p><strong>Autor:</strong> ${data.libro.autor}</p>
                <p><strong>Estado:</strong> <span style="color: green; font-weight: bold;">${data.libro.estado}</span></p>
            `;
        } else {
            document.getElementById('infoLibro').innerHTML = '<p>Error al cargar la información del libro.</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('infoLibro').innerHTML = '<p>Error al cargar la información del libro.</p>';
    });
}

/**
 * Cierra el modal de reserva
 */
function cerrarModalReserva() {
    document.getElementById('reservaModal').style.display = 'none';
    libroSeleccionado = null;
}

/**
 * Confirma y procesa la reserva del libro
 */
function confirmarReserva() {
    if (!libroSeleccionado) return;
    
    // Realizar la reserva
    fetch('../../controllers/books_controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=reservar&id=' + libroSeleccionado
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar el ticket de reserva
            mostrarTicketReserva(data.ticket);
            cerrarModalReserva();
        } else {
            alert('Error al reservar el libro: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la reserva');
    });
}

/**
 * Muestra el modal del ticket de reserva
 * @param {Object} ticket - Datos del ticket de reserva
 */
function mostrarTicketReserva(ticket) {
    const ticketModal = document.getElementById('ticketModal');
    const ticketContent = document.getElementById('ticketContent');
    
    const fechaReserva = new Date().toLocaleDateString('es-ES');
    const horaReserva = new Date().toLocaleTimeString('es-ES');
    
    ticketContent.innerHTML = `
        <div style="text-align: center; border: 2px solid #28a745; padding: 20px; border-radius: 10px; background-color: #f8f9fa;">
            <h2 style="color: #28a745; margin-bottom: 20px;">🎫 TICKET DE RESERVA</h2>
            
            <div style="background-color: white; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #28a745;">
                <h3 style="margin: 0 0 15px 0; color: #333;">Información del Libro</h3>
                <p><strong>Nombre:</strong> ${ticket.libro_nombre}</p>
                <p><strong>Autor:</strong> ${ticket.libro_autor}</p>
                <p><strong>Código:</strong> ${ticket.codigo_reserva}</p>
            </div>
            
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h4 style="margin: 0 0 10px 0;">HORARIOS DE ATENCIÓN</h4>
                <p style="margin: 5px 0;">Lunes a Viernes de 8:00 a 12:00</p>
                <p style="margin: 5px 0;">y de 14:00 a 18:00</p>
                <p style="margin: 10px 0 0 0; font-weight: bold;">Horario válido: Podrás recoger el libro hoy o en días hábiles empezando desde el 20/08/2025</p>
            </div>
            
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #ffc107;">
                <p><strong>Fecha de Reserva:</strong> ${fechaReserva}</p>
                <p><strong>Hora de Reserva:</strong> ${horaReserva}</p>
                <p><strong>Usuario:</strong> ${ticket.usuario_nombre}</p>
            </div>
            
            <div style="margin-top: 20px;">
                <p style="font-size: 14px; color: #666;">¡Reserva exitosa! Presenta este ticket en la biblioteca para recoger tu libro.</p>
            </div>
        </div>
    `;
    
    ticketModal.style.display = 'block';
}

/**
 * Cierra el modal del ticket
 */
function cerrarTicketModal() {
    document.getElementById('ticketModal').style.display = 'none';
    // Recargar la página para actualizar el estado
    location.reload();
}

/**
 * Imprime el ticket de reserva
 */
function imprimirTicket() {
    const ticketContent = document.getElementById('ticketContent').innerHTML;
    const ventanaImpresion = window.open('', '_blank');
    ventanaImpresion.document.write(`
        <html>
            <head>
                <title>Ticket de Reserva</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                ${ticketContent}
            </body>
        </html>
    `);
    ventanaImpresion.document.close();
    ventanaImpresion.print();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal al hacer clic fuera de él
    window.onclick = function(event) {
        const reservaModal = document.getElementById('reservaModal');
        const ticketModal = document.getElementById('ticketModal');
        
        if (event.target == reservaModal) {
            cerrarModalReserva();
        }
        
        if (event.target == ticketModal) {
            cerrarTicketModal();
        }
    }
});
