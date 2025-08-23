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
})();
