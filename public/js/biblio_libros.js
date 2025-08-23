(function(){
  if ((document.body.dataset.sec||'') !== 'libros') return;
  var base = document.body.dataset.base || '';
  var api  = base + '/src/entradas/api_reservas.php';
  var thead = document.getElementById('theadRes');
  var tbody = document.getElementById('tbodyReservas');
  var busca = document.getElementById('buscaRes');
  var estado = 'pendiente';

  var m = {
    box: document.getElementById('mAuth'),
    ficha: document.getElementById('mFicha'),
    libro: document.getElementById('mLibro'),
    autorLibro: document.getElementById('mAutorLibro'),
    nom: document.getElementById('mNom'),
    ape: document.getElementById('mApe'),
    ci: document.getElementById('mCI'),
    imgLibro: document.getElementById('mImgLibro'),
    imgUser: document.getElementById('mImgUser'),
    btnOk: document.getElementById('btnDoAuth'),
    btnCancel: document.getElementById('btnCancelAuth'),
    id: 0
  };

  var d = {
    box: document.getElementById('mDev'),
    libro: document.getElementById('dLibro'),
    autorLibro: document.getElementById('dAutorLibro'),
    nom: document.getElementById('dNom'),
    ape: document.getElementById('dApe'),
    ci: document.getElementById('dCI'),
    imgLibro: document.getElementById('dImgLibro'),
    imgUser: document.getElementById('dImgUser'),
    btnOk: document.getElementById('btnDoDev'),
    btnCancel: document.getElementById('btnCancelDev'),
    id: 0
  };

  function fmt(dt){
    if(!dt) return '';
    var x = new Date((dt+'').replace(' ','T'));
    return x.toLocaleString();
  }

  function headerHtml(){
    if (estado === 'pendiente') {
      return '<tr><th>Ticket</th><th>Usuario</th><th>CI</th><th>Libro</th><th>Autor</th><th>Acciones</th></tr>';
    }
    return '<tr><th>Ticket</th><th>Usuario</th><th>CI</th><th>Libro</th><th>Autor</th><th>F. Reserva</th><th>F. Devolución</th><th>Acciones</th></tr>';
  }

  function fila(r){
    var tr = document.createElement('tr');
    if (estado === 'pendiente') {
      tr.innerHTML =
        '<td>'+r.fichaReserva+'</td>'+
        '<td>'+r.nombre+' '+r.apellido+'</td>'+
        '<td>'+r.CI+'</td>'+
        '<td>'+r.libro+'</td>'+
        '<td>'+r.autor+'</td>'+
        '<td>'+
          '<button class="btn ok" data-a="'+r.id_reserva+'">Autorizar</button> '+
          '<button class="btn ghost" data-x="'+r.id_reserva+'">Rechazar</button>'+
        '</td>';
    } else {
      tr.innerHTML =
        '<td>'+r.fichaReserva+'</td>'+
        '<td>'+r.nombre+' '+r.apellido+'</td>'+
        '<td>'+r.CI+'</td>'+
        '<td>'+r.libro+'</td>'+
        '<td>'+r.autor+'</td>'+
        '<td>'+fmt(r.fechaReserva)+'</td>'+
        '<td>'+fmt(r.fechaDevolucion)+'</td>'+
        '<td><button class="btn ok" data-fin="'+r.id_reserva+'">Registrar devolución</button></td>';
    }
    return tr;
  }

  function cargar(){
    thead.innerHTML = headerHtml();
    var url = api + '?action=list&estado=' + encodeURIComponent(estado) + '&q=' + encodeURIComponent(busca.value||'');
    fetch(url).then(r=>r.json()).then(j=>{
      tbody.innerHTML = '';
      if(!j.ok) return;
      j.data.forEach(function(it){ tbody.appendChild(fila(it)); });
    });
  }

  document.querySelectorAll('.pill[data-estado]').forEach(function(b){
    b.addEventListener('click', function(){
      estado = this.dataset.estado;
      document.querySelectorAll('.pill[data-estado]').forEach(function(x){ x.classList.remove('on'); });
      this.classList.add('on');
      cargar();
    });
  });

  busca.addEventListener('input', function(){ cargar(); });

  tbody.addEventListener('click', function(e){
    var idA = e.target.getAttribute('data-a');
    var idX = e.target.getAttribute('data-x');
    var idF = e.target.getAttribute('data-fin');

    if (idA){
      fetch(api + '?action=detalle&id=' + encodeURIComponent(idA))
        .then(r=>r.json()).then(function(j){
          if(!j.ok) return;
          var s = j.data;
          m.id = s.id_reserva;
          m.ficha.textContent = s.fichaReserva;
          m.libro.textContent = s.libro_nombre;
          m.autorLibro.textContent = s.libro_autor;
          m.nom.textContent = s.nombre;
          m.ape.textContent = s.apellido;
          m.ci.textContent = s.CI;
          m.imgLibro.src = base + '/public/images/books/' + (s.foto_libro || 'default-book.png');
          m.imgUser.src  = base + '/public/images/users/' + (s.fotografia || 'default-user.png');
          m.box.style.display = 'grid';
        });
    }

    if (idX){
      var fd = new URLSearchParams(); fd.append('action','rechazar'); fd.append('id',idX);
      fetch(api,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:fd})
      .then(r=>r.json()).then(function(){ cargar(); });
    }

    if (idF){
      fetch(api + '?action=detalle&id=' + encodeURIComponent(idF))
        .then(r=>r.json()).then(function(j){
          if(!j.ok) return;
          var s = j.data;
          d.id = s.id_reserva;
          d.libro.textContent = s.libro_nombre;
          d.autorLibro.textContent = s.libro_autor;
          d.nom.textContent = s.nombre;
          d.ape.textContent = s.apellido;
          d.ci.textContent = s.CI;
          d.imgLibro.src = base + '/public/images/books/' + (s.foto_libro || 'default-book.png');
          d.imgUser.src  = base + '/public/images/users/' + (s.fotografia || 'default-user.png');
          d.box.style.display = 'grid';
        });
    }
  });

  m.btnCancel.addEventListener('click', function(){ m.box.style.display='none'; });
  m.btnOk.addEventListener('click', function(){
    if(!m.id) return;
    var fd = new URLSearchParams(); fd.append('action','autorizar'); fd.append('id',m.id);
    fetch(api,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:fd})
      .then(r=>r.json()).then(function(){ m.box.style.display='none'; cargar(); });
  });

  d.btnCancel.addEventListener('click', function(){ d.box.style.display='none'; });
  d.btnOk.addEventListener('click', function(){
    if(!d.id) return;
    var fd = new URLSearchParams(); fd.append('action','finalizar'); fd.append('id',d.id);
    fetch(api,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:fd})
      .then(r=>r.json()).then(function(){ d.box.style.display='none'; cargar(); });
  });

  document.querySelector('.pill[data-estado="'+estado+'"]').classList.add('on');
  cargar();
})();
