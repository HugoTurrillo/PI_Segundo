document.addEventListener("DOMContentLoaded", () => {

  document.querySelectorAll(".js-premio").forEach(card => {
    card.addEventListener("click", e => {
      e.preventDefault();

      const categoria = card.dataset.categoria;
      mostrarPopupPremios(categoria);
    });
  });

});

/* ======================================================
   POPUP PREMIOS – DEFINICIÓN EDITORIAL
====================================================== */
function mostrarPopupPremios(categoria) {

  let html = "";

  if (categoria === "Alumnos") {
    html = `
      <p>Premios oficiales para estudiantes universitarios.</p>

      <ul style="margin-top:1rem;">
        <li><strong>🥇 1º Premio:</strong> 1.000 €</li>
        <li><strong>🥈 2º Premio:</strong> 600 €</li>
        <li><strong>🥉 3º Premio:</strong> 300 €</li>
      </ul>

      <p style="margin-top:1rem;">
        <strong>Premio físico:</strong><br>
        Estatuilla oficial del Festival de Cortometrajes de la Universidad Europea.
      </p>
    `;
  }

  if (categoria === "Alumni") {
    html = `
      <p>Premios destinados a antiguos alumnos con trayectoria creativa.</p>

      <ul style="margin-top:1rem;">
        <li><strong>🥇 1º Premio:</strong> 1.500 €</li>
        <li><strong>🥈 2º Premio:</strong> 900 €</li>
        <li><strong>🥉 3º Premio:</strong> 500 €</li>
      </ul>
    `;
  }

  Swal.fire({
    title: categoria,
    html: html,
    width: "600px",
    confirmButtonText: "Cerrar"
  });
}
