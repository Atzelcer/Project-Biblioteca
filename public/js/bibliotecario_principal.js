(function(){
  var buscador = document.querySelector('.buscador input[type="text"]');
  var btnBuscar = document.querySelector('.buscador button');
  var contLibros = document.querySelector('.libros');
  if(!buscador || !btnBuscar || !contLibros) return;

  function buscarLibros() {
    var q = buscador.value.trim();
    fetch('src/entradas/api_libros.php?q=' + encodeURIComponent(q))
      .then(r => r.json())
      .then(j => {
        if(!j.ok) { contLibros.innerHTML = '<p style="color:var(--color-principal)">No se encontraron resultados</p>'; return; }
        if(!j.data.length) { contLibros.innerHTML = '<p style="color:var(--color-principal)">No se encontraron resultados</p>'; return; }
        contLibros.innerHTML = j.data.map(function(libro){
          return `<div class="libro">
            <img src="public/images/books/${libro.foto||'default-book.png'}" alt="${libro.nombre}">
            <div class="info">
              <h4>${libro.nombre}</h4>
              <p>Autor: ${libro.autor}</p>
            </div>
          </div>`;
        }).join('');
      });
  }

  btnBuscar.addEventListener('click', buscarLibros);
  buscador.addEventListener('keydown', function(e){
    if(e.key === 'Enter') buscarLibros();
  });
})();
