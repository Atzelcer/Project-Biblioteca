document.addEventListener('DOMContentLoaded',function(){
  var first = document.querySelector('input[name="usuario"]');
  if(first) first.focus();
  var msgErr = document.querySelector('.msg.err');
  if(msgErr && msgErr.textContent.trim() === 'bloqueado'){
    alert('Estas bloqueado');
  }
});
