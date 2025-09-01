// public/js/lector_home.js
window.LectorHome = (function () {
  let A;

  // Catálogo para pintar las tarjetas y navegar con filtro
  // (si falta la extensión en cover, se le añade .jpg)
  const CATS = [
    { key: "programacion", name: "Programación", tint: "#19c37d", cover: "Clean_Code.jpg" },
    { key: "matematicas",  name: "Matemáticas",  tint: "#4a9bff",  cover: "Analisis_Matematico.jpg" },
    { key: "fisica",       name: "Física",       tint: "#ffb74a",  cover: "Fisica_Universitaria.jpg" },
    { key: "historia",     name: "Historia",     tint: "#ff6b6b",  cover: "Historia_Universal.jpg" },
    { key: "idiomas",      name: "Idiomas",      tint: "#7dd3fc",  cover: "Business_English.jpg" },
    { key: "arquitectura", name: "Arquitectura", tint: "#a78bfa",  cover: "Clean_Architecture.jpg" },
    { key: "quimica",      name: "Química",      tint: "#34d399",  cover: "Quimica_General.jpg" },
    { key: "literatura",   name: "Literatura",   tint: "#f472b6",  cover: "Cien_Anos_de_Soledad.jpg" },
    { key: "ingenieria",   name: "Ingeniería",   tint: "#22d3ee",  cover: "Introduction_to_Algorithms.jpg" },
    { key: "electronica",  name: "Electrónica",  tint: "#60a5fa",  cover: "Electromagnetismo.jpg" },
    { key: "biologia",     name: "Biología",     tint: "#86efac",  cover: "Quimica_Organica.jpg" },
    { key: "arte",         name: "Arte",         tint: "#f59e0b",  cover: "Historia_del_Arte.jpg" }
  ];

  // Mapeo a los slugs canónicos que usa el panel Reservar
  // (?cat=prog|math|phys|hist|eng|arq|chem|lit|inge|elec|bio|arte)
  const SLUG = {
    programacion: "prog",
    matematicas:  "math",
    fisica:       "phys",
    historia:     "hist",
    idiomas:      "eng",
    arquitectura: "arq",
    quimica:      "chem",
    literatura:   "lit",
    ingenieria:   "inge",
    electronica:  "elec",
    biologia:     "bio",
    arte:         "arte"
  };

  function withExt(name) {
    if (!name) return "default-book.png";
    return /\.[a-z]{3,4}$/i.test(name) ? name : (name + ".jpg");
  }

  // Construye el grid de categorías clicables (Home → Reservar con filtro)
  function buildCategoryCards() {
    const wrap = A.q("#catsWrap");
    if (!wrap) return;
    wrap.innerHTML = "";

    CATS.forEach((cat) => {
      const cover = `${A.base}/public/images/books/${withExt(cat.cover)}`;
      const card = document.createElement("div");
      card.className = "catCard";
      card.style.setProperty("--img", `url('${cover}')`);
      card.style.setProperty("--tint", cat.tint);
      card.innerHTML = `<div class="tint"></div><div class="name">${cat.name}</div>`;

      // Navegación: fija ?cat=<slug> y abre la vista "reservar"
      card.addEventListener("click", (e) => {
        e.preventDefault();
        const slug = SLUG[cat.key] || "all";
        A.setQS("q", "");
        A.setQS("cat", slug === "all" ? "" : slug);
        document.querySelector('.nav [data-section="reservar"]')
          ?.dispatchEvent(new MouseEvent("click", { bubbles: true, cancelable: true }));
      });

      wrap.appendChild(card);
    });
  }

  // Tarjetas informativas/hero con portadas aleatorias (estético)
  function decorateInfoCards() {
    const picksRecordatorio = [
      "Analisis_Real.jpg",
      "Code_Complete.jpg",
      "Clean_Architecture.jpg",
      "The_Pragmatic_Programmer.jpg"
    ];
    const picksAccesos = [
      "Clean_Code.jpg",
      "Automate_the_Boring_Stuff.jpg",
      "Introduction_to_Algorithms.jpg",
      "Eloquent_JavaScript.jpg"
    ];
    const pick = (arr) => arr[(Math.random() * arr.length) | 0];

    const setCover = (sel, filename) => {
      const el = A.q(sel);
      if (!el) return;
      el.classList.add("img");
      el.style.setProperty("--img", `url('${A.base}/public/images/books/${withExt(filename)}')`);
    };

    setCover("#cardRecordatorio", pick(picksRecordatorio));
    setCover("#cardAccesos", pick(picksAccesos));
  }

  // Accesos rápidos del Home (si existen en tu HTML)
  function wireQuickActions() {
    // Botón “Buscar y reservar”
    A.q("#btnQuickReservar")?.addEventListener("click", (e) => {
      e.preventDefault();
      A.setQS("cat", "");
      document.querySelector('.nav [data-section="reservar"]')
        ?.dispatchEvent(new MouseEvent("click", { bubbles: true, cancelable: true }));
    });

    // Botón “Mi ticket”
    A.q("#btnQuickTicket")?.addEventListener("click", (e) => {
      e.preventDefault();
      if (A.latest) { A.fillTicket(A.latest); A.modals.show("#mTicket"); }
    });
  }

  // Ticket desde la tarjeta “Mi reserva” del Home
  function wireTicketCard() {
    const openTicket = () => { if (A.latest) { A.fillTicket(A.latest); A.modals.show("#mTicket"); } };
    A.q("#homeTicketCard")?.addEventListener("click", openTicket);
    A.q("#goTicket")?.addEventListener("click", openTicket);
  }

  // También hacemos clic-captura en cualquier elemento que ya exista con data-cat
  // para unificar comportamiento
  function enhanceExistingCategoryBlocks() {
    document.querySelectorAll(".catCard[data-cat], .homeCat[data-cat], .cat[data-cat]").forEach((el) => {
      el.style.cursor = "pointer";
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const raw = (el.dataset.cat || "").toLowerCase();
        const slug = SLUG[raw] || "all";
        A.setQS("q", "");
        A.setQS("cat", slug === "all" ? "" : slug);
        document.querySelector('.nav [data-section="reservar"]')
          ?.dispatchEvent(new MouseEvent("click", { bubbles: true, cancelable: true }));
      });
    });
  }

  function init(app) {
    A = app;

    // Ticket
    wireTicketCard();

    // Categorías y estética
    buildCategoryCards();
    enhanceExistingCategoryBlocks();
    decorateInfoCards();
    wireQuickActions();
  }

  return { init };
})();
