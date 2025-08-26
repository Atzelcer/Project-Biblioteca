(function(){
  if ((document.body.dataset.sec||'') !== 'usuarios') return;
  var base = document.body.dataset.base || '';
  var api  = base + '/src/entradas/api_usuarios.php';
  var tbody = document.getElementById('tbodyUsuarios');
  var busca = document.getElementById('buscaUsr');
  var rol = 'todos';

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
    btn: document.getElementById('btnCerrarUsr')
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

  function fila(u){
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td>'+u.CI+'</td>'+
      '<td>'+u.nombre+'</td>'+
      '<td>'+u.apellido+'</td>'+
      '<td>'+u.rol+'</td>'+
      '<td><button class="btn primary" data-id="'+u.id_usuario+'">Ver más info</button></td>';
    return tr;
  }

  function filaBloqueados(u) {
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td>' + u.ticket + '</td>' +
      '<td>' + u.nombre + '</td>' +
      '<td>' + u.CI + '</td>' +
      '<td>' + u.libro + '</td>' +
      '<td>' + u.autor + '</td>' +
      '<td>' + (u.fechaDevolucionReal || '-') + '</td>' +
  '<td>' + u.diasExpiracion + '</td>' +
  '<td><button class="btn warning desbloq" data-id="' + u.id_reserva + '">Desbloquear</button></td>';
    return tr;
}

 
function actualizarVisibilidadTabla() {
    var tablaBloqueados = document.getElementById('tablaBloqueados');
    var tablaUsuarios = document.getElementById('tablaUsuarios');

    if (!tablaBloqueados || !tablaUsuarios) return;

    if (rol === 'bloqueados') {
        tablaBloqueados.style.display = 'block';
        tablaUsuarios.style.display = 'none';
    } else {
        tablaBloqueados.style.display = 'none';
        tablaUsuarios.style.display = 'block';
    }
}


   
  var cache = { bloqueados: [] };

  // Helper: ahora acepta opciones (p.ej., { method: 'POST', body: FormData })
  function getJSON(url, opts){
    return fetch(url, opts).then(function(r){ return r.json(); });
  }

  function cargar(){
    actualizarVisibilidadTabla();

   
    var url = rol === 'bloqueados' 
        ? api + '?action=bloqueados&q=' + encodeURIComponent(busca.value||'')
        : api + '?action=list&rol=' + encodeURIComponent(rol) + '&q=' + encodeURIComponent(busca.value||'');

  getJSON(url)
      .then(function(j){
        if(!j.ok){
          // Mostrar mensaje cuando no autorizado u otro error
          if(rol === 'bloqueados'){
            var tbB = document.getElementById('tbodyBloqueados');
            if(tbB){ tbB.innerHTML = '<tr><td colspan="8">' + (j.msg||'No autorizado o error') + '</td></tr>'; }
          } else {
            tbody.innerHTML = '<tr><td colspan="5">' + (j.msg||'No autorizado o error') + '</td></tr>';
          }
          return;
        }

  if(rol === 'bloqueados'){
            var tbodyBloqueados = document.getElementById('tbodyBloqueados');
            if(tbodyBloqueados){
                tbodyBloqueados.innerHTML='';
    cache.bloqueados = j.data || [];
    if(cache.bloqueados.length){
      cache.bloqueados.forEach(function(it){ tbodyBloqueados.appendChild(filaBloqueados(it)); });
                } else {
                  tbodyBloqueados.innerHTML = '<tr><td colspan="8">Sin resultados</td></tr>';
                }
            }
        } else {
            tbody.innerHTML = '';
            if(j.data && j.data.length){
              j.data.forEach(function(it){ tbody.appendChild(fila(it)); });
            } else {
              tbody.innerHTML = '<tr><td colspan="5">Sin resultados</td></tr>';
            }
        }

        actualizarVisibilidadTabla();
      })
      .catch(function(err){
        if(rol === 'bloqueados'){
          var tbB = document.getElementById('tbodyBloqueados');
          if(tbB){ tbB.innerHTML = '<tr><td colspan="8">Error de red o del servidor</td></tr>'; }
        } else {
          tbody.innerHTML = '<tr><td colspan="5">Error de red o del servidor</td></tr>';
        }
      });
}


  document.querySelectorAll('.pill[data-rol]').forEach(function(b){
    b.addEventListener('click', function(){
      rol = this.dataset.rol;
      document.querySelectorAll('.pill[data-rol]').forEach(function(x){ x.classList.remove('on'); });
      this.classList.add('on');
      var tablaUsuarios = document.getElementById('tablaUsuarios');
      var tablaBloqueados = document.getElementById('tablaBloqueados');
      if(rol ==='bloqueados'){
        if(tablaUsuarios) tablaUsuarios.style.display ='none';
        if(tablaBloqueados) tablaBloqueados.style.display='block';
      }else{
        if(tablaUsuarios) tablaUsuarios.style.display ='block';
        if(tablaBloqueados)tablaBloqueados.style.display='none';
      }
      cargar();
    });
  });

  busca.addEventListener('input', function(){ cargar(); });

  tbody.addEventListener('click', function(e){
    var id = e.target.getAttribute('data-id');
    if (!id) return;
    fetch(api + '?action=detalle&id=' + encodeURIComponent(id))
      .then(r=>r.json()).then(function(j){
        if(!j.ok) return;
        var d = j.data;
        m.t.textContent   = 'Información Usuario ' + d.nombre;
        m.ci.textContent  = d.CI || '';
        m.nom.textContent = d.nombre || '';
        m.ape.textContent = d.apellido || '';
        m.rol.textContent = d.rol || '';
        m.correo.textContent = d.correo || '';
        m.cel.textContent = d.celular || '';
        m.dir.textContent = d.direccion || '';
        m.foto.src = base + '/public/images/users/' + (d.fotografia || 'default-user.png');
        m.box.style.display = 'grid';
      });
  });

  m.btn.addEventListener('click', function(){ m.box.style.display = 'none'; });

  document.querySelector('.pill[data-rol="'+rol+'"]').classList.add('on');
  cargar();

  // Eventos para desbloquear desde la tabla de bloqueados
  document.getElementById('tbodyBloqueados')?.addEventListener('click', function(e){
    var btn = e.target.closest('button.desbloq');
    if(!btn) return;
    var id = btn.getAttribute('data-id');
    mDes.currentId = id;
    // Buscar la fila de datos en memoria si está cargada en DOM
    // O bien solicitar detalle específico si fuera necesario. Aquí usamos los datos de la fila creada.
    // Recorremos hacia el <tr> y extraemos celdas visibles y buscamos en una lista si fuera necesario.
    var tr = btn.closest('tr');
    if(tr){
      // Como el API de bloqueados trae todos los campos, buscaremos el objeto en la última respuesta si la guardamos
    }
    // Alternativa simple: volver a consultar la lista y tomar el item del id
    // Buscar en cache para evitar nueva petición
    var it = (cache.bloqueados||[]).find(function(x){ return String(x.id_reserva) === String(id); });
    if(!it){
      // Si no está en cache, reconsultar rápidamente
      return getJSON(api + '?action=bloqueados&q=' + encodeURIComponent(busca.value||''))
        .then(function(j){ if(!j.ok) return; cache.bloqueados = j.data||[]; return (cache.bloqueados||[]).find(function(x){return String(x.id_reserva)===String(id);}); })
        .then(function(it2){ if(!it2) return; it = it2; mostrarModalDesbloqueo(it); });
    }
    if(it){ mostrarModalDesbloqueo(it); }
    function mostrarModalDesbloqueo(it){
      mDes.t.textContent = it.ticket || '';
      mDes.user.textContent = (it.nombre||'') + (it.apellido?(' '+it.apellido):'');
      mDes.ci.textContent = it.CI || '';
      mDes.libro.textContent = it.libro || '';
      mDes.autor.textContent = it.autor || '';
      mDes.fecha.textContent = it.fechaDevolucion || '-';
      mDes.dias.textContent = it.diasExpiracion || 0;
      mDes.imgLibro.src = base + '/public/images/books/' + (it.foto_libro || 'default-book.png');
      mDes.imgUser.src  = base + '/public/images/users/' + (it.fotografia || 'default-user.png');
      mDes.box.style.display = 'grid';
    }
  });

  mDes.btnCancel?.addEventListener('click', function(){
    mDes.currentId = null;
    mDes.box.style.display = 'none';
  });

  mDes.btnOk?.addEventListener('click', function(){
    if(!mDes.currentId) return;
  var fd = new FormData(); fd.append('id', mDes.currentId);
  getJSON(api + '?action=desbloquear', { method: 'POST', body: fd })
      .then(function(j){
        if(!j.ok){
          alert(j.msg || 'No se pudo desbloquear');
          return;
        }
        mDes.currentId = null;
        mDes.box.style.display = 'none';
    // Refrescar bloqueados
    cargar();
      })
      .catch(function(){ alert('Error de red'); });
  });
})();