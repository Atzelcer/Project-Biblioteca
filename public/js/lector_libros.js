(function () {
  var base = document.body.getAttribute('data-base') || '';
  var endpoint = base + '/src/entradas/crear_reserva.php';

  var mConfirm = document.getElementById('mConfirm');
  var mTicket  = document.getElementById('mTicket');
  var imgLibro = document.getElementById('imgLibro');
  var nLibro   = document.getElementById('nombreLibro');
  var aLibro   = document.getElementById('autorLibro');
  var tFicha   = document.getElementById('tFicha');
  var tLibro   = document.getElementById('tLibro');
  var tAutor   = document.getElementById('tAutor');

  var sel = { id: 0, nombre: '', autor: '', foto: '' };

  function mostrar(el) { el.style.display = 'grid'; }
  function ocultar(el) { el.style.display = 'none'; }

  var botones = document.querySelectorAll('button.reservar');
  for (var i = 0; i < botones.length; i++) {
    botones[i].addEventListener('click', function (e) {
      var fila = e.target.closest('tr');
      sel.id     = parseInt(fila.getAttribute('data-id'), 10);
      sel.nombre = fila.getAttribute('data-nombre');
      sel.autor  = fila.getAttribute('data-autor');
      sel.foto   = fila.getAttribute('data-foto') || 'default-book.png';
      nLibro.textContent = sel.nombre;
      aLibro.textContent = sel.autor;
      imgLibro.src = base + '/public/images/books/' + sel.foto;
      mostrar(mConfirm);
    });
  }

  document.getElementById('btnCancel').addEventListener('click', function () {
    ocultar(mConfirm);
  });

  document.getElementById('btnConfirm').addEventListener('click', function () {
    var body = 'id_libro=' + encodeURIComponent(sel.id);
    fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        ocultar(mConfirm);
        if (!data.ok) { alert(data.msg || 'No se pudo reservar'); return; }
        tFicha.textContent = data.ficha;
        tLibro.textContent = 'Libro: ' + sel.nombre;
        tAutor.textContent = 'Autor: ' + sel.autor;
        mostrar(mTicket);
        var fila = document.querySelector('tr[data-id="' + sel.id + '"]');
        if (fila) fila.parentNode.removeChild(fila);
      })
      .catch(function () {
        ocultar(mConfirm);
        alert('Error al reservar');
      });
  });

  document.getElementById('btnCerrarTicket').addEventListener('click', function () {
    ocultar(mTicket);
  });
})();
