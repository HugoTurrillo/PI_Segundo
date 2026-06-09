/**
 * Header dinámico + menú hamburguesa en móvil.
 */
(function () {
  function initNavbarMenu(root) {
    var scope = root || document;
    var navbar = scope.querySelector(".navbar");
    if (!navbar || navbar.dataset.navReady === "1") return;

    var toggle = navbar.querySelector(".nav-toggle");
    var menu = navbar.querySelector(".nav-menu");
    var backdrop = navbar.querySelector(".nav-backdrop");
    if (!toggle || !menu) return;

    navbar.dataset.navReady = "1";

    function closeMenu() {
      navbar.classList.remove("nav-open");
      toggle.setAttribute("aria-expanded", "false");
      toggle.setAttribute("aria-label", "Abrir menú");
      document.body.classList.remove("nav-menu-open");
    }

    function openMenu() {
      navbar.classList.add("nav-open");
      toggle.setAttribute("aria-expanded", "true");
      toggle.setAttribute("aria-label", "Cerrar menú");
      document.body.classList.add("nav-menu-open");
    }

    toggle.addEventListener("click", function () {
      if (navbar.classList.contains("nav-open")) closeMenu();
      else openMenu();
    });

    if (backdrop) {
      backdrop.addEventListener("click", closeMenu);
    }

    menu.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", closeMenu);
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeMenu();
    });

    window.addEventListener("resize", function () {
      if (window.matchMedia("(min-width: 901px)").matches) closeMenu();
    });
  }

  function loadDynamicHeader() {
    var container = document.getElementById("navbar-container");
    if (!container) {
      initNavbarMenu(document);
      return;
    }

    if (container.querySelector(".navbar")) {
      initNavbarMenu(container);
      return;
    }

    var pagina = window.location.pathname.split("/").pop() || "";
    var url = "../php/header-html.php?pagina=" + encodeURIComponent(pagina);

    fetch(url, { credentials: "include" })
      .then(function (r) {
        if (!r.ok) throw new Error("header");
        return r.text();
      })
      .then(function (html) {
        if (html) {
          container.innerHTML = html;
          initNavbarMenu(container);
        }
      })
      .catch(function () {});
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", loadDynamicHeader);
  } else {
    loadDynamicHeader();
  }
})();
