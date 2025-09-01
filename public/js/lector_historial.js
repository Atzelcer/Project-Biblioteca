window.LectorHistorial = (function(){
  let A, bound=false;

  function normalizeItem(it){
    return {
      ficha: it.fichaReserva || it.ficha || it.codigo || '',
      titulo: it.libro || it.titulo || it.libro_nombre || '',
      autor: it.autor || it.libro_autor || '',
      estado: (it.estado || '').toLowerCase(),
      fechaReserva: it.fechaReserva || it.fecha || it.fecha_reserva || '',
      fechaDevolucion: it.fechaDevolucion || it.devolucion || it.fecha_devolucion || ''
    };
  }

  async function fetchHistorial(){
    // probamos varios endpoints comunes
    const urls = [
      A.base + '/src/entradas/api_mis_reservas.php?action=historial',
      A.base + '/src/entradas/api_mis_reservas.php?action=lista'
    ];
    for(const url of urls){
      try{
        const r = await fetch(url, {cache:'no-store'});
        if(r.ok){
          const j = await r.json();
          if(j && (j.ok || Array.isArray(j.data))){
            return (Array.isArray(j.data)? j.data : (j.data || [])).map(normalizeItem);
          }
        }
      }catch{}
    }
    return null;
  }

  function render(items){
    const box = A.q('#historialReciente'); if(!box) return;
    if(!items || !items.length){
      box.innerHTML = '<div class="muted" style="padding:12px">Sin registros para mostrar.</div>';
      return;
    }
    const frag = document.createDocumentFragment();
    items.forEach(it=>{
      const row = document.createElement('div');
      row.className = 'card';
      row.style.padding = '12px';
      row.innerHTML = `
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center">
          <div>
            <div><strong>${it.titulo}</strong> <span class="muted">— ${it.autor || ''}</span></div>
            <div class="muted tiny">Ficha: ${it.ficha || '—'}</div>
            <div class="muted tiny">Reserva: ${it.fechaReserva || '—'}${it.fechaDevolucion? ' • Devolución: '+it.fechaDevolucion : ''}</div>
          </div>
          <span class="badge">${(it.estado||'').toUpperCase()||'—'}</span>
        </div>`;
      frag.appendChild(row);
    });
    box.innerHTML = '';
    box.appendChild(frag);
  }

  function init(app){
    if(bound) return; bound = true;
    A = app;
    const cont = A.q('#historialReciente');
    if(cont) cont.innerHTML = '<div class="muted" style="padding:12px">Cargando historial…</div>';
    fetchHistorial().then(render).catch(()=>render(null));
  }

  return { init };
})();
