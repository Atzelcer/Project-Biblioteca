(function(){
  var base = document.body.dataset.base || '';
  var sel = { id:null, nombre:'', autor:'', foto:'' };
  function q(s){return document.querySelector(s)}
  function qa(s){return Array.from(document.querySelectorAll(s))}
  function show(el){el.style.display='grid'}
  function hide(el){el.style.display='none'}

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
      q('#tFicha').textContent = j.ficha;
      q('#tLibro').textContent = 'Libro: ' + sel.nombre;
      q('#tAutor').textContent = 'Autor: ' + sel.autor;
      show(q('#mTicket'));
      var row = document.querySelector('tr[data-id="'+sel.id+'"]');
      if(row) row.remove();
    }).catch(function(){ alert('error del servidor'); });
  });

  q('#btnCerrarTicket').addEventListener('click', function(){ hide(q('#mTicket')) });
})();
