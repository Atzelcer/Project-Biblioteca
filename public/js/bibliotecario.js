const contenedor = document.getElementById('contenedor');

function cargar(pagina) {
    fetch(pagina)
        .then(response => response.text())
        .then(data => {
            contenedor.innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}

function cerrarSesion() {
    fetch('logout.php')
        .then(() => {
            window.location.href = '../auth/login.php';
        })
        .catch(error => console.error('Error:', error));
}