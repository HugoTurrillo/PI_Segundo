/**
 * Compruebo al cargar la página si el usuario está logueado y si su rol coincide con data-rol.
 * Si no está logueado o el rol no coincide, redirijo al home.
 */
(async function () {

    try {
      const res = await fetch("../php/auth-status.php", {
        credentials: "include"
      });

      const data = await res.json();

      if (!data.auth) {
        window.location.href = "../php/home.php";
        return;
      }

      const rolRequerido = document.body.dataset.rol;

      if (rolRequerido && data.rol !== rolRequerido) {
        window.location.href = "../php/home.php";
        return;
      }

    } catch (e) {
      console.error("Error comprobando autenticación", e);
      window.location.href = "../php/home.php";
    }

  })();

/** Uso la misma función de escape que en utils.js en las páginas con auth. */
window.escapeHtml = window.escapeHtml || function (s) {
  if (s == null || s === undefined) return "";
  const t = String(s);
  return t.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
};

/** Si una petición fetch devuelve 401, redirijo a login (sesión expirada). */
(function () {
  const origFetch = window.fetch;
  window.fetch = function () {
    return origFetch.apply(this, arguments).then(function (res) {
      if (res.status === 401) {
        window.location.href = "../HTML/login.html";
      }
      return res;
    });
  };
})();
  