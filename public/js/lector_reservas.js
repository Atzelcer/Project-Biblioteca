// public/js/lector_reservas.js
window.LectorReservas = (function () {
  let A;
  let bound = false;

  // ===== Catálogo de filtros (13) =====
  const FILTERS = [
    { key: 'all',           name: 'Todos',         color: '#64748b' },
    { key: 'programacion',  name: 'Programación',  color: '#0ea5e9' },
    { key: 'matematicas',   name: 'Matemáticas',   color: '#06b6d4' },
    { key: 'fisica',        name: 'Física',        color: '#22c55e' },
    { key: 'historia',      name: 'Historia',      color: '#ef4444' },
    { key: 'idiomas',       name: 'Idiomas',       color: '#7dd3fc' },
    { key: 'arquitectura',  name: 'Arquitectura',  color: '#a78bfa' },
    { key: 'quimica',       name: 'Química',       color: '#34d399' },
    { key: 'literatura',    name: 'Literatura',    color: '#f43f5e' },
    { key: 'ingenieria',    name: 'Ingeniería',    color: '#22d3ee' },
    { key: 'electronica',   name: 'Electrónica',   color: '#60a5fa' },
    { key: 'biologia',      name: 'Biología',      color: '#86efac' },
    { key: 'arte',          name: 'Arte',          color: '#f59e0b' },
    { key: 'otros',         name: 'Otros',         color: '#94a3b8' }
  ];

  function deriveCategoryFromText(t) {
    const text = (t || '').toLowerCase();
    const tests = [
      ['programacion', ['program', 'code', 'python', 'java', 'javascript', 'algoritmo', 'algorithms', 'pattern', 'refactor', 'arquitectura', 'architecture', 'clean', 'pragmatic']],
      ['matematicas',  ['álgebra','algebra','cálculo','calculus','lineal','matem','topología','ecuaciones','probabilidad','estadística','analysis','analisis']],
      ['fisica',       ['física','fisica','electromagnetismo','mecánica','mecanica','óptica','optica','termodinámica','termodinamica','moderna']],
      ['historia',     ['historia','universal','américa','latina','roma','grecia','contemporánea','contemporanea']],
      ['idiomas',      ['english','inglés','ingles','grammar','business english','idioma','ielts','cambridge']],
      ['arquitectura', ['arquitectura','architecture']],
      ['quimica',      ['química','quimica','orgánica','organica','inorgánica','inorganica','fisicoquimica','general','analitica','analítica']],
      ['literatura',   ['quijote','soledad','crónica','cronica','literatura','novela','cuento','poesía','poesia','rayuela']],
      ['ingenieria',   ['ingenier','engineer','industrial','civil','materiales','mecánica de fluidos','mecanica de materiales']],
      ['electronica',  ['electrón','electron','digital','circuit','microelectron','signals','sistemas','rf','rfic']],
      ['biologia',     ['biología','biologia','genética','genetica','microbiología','microbiologia','molecular']],
      ['arte',         ['arte','art','pintura','escultura','historia del arte']]
    ];
    for (const [key, arr] of tests) {
      if (arr.some(w => text.includes(w))) return key;
    }
    return 'otros';
  }

  function normalizeRow(tr) {
    const titulo = (tr.dataset.nombre || tr.children[0]?.textContent || '').trim();
    const autor  = (tr.dataset.autor  || tr.children[1]?.textContent || '').trim();
    const foto   = (tr.dataset.foto   || 'default-book.png').trim();
    let cat = (tr.dataset.categoria || '').toLowerCase().trim();
    if (!cat) cat = deriveCategoryFromText(titulo + ' ' + autor);
    return {
      id: +(tr.dataset.id || 0),
      titulo, autor, foto, categoria: cat
    };
  }

  function buildCards(filterText = '', filterCat = 'all') {
    const grid = A.q('#cardsReservar'); if (!grid) return;
    const stats = A.q('#galleryStats'); const empty = A.q('#noResults');
    grid.innerHTML = '';

    const rows = A.qa('#tablaDisponibles tbody tr');
    const total = rows.length;
    const term = (filterText || '').toLowerCase();
    const wanted = (filterCat || 'all').toLowerCase();

    let matches = 0;
    rows.forEach(tr => {
      const r = normalizeRow(tr);
      const okText = !term || r.titulo.toLowerCase().includes(term) || r.autor.toLowerCase().includes(term);
      const okCat  = (wanted === 'all') || (r.categoria && r.categoria.includes(wanted));
      if (!(okText && okCat)) return;

      matches++;
      const cover = A.base + '/public/images/books/' + (r.foto || 'default-book.png');
      const el = document.createElement('article');
      el.className = 'card cardBook';
      el.innerHTML =
        '<div class="cover" data-preview="'+r.foto+'" data-title="'+r.titulo.replace(/"/g,'&quot;')+'" data-autor="'+r.autor.replace(/"/g,'&quot;')+'">'+
          '<img src="'+cover+'" alt="" onerror="this.src=\''+A.base+'/public/images/books/default-book.png\'">'+
        '</div>'+
        '<div class="meta">'+
          '<h4 class="bookTitle">'+r.titulo+'</h4>'+
          '<p class="bookAuthor">'+r.autor+'</p>'+
          '<button class="btnMini reservar" '+
            'data-id="'+r.id+'" data-nombre="'+r.titulo.replace(/"/g,'&quot;')+'" '+
            'data-autor="'+r.autor.replace(/"/g,'&quot;')+'" '+
            'data-foto="'+r.foto.replace(/"/g,'&quot;')+'">Reservar</button>'+
        '</div>';
      grid.appendChild(el);
    });

    if (stats) stats.textContent = matches + ' de ' + total + ' libros';
    if (empty) empty.style.display = matches ? 'none' : '';
  }

  function renderTopFilters(initialCat) {
    const host = A.q('#filtersBar');
    const wrap = A.q('#filtersWrap');
    if (!host) return;

    host.innerHTML = '';
    FILTERS.forEach(f => {
      const btn = document.createElement('button');
      btn.className = 'chip';
      btn.dataset.filter = f.key;
      btn.title = f.name;
      btn.innerHTML = `<i style="background:${f.color}"></i>${f.name}`;
      btn.addEventListener('click', () => {
        A.qa('#filtersBar .chip').forEach(x => x.classList.remove('active'));
        btn.classList.add('active');
        buildCards(A.q('#searchInput')?.value || '', f.key);
        A.setQS && A.setQS('cat', f.key === 'all' ? '' : f.key);
      });
      host.appendChild(btn);
    });

    const key = initialCat || 'all';
    const toActivate = host.querySelector(`.chip[data-filter="${key}"]`) || host.querySelector('.chip[data-filter="all"]');
    toActivate?.classList.add('active');

    if (wrap) wrap.style.display = 'block';
  }

  function bindOnce() {
    if (bound) return;
    bound = true;

    document.addEventListener('click', e => {
      const el = e.target.closest('.cover'); if (!el || !A.q('#mPreview')) return;
      if (!A.q('#secReservar')) return;
      const pvImg   = A.q('#pvImg');
      const pvTitle = A.q('#pvTitle');
      const pvAutor = A.q('#pvAutor');
      const foto    = el.dataset.preview || '';
      const title   = el.dataset.title || 'Portada';
      const autor   = el.dataset.autor || '';
      if (pvImg) { pvImg.src = A.base + '/public/images/books/' + (foto || 'default-book.png'); pvImg.onerror = () => { pvImg.src = A.base + '/public/images/books/default-book.png'; }; }
      if (pvTitle) pvTitle.textContent = title;
      if (pvAutor) pvAutor.textContent = autor ? ('Autor: ' + autor) : '';
      A.modals.show('#mPreview');
    });
    A.q('#pvClose')?.addEventListener('click', () => A.modals.hide('#mPreview'));

    document.addEventListener('click', e => {
      const btn = e.target.closest('button.reservar'); if (!btn) return;
      if (!A.q('#secReservar')) return;

      const sel = {
        id: +(btn.dataset.id || 0),
        nombre: btn.dataset.nombre || '',
        autor:  btn.dataset.autor  || '',
        foto:   btn.dataset.foto   || 'default-book.png'
      };
      const img = A.q('#imgLibro');
      if (img) {
        img.src = A.base + '/public/images/books/' + (sel.foto || 'default-book.png');
        img.onerror = () => { img.src = A.base + '/public/images/books/default-book.png'; };
      }
      A.q('#nombreLibro') && (A.q('#nombreLibro').textContent = sel.nombre);
      A.q('#autorLibro')  && (A.q('#autorLibro').textContent  = sel.autor);
      A.modals.show('#mConfirm');

      const onConfirm = () => {
        A.q('#btnConfirm')?.removeEventListener('click', onConfirm);
        if (!sel.id) { A.modals.hide('#mConfirm'); alert('Libro inválido'); return; }
        const fd = new URLSearchParams(); fd.append('id_libro', sel.id);
        fetch(A.base + '/src/entradas/crear_reserva.php', {
          method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: fd
        }).then(r => r.json()).then(j => {
          A.modals.hide('#mConfirm');
          if (!j || !j.ok) {
            if (j && j.code === 'YA_ACTIVA') { A.q('#avisoMsg').textContent = j.msg || 'Tienes una reserva pendiente'; A.modals.show('#mAviso'); return; }
            alert((j && j.msg) ? j.msg : 'No se pudo reservar'); return;
          }
          A.latest = A.normalize({ id_reserva: j.id_reserva, fichaReserva: j.ficha, estado: j.estado || 'pendiente', libro: sel.nombre, autor: sel.autor, fechaDevolucion: j.fechaDevolucion || null });
          A.fetchLatest(); A.fillTicket(A.latest); A.modals.show('#mTicket');
        }).catch(() => { A.modals.hide('#mConfirm'); alert('Error del servidor'); });
      };
      A.q('#btnConfirm')?.addEventListener('click', onConfirm, { once: true });
      A.q('#btnCancel')?.addEventListener('click', () => A.modals.hide('#mConfirm'), { once: true });
    });
  }

  function applyFilter(catKey) {
    buildCards(A.q('#searchInput')?.value || '', catKey || 'all');
    const btn = A.q(`#filtersBar .chip[data-filter="${catKey}"]`);
    if (btn) {
      A.qa('#filtersBar .chip').forEach(x => x.classList.remove('active'));
      btn.classList.add('active');
    }
  }

  function init(app) {
    A = app;
    const term = A.getQS('q');
    const cat  = (A.getQS('cat') || 'all').toLowerCase();

    // ← importante: esta clase activa el layout centrado de topbar
    const topbar = A.q('#topbar');
    topbar?.classList.remove('topbar--home');
    topbar?.classList.add('topbar--reservar');

    // Mostrar búsqueda y filtros
    const sw = A.q('#searchWrap');  if (sw) sw.style.display  = 'block';
    const fw = A.q('#filtersWrap'); if (fw) fw.style.display = 'block';

    // Búsqueda inicial
    const input = A.q('#searchInput'); if (input) input.value = term || '';

    // Pintar barra de filtros y tarjetas
    renderTopFilters(cat);
    buildCards(term || '', cat);

    bindOnce();
  }

  return { init, applyFilter };
})();
