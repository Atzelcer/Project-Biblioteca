/**
 * Autorización de Reservas - Modal de Confirmación
 * Maneja la funcionalidad del botón autorizar con modal de confirmación
 */

let reservaSeleccionada = null;


/**
 * Abre el modal de confirmación para autorizar una reserva
 */
function abrirModalAutorizar(idReserva, ticket, libro, ejemplar, solicitante) {
    reservaSeleccionada = idReserva;
    
    // Llenar los datos en el modal
    document.getElementById('modalTicket').textContent = ticket;
    document.getElementById('modalLibro').textContent = libro;
    document.getElementById('modalEjemplar').textContent = ejemplar;
    document.getElementById('modalSolicitante').textContent = solicitante;
    
    // Mostrar el modal
    document.getElementById('modalAutorizar').style.display = 'block';
}

/**
 * Cierra el modal de autorización
 */
function cerrarModal() {
    document.getElementById('modalAutorizar').style.display = 'none';
    reservaSeleccionada = null;
}

/**
 * Confirma y procesa la autorización de la reserva
 */
function confirmarAutorizacion() {
    if (reservaSeleccionada) {
        document.getElementById('hiddenIdReserva').value = reservaSeleccionada;
        document.getElementById('formAutorizar').submit();
    }
}

/**
 * Inicializa los eventos cuando se carga la página
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal al hacer clic fuera de él
    window.onclick = function(event) {
        const modal = document.getElementById('modalAutorizar');
        if (event.target === modal) {
            cerrarModal();
        }
    }
});
