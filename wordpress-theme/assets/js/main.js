/**
 * Comportamiento del sitio, sin frameworks.
 *
 * Reemplaza los tres useState/useEffect que tenía la versión React:
 *   1. estado de scroll del header
 *   2. menú mobile
 *   3. contadores animados de la banda de estadísticas
 * más el submit del formulario de contacto, que abre WhatsApp.
 *
 * Todo lo demás (hover, transiciones, el hero que sube, el anillo del botón de
 * WhatsApp, el scroll suave) es CSS puro y no necesita JavaScript.
 */
(function () {
  "use strict";

  /* ---------------------------------------------------------------------
   * 1. Header: fondo sólido a partir de 24px de scroll.
   *    Equivale al useState `scrolled` del Header.tsx original.
   * ------------------------------------------------------------------ */
  function initHeader() {
    var header = document.getElementById("site-header");
    if (!header) return;

    function onScroll() {
      header.classList.toggle("is-scrolled", window.scrollY > 24);
    }

    onScroll(); // el original corría el handler también en el primer render
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ---------------------------------------------------------------------
   * 2. Menú mobile.
   * ------------------------------------------------------------------ */
  function initMobileMenu() {
    var toggle = document.getElementById("iff-menu-toggle");
    var menu = document.getElementById("iff-mobile-menu");
    if (!toggle || !menu) return;

    var iconOpen = toggle.querySelector('[data-menu-icon="open"]');
    var iconClose = toggle.querySelector('[data-menu-icon="close"]');

    function setOpen(open) {
      menu.classList.toggle("hidden", !open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.setAttribute("aria-label", open ? "Cerrar menú" : "Abrir menú");
      if (iconOpen) iconOpen.hidden = open;
      if (iconClose) iconClose.hidden = !open;
    }

    toggle.addEventListener("click", function () {
      setOpen(menu.classList.contains("hidden"));
    });

    // Al tocar un link el menú se cierra, igual que el onClick del original.
    menu.querySelectorAll("[data-mobile-link]").forEach(function (link) {
      link.addEventListener("click", function () {
        setOpen(false);
      });
    });
  }

  /* ---------------------------------------------------------------------
   * 3. Contadores animados.
   *    IntersectionObserver con threshold 0.3 y animación de 1600ms con
   *    easing cúbico de salida: exactamente los valores del original.
   * ------------------------------------------------------------------ */
  function initCounters() {
    var wrapper = document.getElementById("iff-stats");
    if (!wrapper) return;

    var counters = Array.prototype.slice.call(
      wrapper.querySelectorAll("[data-counter]")
    );
    if (!counters.length) return;

    function format(value, suffix) {
      return value.toLocaleString("es-AR") + suffix;
    }

    // El HTML trae el valor final para que sin JS no quede en cero; con JS
    // arrancamos en cero, como el useState(0) del componente Counter.
    counters.forEach(function (el) {
      el.textContent = format(0, el.dataset.suffix || "");
    });

    var reduceMotion =
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function run() {
      counters.forEach(function (el) {
        var target = parseInt(el.dataset.count, 10) || 0;
        var suffix = el.dataset.suffix || "";

        if (reduceMotion) {
          el.textContent = format(target, suffix);
          return;
        }

        var start = performance.now();
        var dur = 1600;

        function tick(t) {
          var p = Math.min((t - start) / dur, 1);
          el.textContent = format(
            Math.round(target * (1 - Math.pow(1 - p, 3))),
            suffix
          );
          if (p < 1) requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
      });
    }

    if (!("IntersectionObserver" in window)) {
      run();
      return;
    }

    var io = new IntersectionObserver(
      function (entries) {
        if (entries[0] && entries[0].isIntersecting) {
          run();
          io.disconnect();
        }
      },
      { threshold: 0.3 }
    );

    io.observe(wrapper);
  }

  /* ---------------------------------------------------------------------
   * 4. Formulario de contacto -> WhatsApp.
   *    No se envía nada al servidor: se arma el mensaje y se abre wa.me,
   *    igual que el onSubmit del original.
   * ------------------------------------------------------------------ */
  function initContactForm() {
    var form = document.getElementById("iff-contact-form");
    if (!form) return;

    var sent = document.getElementById("iff-form-sent");
    var number = form.dataset.whatsappNumber;

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      var data = new FormData(form);

      function val(key, max) {
        return String(data.get(key) || "")
          .trim()
          .slice(0, max);
      }

      var nombre = val("nombre", 100);
      var email = val("email", 255);
      var telefono = val("telefono", 40);
      var servicio = val("servicio", 120);
      var mensaje = val("mensaje", 1000);

      var texto = [
        "Hola IFF, quisiera solicitar un presupuesto.",
        "",
        "Nombre: " + nombre,
        "Email: " + email,
        telefono ? "Teléfono: " + telefono : null,
        "Servicio: " + servicio,
        "",
        "Mensaje: " + mensaje,
      ]
        .filter(Boolean)
        .join("\n");

      window.open(
        "https://wa.me/" + number + "?text=" + encodeURIComponent(texto),
        "_blank",
        "noopener,noreferrer"
      );

      if (sent) sent.hidden = false;
    });
  }

  function init() {
    initHeader();
    initMobileMenu();
    initCounters();
    initContactForm();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
