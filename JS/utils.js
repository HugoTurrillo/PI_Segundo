/**
 * Función que he añadido para escapar HTML y evitar XSS cuando inserto texto de usuario en innerHTML.
 * @param {string} s - Texto a escapar
 * @returns {string}
 */
window.escapeHtml = function (s) {
  if (s == null || s === undefined) return "";
  const t = String(s);
  return t
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
};
