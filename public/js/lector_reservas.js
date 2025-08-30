window.LectorReservas = (function(){
  let A; let bound = false;

  // === Derivación de categoría si no viene desde el backend ===
  function deriveCategoryFromText(t){
    const text = (t||'').toLowerCase();
    const tests = [
      ['programacion',['program','code','python','java','javascript','algoritmo','algorithms','pattern','refactor','arquitectura','architecture','clean','pragmatic']],
      ['matematicas', ['álgebra','algebra','cálculo','calculus','lineal','matem','topología','ecuaciones','probabilidad','estadística','analysis','analisis']],
      ['fisica',      ['física','fisica','electromagnetismo','mecánica','mecanica','óptica','optica','termodinámica','termodinamica','moderna']],
      ['historia',    ['historia','universal','américa','latina']],
      ['idiomas',     ['english','inglés','ingles','grammar','business english','idioma']],
      ['arquitectura',['arquitectura','architecture']],
      ['quimica',     ['química','quimica','orgánica','organica','inorgánica','inorganica','fisicoquimica','general']],
      ['literatura',  ['quijote','soledad','crónica','cronica','literatura','novela','cuento']],
      ['ingenieria',  ['ingenier','engineer','industrial','civil','sistemas']],
      ['electronica', ['electrón','electron','digital','circuit','microcontrol']],
      ['biologia',    ['biología','biologia','genética','genetica','bioquim']],
      ['arte',        ['arte','art','pintura','escultura']]
    ];
    for(const [key,arr] of tests){ if(arr.some(w=> text.includes(w))) return key; }
    return 'otros';
  }

  function normalizeRow(tr){
    const titulo = (tr.dataset.nombre || tr.children[0]?.textContent || '').trim();
    const autor  = (tr.dataset.autor  || tr.children[1]?.textContent || '').trim();
    const foto   = (tr.dataset.foto   || 'default-book.png').trim();
    // usar la que venga, o derivar
    let cat = (tr.dataset.categoria || '').toLowerCase().trim();
    if(!cat) cat = deriveCategoryFromText(titulo+' '+autor);
    return {
      id: +(tr.dataset.id||0),
      titulo, autor, foto,
      categoria: cat
    };
  }

  function buildCards(filterText='', filterCat='all'){
    const grid=A.q('#cardsReservar'); if(!grid) return;
    const stats=A.q('#galleryStats'); const empty=A.q('#noResults');
    grid.innerHTML='';

    const rows=A.qa('#tablaDisponibles tbody tr');
    const total=rows.length;
    const term = (filterText||'').toLowerCase();
    const catWanted = (filterCat||'all').toLowerCase();

    let matches=0;
    rows.forEach(tr=>{
      const r = normalizeRow(tr);
      const okText = !term || r.titulo.toLowerCase().includes(term) || r.autor.toLowerCase().includes(term);
      const okCat  = (catWanted==='all') || (r.categoria && r.categoria.includes(catWanted));
      if(!(okText && okCat)) return;

      matches++;
      const cover = A.base+'/public/images/books/'+(r.foto||'default-book.png');
      const card=document.createElement('article');
      card.className='card cardBook';
      card.innerHTML =
        '<div class="cover" data-preview="'+r.foto+'" data-title="'+r.titulo.replace(/"/g,'&quot;')+'" data-autor="'+r.autor.replace(/"/g,'&quot;')+'">'+
          '<img src="'+cover+'" alt="" onerror="this.src=\''+A.base+'/public/images/books/default-book.png\'">'+
        '</div>'+
        '<div class="meta">'+
          '<h4 class="bookTitle">'+r.titulo+'</h4>'+
          '<p class="bookAuthor">'+r.autor+'</p>'+
          '<div><button class="btnMini reservar" '+
          'data-id="'+r.id+'" data-nombre="'+r.titulo.replace(/"/g,'&quot;')+'" '+
          'data-autor="'+r.autor.replace(/"/g,'&quot;')+'" '+
          'data-foto="'+r.foto.replace(/"/g,'&quot;')+'">Reservar</button></div>'+
        '</div>';
      grid.appendChild(card);
    });

    if(stats) stats.textContent = matches+' de '+total+' libros';
    if(empty) empty.style.display = matches ? 'none' : '';
  }

  function bindOnce(){
    if(bound) return; bound = true;

    // chips (si existen)
    A.qa('#secReservar .chip').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        A.qa('#secReservar .chip').forEach(b=> b.classList.remove('active'));
        btn.classList.add('active');
        buildCards(A.q('#searchInput')?.value||'', (btn.dataset.filter||'all'));
      });
    });

    // preview portada
    document.addEventListener('click', e=>{
      const el = e.target.closest('.cover'); if(!el || !A.q('#mPreview')) return;
      if(!A.q('#secReservar')) return;
      const foto  = el.dataset.preview || '';
      const title = el.dataset.title   || 'Portada';
      const autor = el.dataset.autor   || '';
      const pvImg = A.q('#pvImg'), pvTitle=A.q('#pvTitle'), pvAutor=A.q('#pvAutor');
      if(pvImg){ pvImg.src = A.base+'/public/images/books/'+(foto||'default-book.png'); pvImg.onerror = ()=>{ pvImg.src = A.base+'/public/images/books/default-book.png'; }; }
      if(pvTitle){ pvTitle.textContent = title; }
      if(pvAutor){ pvAutor.textContent = autor ? ('Autor: '+autor) : ''; }
      A.modals.show('#mPreview');
    });
    A.q('#pvClose')?.addEventListener('click', ()=>A.modals.hide('#mPreview'));

    // flujo de reservar
    document.addEventListener('click', e=>{
      const btn = e.target.closest('button.reservar'); if(!btn) return;
      if(!A.q('#secReservar')) return;

      const sel = {
        id:+(btn.dataset.id||0),
        nombre:btn.dataset.nombre||'',
        autor: btn.dataset.autor||'',
        foto:  btn.dataset.foto||'default-book.png'
      };
      const img=A.q('#imgLibro'); if(img){ img.src=A.base+'/public/images/books/'+(sel.foto||'default-book.png'); img.onerror=()=>{img.src=A.base+'/public/images/books/default-book.png';}; }
      A.q('#nombreLibro')&&(A.q('#nombreLibro').textContent=sel.nombre);
      A.q('#autorLibro')&&(A.q('#autorLibro').textContent=sel.autor);
      A.modals.show('#mConfirm');

      const onConfirm = ()=>{
        A.q('#btnConfirm')?.removeEventListener('click', onConfirm);
        if(!sel.id){ A.modals.hide('#mConfirm'); alert('Libro inválido'); return; }
        const fd=new URLSearchParams(); fd.append('id_libro', sel.id);
        fetch(A.base+'/src/entradas/crear_reserva.php', {
          method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:fd
        }).then(r=>r.json()).then(j=>{
          A.modals.hide('#mConfirm');
          if(!j || !j.ok){
            if(j && j.code==='YA_ACTIVA'){ A.q('#avisoMsg').textContent=j.msg||'Tienes una reserva pendiente'; A.modals.show('#mAviso'); return; }
            alert((j&&j.msg)?j.msg:'No se pudo reservar'); return;
          }
          A.latest = A.normalize({ id_reserva:j.id_reserva, fichaReserva:j.ficha, estado:j.estado||'pendiente', libro:sel.nombre, autor:sel.autor, fechaDevolucion:j.fechaDevolucion||null });
          A.fetchLatest(); A.fillTicket(A.latest); A.modals.show('#mTicket');
        }).catch(()=>{ A.modals.hide('#mConfirm'); alert('Error del servidor'); });
      };
      A.q('#btnConfirm')?.addEventListener('click', onConfirm, {once:true});
      A.q('#btnCancel')?.addEventListener('click', ()=>A.modals.hide('#mConfirm'), {once:true});
    });
  }

  function init(app){
    A = app;
    const term = A.getQS('q');
    const cat  = (A.getQS('cat') || 'all').toLowerCase();

    const input = A.q('#searchInput'); if(input){ input.value = term; }

    // activar visualmente el chip si existe
    let applied = false;
    A.qa('#secReservar .chip').forEach(ch=>{
      const k = (ch.dataset.filter || '').toLowerCase();
      if(k && k === cat){ ch.classList.add('active'); applied = true; }
      else ch.classList.remove('active');
    });
    if(!applied){
      const all = A.q('#secReservar .chip[data-filter="all"]'); all && all.classList.add('active');
    }

    buildCards(term, applied ? cat : cat); // si cat='all' mostrará todo
    bindOnce();
  }

  return { init };
})();
