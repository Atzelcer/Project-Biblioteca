function abrirModalDevolucion(idReserva, nombreCompleto, ci, libroNombre, autor, fotoUsuario, fotoLibro) {
    // Separar nombre y apellido
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
    
    document.getElementById('fotoLibro').onerror = function() {
        this.src = '../../../public/images/booksPortada/default-book.svg';
    };
    
    document.getElementById('fotoUsuario').onerror = function() {
        this.src = '../../../public/images/usersFotos/default-user.svg';
    };
    
    document.getElementById('modalDevolucion').style.display = 'block';
}

function cerrarModalDevolucion() {
    document.getElementById('modalDevolucion').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    window.onclick = function(event) {
        const modal = document.getElementById('modalDevolucion');
        if (event.target == modal) {
            cerrarModalDevolucion();
        }
    };
    
    const formDevolucion = document.getElementById('formDevolucion');
    if (formDevolucion) {
        formDevolucion.addEventListener('submit', function(e) {
            const confirmacion = confirm('¿Confirmar la devolución del libro?');
            if (!confirmacion) {
                e.preventDefault();
                return false;
            }
        });
    }
});
