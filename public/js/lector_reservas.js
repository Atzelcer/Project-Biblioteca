window.LectorReservas = (function(){
  let A;               // referencia a AppLector
  let bound = false;   // evita doble binding

  function normalizeRow(tr){
    return {
      id: +(tr.dataset.id||0),
      titulo: (tr.dataset.nombre || tr.children[0]?.textContent || '').trim(),
      autor:  (tr.dataset.autor  || tr.children[1]?.textContent || '').trim(),
      foto:   (tr.dataset.foto   || 'default-book.png').trim(),
      categoria: (tr.dataset.categoria || '').toLowerCase()
    };
  }

  function buildCards(filterText='', filterCat='all'){
    const grid=A.q('#cardsReservar'); if(!grid) return;
    const stats=A.q('#galleryStats'); const empty=A.q('#noResults');
    grid.innerHTML='';

    const rows=A.qa('#tablaDisponibles tbody tr');
    const total=rows.length;
    const term = (filterText||'').toLowerCase();

    let matches=0;
    rows.forEach(tr=>{
      const r = normalizeRow(tr);
      const okText = !term || r.titulo.toLowerCase().includes(term) || r.autor.toLowerCase().includes(term);
      const okCat  = (filterCat==='all') || (r.categoria && r.categoria.includes(filterCat));
      if(!(okText && okCat)) return;

      matches++;
      const card=document.createElement('article');
      card.className='card cardBook';
      card.innerHTML =
        '<div class="cover" data-preview="'+r.foto+'" data-title="'+r.titulo.replace(/"/g,'&quot;')+'" data-autor="'+r.autor.replace(/"/g,'&quot;')+'">'+
          '<img src="'+A.base+'/public/images/books/'+r.foto+'" alt=""></div>'+
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
    if(bound) return;
    bound = true;

    // filtros por chip
    A.qa('#secReservar .chip').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        A.qa('#secReservar .chip').forEach(b=> b.classList.remove('active'));
        btn.classList.add('active');
        buildCards(A.q('#searchInput')?.value||'', (btn.dataset.filter||'all'));
      });
    });

    // preview portada (delegado)
    document.addEventListener('click', e=>{
      const el = e.target.closest('.cover'); if(!el || !A.q('#mPreview')) return;
      if(!A.q('#secReservar')) return; // solo cuando estás en la vista
      const foto  = el.dataset.preview || '';
      const title = el.dataset.title   || 'Portada';
      const autor = el.dataset.autor   || '';
      const pvImg = A.q('#pvImg'), pvTitle=A.q('#pvTitle'), pvAutor=A.q('#pvAutor');
      if(pvImg){ pvImg.src = A.base+'/public/images/books/'+foto; }
      if(pvTitle){ pvTitle.textContent = title; }
      if(pvAutor){ pvAutor.textContent = autor ? ('Autor: '+autor) : ''; }
      A.modals.show('#mPreview');
    });
    A.q('#pvClose')?.addEventListener('click', ()=>A.modals.hide('#mPreview'));

    // flujo de reservar (delegado)
    document.addEventListener('click', e=>{
      const btn = e.target.closest('button.reservar'); if(!btn) return;
      if(!A.q('#secReservar')) return;

      const sel = {
        id:+(btn.dataset.id||0),
        nombre:btn.dataset.nombre||'',
        autor: btn.dataset.autor||'',
        foto:  btn.dataset.foto||'default-book.png'
      };
      const img=A.q('#imgLibro'); if(img){ img.src=A.base+'/public/images/books/'+sel.foto; }
      A.q('#nombreLibro')&&(A.q('#nombreLibro').textContent=sel.nombre);
      A.q('#autorLibro')&&(A.q('#autorLibro').textContent=sel.autor);
      A.modals.show('#mConfirm');

      // confirmar (una sola vez por click)
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
    // set placeholder del input con el término actual
    const term = A.getQS('q');
    const input = A.q('#searchInput'); if(input){ input.value = term; }
    buildCards(term,'all');
    bindOnce();
  }

  return { init };
})();
