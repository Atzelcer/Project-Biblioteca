function mostrarModalResultado(exito, mensaje) {
    
    let modal = document.getElementById('modalResultado');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modalResultado';
        modal.style.position = 'fixed';
        modal.style.zIndex = '1100';
        modal.style.left = '0';
        modal.style.top = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0,0,0,0.4)';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';

        modal.innerHTML = `
            <div style="background-color: #fefefe; padding: 20px; border-radius: 5px; min-width: 300px; text-align: center;">
                <h3 id="modalResultadoTitulo"></h3>
                <p id="modalResultadoMensaje"></p>
                <!--  <button onclick="cerrarModalResultado()" style="margin-top: 15px; padding: 10px 20px; border: none; border-radius: 3px; background-color: #007bff; color: white; cursor: pointer;">Cerrar</button>-->
            </div>
        `;
        document.body.appendChild(modal);
    }

    document.getElementById('modalResultadoTitulo').textContent = exito ? '¡Éxito!' : 'Error';
    document.getElementById('modalResultadoMensaje').textContent = mensaje;
    modal.style.display = 'flex';
}

function cerrarModalResultado() {
    const modal = document.getElementById('modalResultado');
    if (modal) modal.style.display = 'none';
}
function confirmarAutorizacion() {
    if (!reservaSeleccionada) return;

    const formData = new FormData();
    formData.append('id_reserva', reservaSeleccionada);
    formData.append('accion', 'autorizar');

    fetch('GestionReservas.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes("Reserva autorizada exitosamente")) {
            mostrarModalResultado(true, 'RESERVA AUTORIZADA !!!!!!');

            setTimeout(() => {
                cerrarModalResultado();
            }, 2000);

            setTimeout(() => {
                refrescarTabla();
            }, 1000);

        } else {
            mostrarModalResultado(false, 'Error en la reserva!!!!!!');
        }
    })
    .catch(error => {
        mostrarModalResultado(false, 'Error de conexión.');
    });
}
function refrescarTabla() {
    fetch('GestionReservas.php')
        .then(response => response.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');
            let nuevoTbody = doc.querySelector('table tbody');
            let actualTbody = document.querySelector('table tbody');
            if (nuevoTbody && actualTbody) {
                actualTbody.innerHTML = nuevoTbody.innerHTML;
            }
        })
        .catch(error => console.error("Error al refrescar la tabla:", error));
} 