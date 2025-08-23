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

  function cargar(){
    var url = api + '?action=list&rol=' + encodeURIComponent(rol) + '&q=' + encodeURIComponent(busca.value||'');
    fetch(url).then(r=>r.json()).then(j=>{
      tbody.innerHTML = '';
      if(!j.ok) return;
      j.data.forEach(function(it){ tbody.appendChild(fila(it)); });
    });
  }

  document.querySelectorAll('.pill[data-rol]').forEach(function(b){
    b.addEventListener('click', function(){
      rol = this.dataset.rol;
      document.querySelectorAll('.pill[data-rol]').forEach(function(x){ x.classList.remove('on'); });
      this.classList.add('on');
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
})();
