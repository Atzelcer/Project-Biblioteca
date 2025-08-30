
(function(){
  var base = document.body.dataset.base || '';
  var sel = { id:null, nombre:'', autor:'', foto:'' };
  var latest = null;

  function q(s){ return document.querySelector(s); }
  function qa(s){ return Array.from(document.querySelectorAll(s)); }
  function showEl(el){ if(el){ el.style.display = 'grid'; } }
  function hideEl(el){ if(el){ el.style.display = 'none'; } }

  var modalIds = ['#mAviso','#mTicket','#mConfirm','#mExtender'];
  function hideAllModals(){
    modalIds.forEach(function(id){ var m = q(id); if(m) hideEl(m); });
  }
  function showModal(id){
    hideAllModals();
    var m = typeof id==='string' ? q(id) : id;
    showEl(m);
  }
  function closeNearestModal(fromEl){
    var m = fromEl.closest('.overlay');
    if(m) hideEl(m);
  }

  function normalizeReserva(r){
    if(!r) return null;
    return {
      id_reserva: r.id_reserva || r.id || r.reserva_id || null,
      fichaReserva: r.fichaReserva || r.ficha || '',
      libro: r.libro || r.libro_nombre || '',
      autor: r.autor || r.libro_autor || '',
      estado: r.estado || '',
      fechaDevolucion: r.fechaDevolucion || null
    };
  }

  function getIdReservaFromLatest(){
    return latest && latest.id_reserva ? latest.id_reserva : null;
  }

  function showAviso(msg){
    var el = q('#mAviso');
    if(!el){ alert(msg || 'Tienes una reserva pendiente'); return; }
    q('#avisoMsg') && (q('#avisoMsg').textContent = msg || 'Tienes una reserva pendiente');
    showModal('#mAviso');
  }

  function fillTicket(d){
    d = normalizeReserva(d) || {};
    var ficha = d.fichaReserva || '';
    q('#rFichaReserva') && (q('#rFichaReserva').textContent = ficha);
    q('#tLibro') && (q('#tLibro').textContent = d.libro ? ('Libro: ' + d.libro) : '');
    q('#tAutor') && (q('#tAutor').textContent = d.autor ? ('Autor: ' + d.autor) : '');
    q('#tEstado') && (q('#tEstado').textContent = d.estado ? ('Estado: ' + d.estado) : '');

    var dot = q('#dotEstado');
    if(dot){
      if((d.estado || '').toLowerCase() === 'autorizada'){
        dot.classList.remove('off');
        dot.style.backgroundColor = '#3cb371';
      }else{
        dot.classList.remove('off');
        dot.style.backgroundColor = '#9ca3af';
      }
    }

    var btnExt = q('#btnExtender');
    if(btnExt){
      var showBtn = (d.estado || '').toLowerCase() === 'autorizada';
      btnExt.style.display = showBtn ? 'inline-block' : 'none';
    }
  }

  function toggleTicketButton(){
    var b = q('#btnTicket');
    if(!b) return;
    if(latest){
      b.style.display = 'inline-block';
      b.textContent = latest.fichaReserva || 'Ticket';
      if((latest.estado || '').toLowerCase() === 'autorizada'){
        b.style.backgroundColor = '#3cb371';
        b.style.color = '#fff';
      }else{
        b.style.backgroundColor = '';
        b.style.color = '';
      }
    }else{
      b.style.display = 'none';
      b.style.backgroundColor = '';
      b.style.color = '';
    }
  }

  function fetchLatest(){
    fetch(base + '/src/entradas/api_mis_reservas.php?action=ultima')
      .then(function(r){ return r.json(); })
      .then(function(j){
        if(j && j.ok && j.data){
          latest = normalizeReserva(j.data);
        }else{
          latest = null;
        }
        toggleTicketButton();
      })
      .catch(function(){
        latest = null;
        toggleTicketButton();
      });
  }

  var btnCerrarAviso = q('#btnCerrarAviso');
  if(btnCerrarAviso){
    btnCerrarAviso.addEventListener('click', function(){ hideEl(q('#mAviso')); });
  }

  var btnTicket = q('#btnTicket');
  if(btnTicket){
    btnTicket.addEventListener('click', function(){
      if(!latest) return;
      fillTicket(latest);
      showModal('#mTicket');
    });
  }

  var btnCerrarTicket = q('#btnCerrarTicket');
  if(btnCerrarTicket){
    btnCerrarTicket.addEventListener('click', function(){ hideEl(q('#mTicket')); });
  }

  qa('button.reservar').forEach(function(b){
    b.addEventListener('click', function(e){
      var tr = e.target.closest('tr');
      if(!tr) return;
      sel.id = +(tr.dataset.id || 0);
      sel.nombre = tr.dataset.nombre || '';
      sel.autor = tr.dataset.autor || '';
      sel.foto = tr.dataset.foto || 'default-book.png';
      var img = q('#imgLibro'); if(img){ img.src = base + '/public/images/books/' + sel.foto; }
      q('#nombreLibro') && (q('#nombreLibro').textContent = sel.nombre);
      q('#autorLibro') && (q('#autorLibro').textContent = sel.autor);
      showModal('#mConfirm');
    });
  });

  var btnCancel = q('#btnCancel');
  if(btnCancel){
    btnCancel.addEventListener('click', function(){ hideEl(q('#mConfirm')); });
  }

  var btnConfirm = q('#btnConfirm');
  if(btnConfirm){
    btnConfirm.addEventListener('click', function(){
      if(!sel.id){ hideEl(q('#mConfirm')); alert('libro invalido'); return; }
      var fd = new URLSearchParams();
      fd.append('id_libro', sel.id);

      fetch(base + '/src/entradas/crear_reserva.php', {
        method: 'POST',
        headers: { 'Content-Type':'application/x-www-form-urlencoded' },
        body: fd
      })
      .then(function(r){ return r.json(); })
      .then(function(j){
        hideEl(q('#mConfirm'));
        if(!j || !j.ok){
          if(j && j.code === 'YA_ACTIVA'){
            showAviso(j.msg || 'Tienes una reserva pendiente');
            return;
          }
          alert((j && j.msg) ? j.msg : 'no se pudo reservar');
          return;
        }
        latest = normalizeReserva({
          id_reserva: j.id_reserva,
          fichaReserva: j.ficha,
          estado: j.estado || 'pendiente',
          libro: sel.nombre,
          autor: sel.autor,
          fechaDevolucion: j.fechaDevolucion || null
        });
        fetchLatest();
        fillTicket(latest);
        showModal('#mTicket');
        var row = document.querySelector('tr[data-id="'+sel.id+'"]');
        if(row) row.remove();
      })
      .catch(function(){
        hideEl(q('#mConfirm'));
        alert('error del servidor');
      });
    });
  }

  var btnExtender = q('#btnExtender');
  if(btnExtender){
    btnExtender.addEventListener('click', function(){ showModal('#mExtender'); });
  }

  var btnCancelExt = q('#btnCancelExt');
  if(btnCancelExt){
    btnCancelExt.addEventListener('click', function(){ hideEl(q('#mExtender')); });
  }

  var btnConfirmExt = q('#btnConfirmExt');
  if(btnConfirmExt){
    btnConfirmExt.addEventListener('click', function(){
      var idRes = getIdReservaFromLatest();
      if(!idRes){
        alert('Error: No se encontro la informacion de la reserva');
        return;
      }
      var fd = new URLSearchParams();
      fd.append('id', idRes);

      fetch(base + '/src/entradas/api_reservas.php?action=extender', {
        method: 'POST',
        headers: { 'Content-Type':'application/x-www-form-urlencoded' },
        body: fd
      })
      .then(function(r){ return r.json(); })
      .then(function(j){
        if(j && j.ok){
          if(!latest) latest = {};
          latest.fechaDevolucion = j.nuevaFecha || latest.fechaDevolucion || null;
          latest.estado = 'autorizada';
          fillTicket(latest);
          hideEl(q('#mExtender'));
          toggleTicketButton();
          alert('Se ha extendido el tiempo de la reserva por 1 dia mas');
        }else{
          alert((j && j.msg) ? j.msg : 'No se pudo extender la reserva. Intente nuevamente.');
        }
      })
      .catch(function(){
        alert('Error en el servidor al procesar la extension de tiempo');
      });
    });
  }

  document.addEventListener('click', function(e){
    var t = e.target;
    if(t.matches('[data-agree], .btnAgree')){
      closeNearestModal(t);
    }
    if(t.matches('.overlay[data-close-on-bg="true"]')){
      if(e.target === t) hideEl(t);
    }
    if(t.matches('[data-close]')){
      closeNearestModal(t);
    }
  });

  fetchLatest();
})();

