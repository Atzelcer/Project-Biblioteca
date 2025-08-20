const contenedor = document.getElementById('contenedor');

window.onload = () => {
    cargarInicio();
};

function cargarInicio() {
    fetch('GestionLibros.php')
        .then(response => response.text())
        .then(data => {
            contenedor.innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}

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