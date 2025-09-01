(function(){
  var sidebar = document.getElementById('sidebar');
  var btnSidebar = document.getElementById('btnSidebarToggle');
  if(btnSidebar && sidebar){
    btnSidebar.onclick = function(){
      sidebar.classList.toggle('mini');
    };
  }
})();
(function(){
  var sec = document.body.getAttribute('data-sec') || 'principal';
  var enlaces = document.querySelectorAll('[data-sec-link]');
  for (var i=0;i<enlaces.length;i++){
    if (enlaces[i].getAttribute('data-sec-link') === sec) {
      enlaces[i].classList.add('activo');
    } else {
      enlaces[i].classList.remove('activo');
    }
  }

  var modal = document.getElementById('modalBibliotecario');
  var btnCancelar = document.getElementById('btnCancelarReserva');
  var btnCargar = document.getElementById('btnCargarReserva');
  var inputCI = document.getElementById('ciReserva');

  if (!modal) return;
  document.querySelectorAll('.libro').forEach(function(libro){
    libro.addEventListener('click', function(){
       inputCI.value = '';
       modal.style.display = 'flex';
        });
  });

  btnCancelar?.addEventListener('click', function(){ modal.style.display = 'none'; });
  
})();
