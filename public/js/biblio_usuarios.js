(function(){
  if ((document.body.dataset.sec||'') !== 'usuarios') return;

  var base = document.body.dataset.base || '';
  var api  = base + '/src/entradas/api_usuarios.php';

  var tbody = document.getElementById('tbodyUsuarios');
  var tbodyBloq = document.getElementById('tbodyBloqueados');
  var busca = document.getElementById('buscaUsr');
  var rol = 'todos';

  // Modal info usuario
  var m = {
    box: document.getElementById('mUsr'),
    t:   document.getElementById('tUsr'),
    ci:  document.getElementById('uCI'),
    nom: document.getElementById('uNom'),
    ape: document.getElementById('uApe'),
    rol: document.getElementById('uRol'),
    correo: document.getElementById('uCorreo'),
    cel: document.getElementById('uCel'),
    dir: document.getElementById('uDir'),
    foto: document.getElementById('uFoto'),
    btn: document.getElementById('btnCerrarUsr'),
    btnBloquear: document.getElementById('btnBloquearUsr'),
    currentUserId: null
  };

  // Modal desbloqueo
  var mDes = {
    box: document.getElementById('mDesbloq'),
    t: document.getElementById('desbTicket'),
    user: document.getElementById('desbUser'),
    ci: document.getElementById('desbCI'),
    libro: document.getElementById('desbLibro'),
    autor: document.getElementById('desbAutor'),
    fecha: document.getElementById('desbFechaLimite'),
    dias: document.getElementById('desbDias'),
    imgLibro: document.getElementById('desbImgLibro'),
    imgUser: document.getElementById('desbImgUser'),
    btnOk: document.getElementById('btnOkDesbloq'),
    btnCancel: document.getElementById('btnCancelDesbloq'),
    currentId: null
  };

  // cache en memoria
  var cache = { bloqueados: [] };

  // helpers
  function getJSON(url, opts){ return fetch(url, opts).then(function(r){ return r.json(); }); }

  function fila(u){
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td>'+ (u.CI||'') +'</td>'+
      '<td>'+ (u.nombre||'') +'</td>'+
      '<td>'+ (u.apellido||'') +'</td>'+
      '<td>'+ (u.rol||'') +'</td>'+
      '<td>'+ (u.estado||'normal') +'</td>'+
      '<td><button class="btn primary" data-id="'+u.id_usuario+'">Ver mas info</button></td>';
    return tr;
  }

  function filaBloqueados(u){
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td>' + (u.CI||'') + '</td>' +
      '<td>' + (u.nombre||'') + '</td>' +
      '<td>' + (u.apellido||'') + '</td>' +
      '<td>' + (u.rol||'lector') + '</td>' +
      '<td>' + (u.estado||'bloqueado') + '</td>' +
      '<td><button class="btn warning desbloq" data-id="' + (u.id_reserva||'') + '">Desbloquear</button></td>';
    return tr;
  }

  function actualizarVisibilidadTabla(){
    var tablaBloqueados = document.getElementById('tablaBloqueados');
    var tablaUsuarios = document.getElementById('tablaUsuarios');
    if(!tablaBloqueados || !tablaUsuarios) return;
    
    if(rol === 'bloqueados'){
      tablaBloqueados.style.display = 'block';
      tablaUsuarios.style.display = 'none';
      // Limpiar tabla al mostrarla
      if(tbodyBloq) tbodyBloq.innerHTML = '';
    }else{
      tablaBloqueados.style.display = 'none';
      tablaUsuarios.style.display = 'block';
      // Limpiar tabla al mostrarla
      if(tbody) tbody.innerHTML = '';
    }
  }

  function cargar(){
    actualizarVisibilidadTabla();

    var url = (rol === 'bloqueados')
      ? api + '?action=bloqueados&q=' + encodeURIComponent(busca.value||'')
      : api + '?action=list&rol=' + encodeURIComponent(rol) + '&q=' + encodeURIComponent(busca.value||'');

    getJSON(url).then(function(j){
      if(!j.ok){
        if(rol === 'bloqueados'){
          if(tbodyBloq){ tbodyBloq.innerHTML = '<tr><td colspan="6">'+(j.msg||'No autorizado o error')+'</td></tr>'; }
        }else{
          if(tbody){ tbody.innerHTML = '<tr><td colspan="6">'+(j.msg||'No autorizado o error')+'</td></tr>'; }
        }
        return;
      }

      if(rol === 'bloqueados'){
        cache.bloqueados = j.data || [];
        if(!tbodyBloq) return;
        tbodyBloq.innerHTML = ''; // Limpiar completamente la tabla
        if(cache.bloqueados.length){
          cache.bloqueados.forEach(function(it){ 
            tbodyBloq.appendChild(filaBloqueados(it)); 
          });
        }else{
          tbodyBloq.innerHTML = '<tr><td colspan="6">Sin resultados</td></tr>';
        }
      }else{
        if(!tbody) return;
        tbody.innerHTML = '';
        if(j.data && j.data.length){
          j.data.forEach(function(it){ tbody.appendChild(fila(it)); });
        }else{
          tbody.innerHTML = '<tr><td colspan="6">Sin resultados</td></tr>';
        }
      }
    }).catch(function(){
      if(rol === 'bloqueados'){
        if(tbodyBloq){ tbodyBloq.innerHTML = '<tr><td colspan="6">Error de red o del servidor</td></tr>'; }
      }else{
        if(tbody){ tbody.innerHTML = '<tr><td colspan="6">Error de red o del servidor</td></tr>'; }
      }
    });
  }

  // tabs
  document.querySelectorAll('.pill[data-rol]').forEach(function(b){
    b.addEventListener('click', function(){
      rol = this.dataset.rol;
      document.querySelectorAll('.pill[data-rol]').forEach(function(x){ x.classList.remove('on'); });
      this.classList.add('on');
      cargar();
    });
  });

  // buscar
  busca.addEventListener('input', cargar);

  // ver info usuario
  tbody && tbody.addEventListener('click', function(e){
    var id = e.target.getAttribute('data-id');
    if(!id) return;
    getJSON(api + '?action=detalle&id=' + encodeURIComponent(id)).then(function(j){
      if(!j.ok) return;
      var d = j.data;
      m.currentUserId = id;
      m.t.textContent   = 'Informacion Usuario ' + (d.nombre||'');
      m.ci.textContent  = d.CI || '';
      m.nom.textContent = d.nombre || '';
      m.ape.textContent = d.apellido || '';
      m.rol.textContent = d.rol || '';
      m.correo.textContent = d.correo || '';
      m.cel.textContent = d.celular || '';
      m.dir.textContent = d.direccion || '';
      m.foto.src = base + '/public/images/users/' + ((d.fotografia && d.fotografia.trim()) ? d.fotografia : 'default-user.png');
      
      // Mostrar/ocultar botón de bloquear según el estado del usuario
      if(m.btnBloquear) {
        if(d.estado === 'normal') {
          m.btnBloquear.style.display = 'inline-block';
          m.btnBloquear.textContent = 'Bloquear Usuario';
        } else {
          m.btnBloquear.style.display = 'none';
        }
      }
      
      m.box.style.display = 'grid';
    });
  });

  m.btn && m.btn.addEventListener('click', function(){ 
    m.currentUserId = null;
    m.box.style.display = 'none'; 
  });

  // bloquear usuario
  m.btnBloquear && m.btnBloquear.addEventListener('click', function(){
    if(!m.currentUserId) { alert('Error: ID de usuario no válido'); return; }
    
    var confirmMsg = '¿Estás seguro de que quieres bloquear a este usuario?\n\nEsto restringirá su acceso al sistema.';
    if(!confirm(confirmMsg)) return;
    
    var fd = new FormData();
    fd.append('id', m.currentUserId);
    getJSON(api + '?action=bloquear', { method: 'POST', body: fd })
      .then(function(j){
        if(!j.ok){ 
          alert(j.msg || 'No se pudo bloquear el usuario'); 
          return; 
        }
        alert('Usuario bloqueado exitosamente');
        m.currentUserId = null;
        m.box.style.display = 'none';
        cargar(); // refrescar lista
      })
      .catch(function(){ 
        alert('Error de red al intentar bloquear el usuario'); 
      });
  });

  // abrir modal desbloqueo
  tbodyBloq && tbodyBloq.addEventListener('click', function(e){
    var btn = e.target.closest('button.desbloq');
    if(!btn) return;

    var id = btn.getAttribute('data-id');
    if(!id){ alert('id invalido'); return; }

    mDes.currentId = id;

    var it = (cache.bloqueados||[]).find(function(x){ return String(x.id_reserva) === String(id); });

    function mostrar(it){
      mDes.t.textContent = it.ticket || '';
      mDes.user.textContent = (it.nombre||'') + (it.apellido?(' '+it.apellido):'');
      mDes.ci.textContent = it.CI || '';
      mDes.libro.textContent = it.libro || '';
      mDes.autor.textContent = it.autor || '';
      mDes.fecha.textContent = it.fechaDevolucion || '-';
      mDes.dias.textContent = it.diasExpiracion || 0;

      mDes.imgLibro.src = base + '/public/images/books/' + ((it.foto_libro && it.foto_libro.trim()) ? it.foto_libro : 'default-book.png');
      mDes.imgUser.src  = base + '/public/images/users/' + ((it.fotografia && it.fotografia.trim()) ? it.fotografia : 'default-user.png');

      mDes.box.style.display = 'grid';
    }

    if(it){ mostrar(it); }
    else{
      // si por alguna razon no esta en cache, recargamos
      getJSON(api + '?action=bloqueados&q=' + encodeURIComponent(busca.value||''))
        .then(function(j){
          if(!j.ok) return;
          cache.bloqueados = j.data||[];
          var it2 = cache.bloqueados.find(function(x){ return String(x.id_reserva) === String(id); });
          if(it2) mostrar(it2);
        });
    }
  });

  // cerrar modal desbloqueo
  mDes.btnCancel && mDes.btnCancel.addEventListener('click', function(){
    mDes.currentId = null;
    mDes.box.style.display = 'none';
  });

  // confirmar desbloqueo
  mDes.btnOk && mDes.btnOk.addEventListener('click', function(){
    if(!mDes.currentId){ alert('id invalido'); return; }
    var fd = new FormData();
    fd.append('id', mDes.currentId);
    getJSON(api + '?action=desbloquear', { method: 'POST', body: fd })
      .then(function(j){
        if(!j.ok){ alert(j.msg || 'No se pudo desbloquear'); return; }
        mDes.currentId = null;
        mDes.box.style.display = 'none';
        cargar(); // refrescar lista
      })
      .catch(function(){ alert('Error de red'); });
  });

  // init
  var pillIni = document.querySelector('.pill[data-rol="'+rol+'"]');
  if(pillIni) pillIni.classList.add('on');
  cargar();
})();
