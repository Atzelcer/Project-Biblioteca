document.addEventListener('DOMContentLoaded',function(){
  var f = document.querySelector('input[name="foto"]');
  if(!f) return;
  f.addEventListener('change',function(){
    if (!this.files || !this.files[0]) return;
    var name = this.files[0].name;
    this.title = name;
  });
});
