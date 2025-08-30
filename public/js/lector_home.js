window.LectorHome = (function(){
  let A;

  // 12 categorías con tinte + una portada base (estática para no depender del backend)
  const CATS = [
    {key:'programacion', name:'Programación', tint:'#19c37d', cover:'Clean_Code.jpg'},
    {key:'matematicas',  name:'Matemáticas',  tint:'#4a9bff',  cover:'Analisis_Matematico.jpg'},
    {key:'fisica',       name:'Física',       tint:'#ffb74a',  cover:'Fisica_Universitaria.jpg'},
    {key:'historia',     name:'Historia',     tint:'#ff6b6b',  cover:'Historia_Universal.jpg'},
    {key:'idiomas',      name:'Idiomas',      tint:'#7dd3fc',  cover:'Business_English.jpg'},
    {key:'arquitectura', name:'Arquitectura', tint:'#a78bfa',  cover:'Clean_Architecture.jpg'},
    {key:'quimica',      name:'Química',      tint:'#34d399',  cover:'Quimica_General.jpg'},
    {key:'literatura',   name:'Literatura',   tint:'#f472b6',  cover:'Cien_Anos_de_Soledad.jpg'},
    {key:'ingenieria',   name:'Ingeniería',   tint:'#22d3ee',  cover:'Introduction_to_Algorithms.jpg'},
    {key:'electronica',  name:'Electrónica',  tint:'#60a5fa',  cover:'Electromagnetismo.jpg'},
    {key:'biologia',     name:'Biología',     tint:'#86efac',  cover:'Biologia.jpg'},          // si no existe, cae a default
    {key:'arte',         name:'Arte',         tint:'#f59e0b',  cover:'Historia_del_Arte.jpg'}
  ];

  function buildCategoryCards(){
    const wrap = A.q('#catsWrap'); if(!wrap) return;
    wrap.innerHTML = '';
    CATS.forEach(cat=>{
      const cover = A.base+'/public/images/books/'+(cat.cover||'default-book.png');
      const card = document.createElement('div');
      card.className = 'catCard';
      card.style.setProperty('--img', `url('${cover}')`);
      card.style.setProperty('--tint', cat.tint);
      card.innerHTML = `<div class="tint"></div><div class="name">${cat.name}</div>`;
      card.addEventListener('click', (e)=>{
        e.preventDefault();
        const u=new URL(location.href); u.searchParams.set('cat',cat.key); u.searchParams.delete('q'); history.pushState({},'',u);
        document.querySelector('[data-section="reservar"]')
          ?.dispatchEvent(new MouseEvent('click',{bubbles:true,cancelable:true}));
      });
      wrap.appendChild(card);
    });
  }

  function init(app){
    A = app;
    // eventos home estándar
    A.q('#homeTicketCard')?.addEventListener('click', ()=>{ if(A.latest){ A.fillTicket(A.latest); A.modals.show('#mTicket'); } });
    A.q('#goTicket')?.addEventListener('click', ()=>{ if(A.latest){ A.fillTicket(A.latest); A.modals.show('#mTicket'); } });
    buildCategoryCards();
  }

  return { init };
})();
