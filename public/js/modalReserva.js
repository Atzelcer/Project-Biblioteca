/**
 * Modal de Reserva de Libros
 * Maneja la funcionalidad del modal para reservar libros
 */

console.log('modalReserva.js cargado correctamente');

let libroSeleccionado = null;

/**
 * Abre el modal de reserva para un libro específico
 * @param {number} idLibro - ID del libro a reservar
 */
function abrirModalReserva(idLibro) {
    console.log('abrirModalReserva llamada con ID:', idLibro);
    libroSeleccionado = idLibro;
    // Mostrar el modal
    document.getElementById('reservaModal').style.display = 'block';
    
    // Obtener información del libro con fetch
    fetch('/Project-Biblioteca/public/ajax_books.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=getBookInfo&id=' + idLibro
    })
    .then(response => {
        console.log('Respuesta recibida para getBookInfo:', response);
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            document.getElementById('infoLibro').innerHTML = `
                <h4 style="margin: 0 0 10px 0;">Información del Libro:</h4>
                <p><strong>Nombre:</strong> ${data.libro.nombre}</p>
                <p><strong>Autor:</strong> ${data.libro.autor}</p>
                <p><strong>Estado:</strong> <span style="color: green; font-weight: bold;">${data.libro.estado}</span></p>
            `;
        } else {
            console.error('Error del servidor:', data.message);
            document.getElementById('infoLibro').innerHTML = '<p>Error: ' + (data.message || 'Error al cargar la información del libro.') + '</p>';
        }
    })
    .catch(error => {
        console.error('Error en fetch:', error);
        document.getElementById('infoLibro').innerHTML = '<p>Error de conexión: ' + error.message + '</p>';
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
    fetch('/Project-Biblioteca/public/ajax_books.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=reservar&id=' + libroSeleccionado
    })
    .then(response => {
        console.log('Respuesta recibida para reservar:', response);
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos de reserva recibidos:', data);
        if (data.success) {
            // Mostrar el ticket de reserva
            mostrarTicketReserva(data.ticket);
            cerrarModalReserva();
        } else {
            alert('Error al reservar el libro: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error en reserva:', error);
        alert('Error al procesar la reserva: ' + error.message);
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
        <div style="text-align: center; border: 2px solid #000; padding: 20px; background-color: #fff;">
            <h2 style="margin: 0 0 20px 0; color: #000;">TICKET DE RESERVA</h2>
            
            <div style="text-align: left; margin: 15px 0; padding: 15px; border: 1px solid #ccc;">
                <h3 style="margin: 0 0 10px 0; color: #000;">Información del Libro</h3>
                <p style="margin: 5px 0;"><strong>Nombre:</strong> ${ticket.libro_nombre}</p>
                <p style="margin: 5px 0;"><strong>Autor:</strong> ${ticket.libro_autor}</p>
                <p style="margin: 5px 0;"><strong>Código:</strong> ${ticket.codigo_reserva}</p>
            </div>
            
            <div style="text-align: left; margin: 15px 0; padding: 15px; border: 1px solid #ccc;">
                <h3 style="margin: 0 0 10px 0; color: #000;">HORARIOS DE ATENCIÓN</h3>
                <p style="margin: 5px 0;">Lunes a Viernes de 8:00 a 12:00</p>
                <p style="margin: 5px 0;">y de 14:00 a 18:00</p>
                <p style="margin: 10px 0 0 0; font-weight: bold;">Horario válido: Podrás recoger el libro hoy o en días hábiles empezando desde el 20/08/2025</p>
            </div>
            
            <div style="text-align: left; margin: 15px 0; padding: 15px; border: 1px solid #ccc;">
                <p style="margin: 5px 0;"><strong>Fecha de Reserva:</strong> ${fechaReserva}</p>
                <p style="margin: 5px 0;"><strong>Hora de Reserva:</strong> ${horaReserva}</p>
                <p style="margin: 5px 0;"><strong>Usuario:</strong> ${ticket.usuario_nombre}</p>
            </div>
            
            <div style="margin-top: 20px;">
                <p style="font-size: 14px; color: #000;">¡Reserva exitosa! Presenta este ticket en la biblioteca para recoger tu libro.</p>
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
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 20px; 
                        background: white;
                        color: #000;
                    }
                    @media print { 
                        body { margin: 0; }
                    }
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
