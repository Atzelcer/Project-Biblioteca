(function(){
  var base = document.body.dataset.base || '';
  var sel = { id:null, nombre:'', autor:'', foto:'' };
  var latest = null;

  function q(s){return document.querySelector(s)}
  function qa(s){return Array.from(document.querySelectorAll(s))}
  function show(el){el.style.display='grid'}
  function hide(el){el.style.display='none'}

  function fillTicket(d){
    // Mostrar solo el código de reserva en el campo correspondiente
    q('#rFichaReserva').textContent = d.fichaReserva || d.ficha || '';
    q('#tLibro').textContent = d.libro ? ('Libro: ' + d.libro) : '';
    q('#tAutor').textContent = d.autor ? ('Autor: ' + d.autor) : '';
    q('#tEstado').textContent = d.estado ? ('Estado: ' + d.estado) : '';
    // Mostrar botón extender solo si está autorizada
    var btnExt = q('#btnExtender');
    if (btnExt) {
      if (d.estado && d.estado.toLowerCase() === 'autorizada') {
        btnExt.style.display = 'inline-block';
      } else {
        btnExt.style.display = 'none';
      }
    }
  }

  function toggleTicketButton(){
    var b = q('#btnTicket');
    if (latest) {
      b.style.display = 'inline-block';
  b.textContent = latest.fichaReserva || latest.ficha || 'Ticket';
      // Cambiar color de fondo si está autorizada
      if (latest.estado && latest.estado.toLowerCase() === 'autorizada') {
        b.style.backgroundColor = '#3cb371'; // verde
        b.style.color = '#fff';
      } else {
        b.style.backgroundColor = '';
        b.style.color = '';
      }
    } else {
      b.style.display = 'none';
      b.style.backgroundColor = '';
      b.style.color = '';
    }
  }

  function fetchLatest(){
    fetch(base + '/src/entradas/api_mis_reservas.php?action=ultima')
      .then(function(r){return r.json()})
      .then(function(j){
        if(j.ok && j.has){ latest = j.data; } else { latest = null; }
        toggleTicketButton();
      });
  }

  qa('button.reservar').forEach(function(b){
    b.addEventListener('click', function(e){
      var tr = e.target.closest('tr');
      sel.id = +tr.dataset.id;
      sel.nombre = tr.dataset.nombre || '';
      sel.autor = tr.dataset.autor || '';
      sel.foto = tr.dataset.foto || 'default-book.png';
      q('#nombreLibro').textContent = sel.nombre;
      q('#autorLibro').textContent = sel.autor;
      q('#imgLibro').src = base + '/public/images/books/' + sel.foto;
      show(q('#mConfirm'));
    });
  });

  q('#btnCancel').addEventListener('click', function(){ hide(q('#mConfirm')) });

  q('#btnConfirm').addEventListener('click', function(){
    var fd = new URLSearchParams(); fd.append('id_libro', sel.id);
    fetch(base + '/src/entradas/crear_reserva.php', {
      method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:fd
    }).then(function(r){return r.json()}).then(function(j){
      hide(q('#mConfirm'));
      if(!j.ok){ alert(j.msg||'no se pudo reservar'); return; }
      // Después de crear la reserva, recargar el estado real desde el backend
      fetchLatest();
      fillTicket({
        fichaReserva: j.ficha,
        libro: sel.nombre,
        autor: sel.autor,
        estado: j.estado || 'pendiente'
      });
      show(q('#mTicket'));
      var row = document.querySelector('tr[data-id="'+sel.id+'"]');
      if(row) row.remove();
    }).catch(function(){ alert('error del servidor'); });
  });

  q('#btnCerrarTicket').addEventListener('click', function(){ hide(q('#mTicket')) });

  q('#btnTicket').addEventListener('click', function(){
    if(!latest){ return; }
    fillTicket(latest);
    show(q('#mTicket'));
  });

  fetchLatest();
})();
