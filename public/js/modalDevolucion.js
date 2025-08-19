function abrirModalDevolucion(idReserva, nombreCompleto, ci, libroNombre, autor, fotoUsuario, fotoLibro) {
    limpiarMensajes();
    
    const partesNombre = nombreCompleto.split(' ');
    const nombre = partesNombre[0] || '';
    const apellido = partesNombre.slice(1).join(' ') || '';
    
    document.getElementById('idReservaModal').value = idReserva;
    document.getElementById('nombreLibro').textContent = libroNombre;
    document.getElementById('autorLibro').textContent = autor;
    document.getElementById('nombreUsuario').textContent = nombre;
    document.getElementById('apellidoUsuario').textContent = apellido;
    document.getElementById('ciUsuario').textContent = ci;
    
    document.getElementById('fotoLibro').src = fotoLibro;
    document.getElementById('fotoUsuario').src = fotoUsuario;
    
    document.getElementById('fotoLibro').onerror = () => {
        document.getElementById('fotoLibro').src = '../../../public/images/booksPortada/default-book.svg';
    };
    
    document.getElementById('fotoUsuario').onerror = () => {
        document.getElementById('fotoUsuario').src = '../../../public/images/usersFotos/default-user.svg';
    };
    
    document.getElementById('modalDevolucion').style.display = 'block';
}

function cerrarModalDevolucion() {
    document.getElementById('modalDevolucion').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    window.onclick = (event) => {
        const modal = document.getElementById('modalDevolucion');
        if (event.target === modal) {
            cerrarModalDevolucion();
        }
    };
    
    const formDevolucion = document.getElementById('formDevolucion');
    if (formDevolucion) {
        formDevolucion.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const idReserva = document.getElementById('idReservaModal').value;
            const botonRegistrar = formDevolucion.querySelector('button[type="submit"]');
            const textoOriginal = botonRegistrar.textContent;
            
            botonRegistrar.textContent = 'Procesando...';
            botonRegistrar.disabled = true;
            
            try {
                const response = await fetch('procesar_devolucion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_reserva=${encodeURIComponent(idReserva)}&accion=registrar_devolucion`
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                cerrarModalDevolucion();
                
                if (data.success) {
                    mostrarMensajeExito(data.mensaje);
                    removerFilaReserva(idReserva);
                } else {
                    mostrarMensajeError(data.mensaje);
                }
                
            } catch (error) {
                console.error('Error:', error);
                mostrarMensajeError('Error al procesar la devolución. Por favor, intente nuevamente.');
            } finally {
                botonRegistrar.textContent = textoOriginal;
                botonRegistrar.disabled = false;
            }
        });
    }
});

function mostrarMensajeExito(mensaje) {
    const mensajeAnterior = document.querySelector('.mensaje-exito');
    if (mensajeAnterior) {
        mensajeAnterior.remove();
    }
    
    const divMensaje = document.createElement('div');
    divMensaje.className = 'mensaje-exito';
    divMensaje.style.cssText = `
        padding: 10px; 
        border: 1px solid #28a745; 
        margin: 10px 0; 
        border-radius: 5px; 
        font-weight: bold;
        background-color: #d4edda;
        color: #155724;
        animation: fadeIn 0.5s ease-in;
    `;
    divMensaje.textContent = mensaje;
    
    const titulo = document.querySelector('h1');
    titulo.parentNode.insertBefore(divMensaje, titulo.nextSibling);
    
    setTimeout(() => {
        if (divMensaje.parentNode) {
            divMensaje.style.opacity = '0';
            divMensaje.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => divMensaje.remove(), 500);
        }
    }, 5000);
}

function mostrarMensajeError(mensaje) {
    const mensajeAnterior = document.querySelector('.mensaje-error');
    if (mensajeAnterior) {
        mensajeAnterior.remove();
    }
    
    const divMensaje = document.createElement('div');
    divMensaje.className = 'mensaje-error';
    divMensaje.style.cssText = `
        padding: 10px; 
        border: 1px solid #dc3545; 
        margin: 10px 0; 
        border-radius: 5px; 
        font-weight: bold;
        background-color: #f8d7da;
        color: #721c24;
        animation: fadeIn 0.5s ease-in;
    `;
    divMensaje.textContent = `❌ ${mensaje}`;
    
    const titulo = document.querySelector('h1');
    titulo.parentNode.insertBefore(divMensaje, titulo.nextSibling);
    
    setTimeout(() => {
        if (divMensaje.parentNode) {
            divMensaje.style.opacity = '0';
            divMensaje.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => divMensaje.remove(), 500);
        }
    }, 7000);
}

function removerFilaReserva(idReserva) {
    const filas = document.querySelectorAll('tbody tr');
    filas.forEach(fila => {
        const inputIdReserva = fila.querySelector('input[name="id_reserva"]');
        if (inputIdReserva && inputIdReserva.value === idReserva) {
            fila.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
            fila.style.opacity = '0';
            fila.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                fila.remove();
                
                const filasRestantes = document.querySelectorAll('tbody tr');
                if (filasRestantes.length === 0) {
                    const tbody = document.querySelector('tbody');
                    const filaSinDatos = document.createElement('tr');
                    filaSinDatos.innerHTML = `
                        <td colspan="8" style="text-align: center; padding: 20px;">
                            No hay reservas autorizadas
                        </td>
                    `;
                    tbody.appendChild(filaSinDatos);
                }
            }, 500);
        }
    });
}

function limpiarMensajes() {
    const mensajeExito = document.querySelector('.mensaje-exito');
    const mensajeError = document.querySelector('.mensaje-error');
    
    if (mensajeExito) {
        mensajeExito.remove();
    }
    
    if (mensajeError) {
        mensajeError.remove();
    }
}
