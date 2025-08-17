const contenedor = document.getElementById('contenedor');

function cargar(pagina) {
    fetch(pagina)
        .then(response => response.text())
        .then(data => {
            contenedor.innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}