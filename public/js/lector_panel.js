(function(){
  if((document.body.dataset.sec||"")!=="lector") return;

  /* ============== BASE robusto ============== */
  function detectBase(){
    const b = (document.body.getAttribute('data-base') || '').trim();
    if (b) return b.replace(/\/+$/,'');
    const s = document.querySelector('script[src*="/public/js/lector_panel.js"]');
    if (s) {
      try{
        const u = new URL(s.getAttribute('src'), location.href);
        const idx = u.pathname.indexOf('/public/js/');
        if (idx > -1) return u.pathname.slice(0, idx);
      }catch{}
    }
    const segs = location.pathname.split('/').filter(Boolean);
    return segs.length ? ('/'+segs[0]) : '';
  }
  const base = detectBase();

  /* ============== Helpers y estado global ============== */
  const q  = s => document.querySelector(s);
  const qa = s => Array.from(document.querySelectorAll(s));
  const show = (el,mode) => el && (el.style.display = mode || 'grid');
  const hide = el => el && (el.style.display = 'none');
  const getQS = (key) => new URLSearchParams(location.search).get(key) || '';
  const setQS = (key, val) => { const u = new URL(location.href); (val&&String(val).trim()!=='')?u.searchParams.set(key,val):u.searchParams.delete(key); history.pushState({},'',u) };

  let latest = null; // última reserva

  function normalize(r){
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
  function fillTicket(d){
    d = normalize(d) || {};
    q('#rFichaReserva') && (q('#rFichaReserva').textContent = d.fichaReserva || '');
    q('#tLibro') && (q('#tLibro').textContent = d.libro ? ('Libro: '+d.libro) : '');
    q('#tAutor') && (q('#tAutor').textContent = d.autor ? ('Autor: '+d.autor) : '');
    q('#tEstado') && (q('#tEstado').textContent = d.estado ? ('Estado: '+d.estado) : '');
    const code = q('#homeTicketCode'); if(code) code.textContent = d.fichaReserva || '—';
    const btnExt=q('#btnExtender'); btnExt && (btnExt.style.display=((d.estado||'').toLowerCase()==='autorizada')?'inline-block':'none');
  }
  function toggleTicketBtn(){
    const b=q('#btnTicket'); if(!b) return;
    if(latest){
      b.style.display='inline-block';
      b.textContent=latest.fichaReserva || 'Ticket';
      b.classList.toggle('ticket--ok', (latest.estado||'').toLowerCase()==='autorizada');
    }else{
      b.style.display='none';
      b.classList.remove('ticket--ok');
    }
  }
  function fetchLatest(){
    fetch(base+'/src/entradas/api_mis_reservas.php?action=ultima')
      .then(r=>r.json())
      .then(j=>{ latest = (j && j.ok) ? normalize(j.data) : null; toggleTicketBtn(); fillTicket(latest); })
      .catch(()=>{ latest=null; toggleTicketBtn(); });
  }

  // exposición para módulos
  window.AppLector = {
    base,q,qa,show,hide,getQS,setQS,
    normalize,fillTicket,fetchLatest,
    get latest(){ return latest; }, set latest(v){ latest=v; toggleTicketBtn(); },
    modals:{ show:id=>show(q(id)), hide:id=>hide(q(id)), hideAll:()=>{} }
  };

  /* ============== Tema y sidebar ============== */
  const themeChk = q('#themeToggle');
  function setTheme(t){ document.documentElement.setAttribute('data-theme', t); localStorage.setItem('theme', t); themeChk && (themeChk.checked = (t==='light')); }
  setTheme(localStorage.getItem('theme') || 'dark');
  themeChk && themeChk.addEventListener('change', ()=> setTheme(themeChk.checked ? 'light' : 'dark'));

  const side=q('#sidebar');
  if(localStorage.getItem('sidebarMini')==='1'){ side.classList.add('mini'); document.body.classList.add('side-mini'); }
  q('#sideToggle')?.addEventListener('click', ()=>{
    side.classList.toggle('mini'); document.body.classList.toggle('side-mini');
    localStorage.setItem('sidebarMini', document.body.classList.contains('side-mini')?'1':'0');
  });

  /* ============== Perfil modal ============== */
  (function(){
    const mdl=q('#profileModal'), mback=q('#modalBackdrop'), mclose=q('#modalClose'), avatarBtn=q('#avatarBtn');
    const openM=()=>mdl?.classList.add('show'), closeM=()=>mdl?.classList.remove('show');
    avatarBtn?.addEventListener('click',openM); mclose?.addEventListener('click',closeM); mback?.addEventListener('click',closeM);
    const uimg = (document.body.dataset.userImg||'').includes('{{')? (base+'/public/images/users/default-user.png') : (document.body.dataset.userImg|| (base+'/public/images/users/default-user.png'));
    const uname = (document.body.dataset.userName||'Lector').trim() || 'Lector';
    const topImg = q('#avatarImg'); if(topImg) topImg.src = uimg;
    const modImg = q('#modalUserImg'); if(modImg) modImg.src = uimg;
    const profName = q('#helloUser'); if(profName) profName.textContent = uname;
    const modName  = q('#modalUserName'); if(modName) modName.textContent = uname;
  })();

  /* ============== Modales ticket/aviso/extender ============== */
  const overlayIds=['#mAviso','#mTicket','#mConfirm','#mExtender','#mPreview'];
  const hideAll=()=>overlayIds.forEach(id=>{ const el=q(id); el && (el.style.display='none'); });
  window.AppLector.modals.hideAll = hideAll;
  function showModal(id){ hideAll(); show(q(id)); }

  q('#btnTicket')?.addEventListener('click', ()=>{ if(latest){ fillTicket(latest); showModal('#mTicket'); } });
  q('#btnCerrarAviso')?.addEventListener('click', ()=>hide(q('#mAviso')));
  q('#btnCerrarTicket')?.addEventListener('click', ()=>hide(q('#mTicket')));
  q('#btnExtender')?.addEventListener('click', ()=>show(q('#mExtender')));
  q('#btnCancelExt')?.addEventListener('click', ()=>hide(q('#mExtender')));
  q('#btnConfirmExt')?.addEventListener('click', ()=>{
    const idRes = latest && latest.id_reserva; if(!idRes){ alert('No se encontró la reserva'); return; }
    const fd=new URLSearchParams(); fd.append('id', idRes);
    fetch(base+'/src/entradas/api_reservas.php?action=extender', {
      method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:fd
    }).then(r=>r.json()).then(j=>{
      if(j && j.ok){
        latest.fechaDevolucion=j.nuevaFecha||latest.fechaDevolucion||null;
        latest.estado='autorizada'; fillTicket(latest); hide(q('#mExtender')); toggleTicketBtn();
        alert('Se extendió la reserva por 1 día más');
      }else{
        alert((j&&j.msg)?j.msg:'No se pudo extender la reserva');
      }
    }).catch(()=>alert('Error en el servidor'));
  });

  /* ============== Router de vistas ============== */
  const VIEW_CANDIDATES = {
    home: [
      base + '/src/vistas/panel/lector_home.html',
      base + '/src/vistas/panel/partials/lector_home.html'
    ],
    reservar: [
      base + '/src/vistas/panel/lector_reservas.html',
      base + '/src/vistas/panel/partials/lector_reservas.html'
    ],
    historial: [
      base + '/src/vistas/panel/lector_historial.html',
      base + '/src/vistas/panel/partials/lector_historial.html'
    ]
  };

  function markActive(name){ qa('.nav .navItem').forEach(a=> a.classList.toggle('active', a.dataset.section===name)); }
  function toggleSearchOn(section){
    const topbar=q('#topbar'), searchWrap=q('#searchWrap');
    if(section==='reservar'){ searchWrap.style.display=''; topbar.classList.remove('topbar--home'); }
    else if(section==='home'){ searchWrap.style.display='none'; topbar.classList.add('topbar--home'); }
    else { searchWrap.style.display='none'; topbar.classList.remove('topbar--home'); }
  }

  const loadedScripts = new Set();
  function loadScriptOnce(key, url, cb){
    if(loadedScripts.has(key)){ cb && cb(); return; }
    const s = document.createElement('script');
    s.src = url + (url.includes('?')?'&':'?') + 'v=22';
    s.onload = ()=>{ loadedScripts.add(key); cb && cb(); };
    s.onerror = ()=>{ console.error('No se pudo cargar', url); cb && cb(); };
    document.head.appendChild(s);
  }

  async function loadView(name){
    markActive(name); toggleSearchOn(name);
    const cont = q('#content');
    cont.innerHTML = '<div style="opacity:.6">Cargando…</div>';

    const urls = VIEW_CANDIDATES[name] || VIEW_CANDIDATES.home;
    let ok=false, html='';
    for(const url of urls){
      try{
        const res = await fetch(url+'?v=22',{cache:'no-store'});
        if(res.ok){ html = await res.text(); ok=true; break; }
      }catch{}
    }
    if(!ok){
      cont.innerHTML = `<div class="card" style="padding:16px"><h3>No pude cargar <code>${name}</code></h3></div>`;
      return;
    }
    cont.innerHTML = html;

    if(name==='home'){
      loadScriptOnce('home', base+'/public/js/lector_home.js', ()=>{ window.LectorHome && window.LectorHome.init(window.AppLector); });
    }else if(name==='reservar'){
      loadScriptOnce('reservas', base+'/public/js/lector_reservas.js', ()=>{ window.LectorReservas && window.LectorReservas.init(window.AppLector); });
    }else if(name==='historial'){
      loadScriptOnce('historial', base+'/public/js/lector_historial.js', ()=>{ window.LectorHistorial && window.LectorHistorial.init(window.AppLector); });
    }
  }

  document.addEventListener('click', e=>{
    const link = e.target.closest('[data-section]'); if(!link) return;
    const sec = link.dataset.section; if(!VIEW_CANDIDATES[sec]) return;
    e.preventDefault(); loadView(sec);
  });

  q('#searchBox')?.addEventListener('submit', e=>{
    e.preventDefault();
    const term = (q('#searchInput')?.value || '').trim();
    setQS('q', term);
    loadView('reservar');
  });

  loadView(getQS('q') ? 'reservar' : 'home');

  /* ============== Partículas (decorativo) ============== */
  (function(){
    const cvs=q('#bgParticles'); if(!cvs) return;
    const ctx=cvs.getContext('2d');
    const parts=[], imgs=[]; let w=0,h=0; const dpr=Math.max(1,window.devicePixelRatio||1);
    const bookImgs=[
      base+'/public/images/books/Clean_Code.jpg',
      base+'/public/images/books/Analisis_Real.jpg',
      base+'/public/images/books/Code_Complete.jpg',
      base+'/public/images/books/Clean_Architecture.jpg',
      base+'/public/images/books/The_Pragmatic_Programmer.jpg'
    ];
    function preload(arr,cb){let c=0;arr.forEach(s=>{const im=new Image();im.src=s;im.onload=()=>{imgs.push(im); if(++c===arr.length) cb&&cb();};});}
    function resize(){w=innerWidth;h=innerHeight;cvs.width=w*dpr;cvs.height=h*dpr;cvs.style.width=w+'px';cvs.style.height=h+'px';ctx.setTransform(dpr,0,0,dpr,0,0);}
    function rand(a,b){return a+Math.random()*(b-a);} function pick(a){return a[(Math.random()*a.length)|0];}
    function spawn(n){for(let i=0;i<n;i++){parts.push({x:rand(0,w),y:rand(0,h),w:rand(32,64),h:rand(40,80),vx:rand(-.4,.4),vy:rand(-.3,.3),alpha:rand(.35,.65),img:pick(imgs),angle:rand(0,Math.PI*2),va:rand(-.005,.005)})}}
    function step(){ctx.clearRect(0,0,w,h);for(const p of parts){p.x+=p.vx;p.y+=p.vy;p.angle+=p.va;if(p.x<-80)p.x=w+80;if(p.x>w+80)p.x=-80;if(p.y<-80)p.y=h+80;if(p.y>h+80)p.y=-80;ctx.save();ctx.globalAlpha=p.alpha;ctx.translate(p.x,p.y);ctx.rotate(p.angle);ctx.drawImage(p.img,-p.w/2,-p.h/2,p.w,p.h);ctx.restore();}requestAnimationFrame(step)}
    addEventListener('resize',resize); resize(); preload(bookImgs,()=>{spawn(20);step();});
  })();

  fetchLatest();
})();
