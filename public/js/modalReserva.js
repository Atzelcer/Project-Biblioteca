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
        <div style="text-align: center; border: 3px solid #28a745; padding: 25px; border-radius: 15px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); box-shadow: 0 8px 25px rgba(40, 167, 69, 0.15);">
            
            <!-- Header del Ticket -->
            <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 20px; border-radius: 10px; margin: -10px -10px 20px -10px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                <h1 style="margin: 0; font-size: 28px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                    🎫 TICKET DE RESERVA
                </h1>
                <p style="margin: 5px 0 0 0; font-size: 16px; opacity: 0.9;">Sistema de Biblioteca Digital</p>
            </div>
            
            <!-- Información Principal -->
            <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 6px solid #28a745; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 15px 0; color: #28a745; font-size: 20px; border-bottom: 2px solid #e9ecef; padding-bottom: 10px;">
                    📚 Información del Libro
                </h3>
                <div style="text-align: left;">
                    <p style="margin: 8px 0; font-size: 16px;"><strong style="color: #495057;">Título:</strong> <span style="color: #212529;">${ticket.libro_nombre}</span></p>
                    <p style="margin: 8px 0; font-size: 16px;"><strong style="color: #495057;">Autor:</strong> <span style="color: #212529;">${ticket.libro_autor}</span></p>
                    <p style="margin: 8px 0; font-size: 16px;"><strong style="color: #495057;">Código de Reserva:</strong> <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 5px; font-weight: bold; font-family: monospace;">${ticket.codigo_reserva}</span></p>
                </div>
            </div>
            
            <!-- Información del Usuario -->
            <div style="background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 6px solid #007bff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 15px 0; color: #007bff; font-size: 18px; border-bottom: 2px solid #cce7ff; padding-bottom: 10px;">
                    👤 Datos del Usuario
                </h3>
                <div style="text-align: left;">
                    <p style="margin: 8px 0; font-size: 16px;"><strong style="color: #495057;">Usuario:</strong> <span style="color: #212529;">${ticket.usuario_nombre}</span></p>
                    <p style="margin: 8px 0; font-size: 16px;"><strong style="color: #495057;">Fecha:</strong> <span style="color: #212529;">${fechaReserva}</span></p>
                    <p style="margin: 8px 0; font-size: 16px;"><strong style="color: #495057;">Hora:</strong> <span style="color: #212529;">${horaReserva}</span></p>
                </div>
            </div>
            
            <!-- Horarios de Atención -->
            <div style="background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 6px solid #ffc107; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 15px 0; color: #856404; font-size: 18px; border-bottom: 2px solid #ffeaa7; padding-bottom: 10px;">
                    🕒 HORARIOS DE ATENCIÓN
                </h3>
                <div style="text-align: left;">
                    <p style="margin: 8px 0; font-size: 16px; font-weight: bold; color: #856404;">📅 Lunes a Viernes</p>
                    <p style="margin: 5px 0; font-size: 15px; color: #6c757d;">🌅 Mañana: 8:00 AM - 12:00 PM</p>
                    <p style="margin: 5px 0; font-size: 15px; color: #6c757d;">🌆 Tarde: 2:00 PM - 6:00 PM</p>
                    <div style="background: #fd7e14; color: white; padding: 10px; border-radius: 8px; margin-top: 15px;">
                        <p style="margin: 0; font-weight: bold; font-size: 14px;">⏰ <strong>Horario válido:</strong> Podrás recoger el libro hoy o en días hábiles empezando desde el 20/08/2025</p>
                    </div>
                </div>
            </div>
            
            <!-- Instrucciones -->
            <div style="background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 6px solid #17a2b8; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 15px 0; color: #0c5460; font-size: 18px;">
                    📋 Instrucciones Importantes
                </h3>
                <div style="text-align: left; color: #0c5460;">
                    <p style="margin: 8px 0; font-size: 15px;">✅ Presenta este ticket en la biblioteca para recoger tu libro</p>
                    <p style="margin: 8px 0; font-size: 15px;">📱 Guarda una captura de pantalla o imprime este ticket</p>
                    <p style="margin: 8px 0; font-size: 15px;">🆔 Lleva tu documento de identificación</p>
                    <p style="margin: 8px 0; font-size: 15px;">⚠️ La reserva vence si no recoges el libro en 3 días hábiles</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                <p style="font-size: 14px; color: #6c757d; margin: 5px 0;">Sistema de Biblioteca Digital - ${new Date().getFullYear()}</p>
                <p style="font-size: 12px; color: #adb5bd; margin: 0;">¡Gracias por usar nuestros servicios!</p>
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
                <title>Ticket de Reserva - Biblioteca Digital</title>
                <style>
                    body { 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                        margin: 20px; 
                        background: white;
                        color: #333;
                        line-height: 1.6;
                    }
                    @media print { 
                        body { 
                            margin: 0; 
                            background: white !important;
                        }
                        * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                        }
                    }
                    @page {
                        margin: 1cm;
                        size: A4;
                    }
                    .no-print {
                        display: none;
                    }
                </style>
            </head>
            <body>
                <div style="max-width: 600px; margin: 0 auto;">
                    ${ticketContent}
                </div>
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                        }, 500);
                    }
                </script>
            </body>
        </html>
    `);
    ventanaImpresion.document.close();
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
