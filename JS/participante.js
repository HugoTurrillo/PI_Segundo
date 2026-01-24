document.addEventListener("DOMContentLoaded", async () => {

  const contenedor = document.getElementById("bloque-candidatura");
  if (!contenedor) return;

  try {
    const res = await fetch("../php/candidatura-mi-estado.php");
    const data = await res.json();

    if (!data.ok) {
      contenedor.innerHTML = "<p>Error cargando la candidatura.</p>";
      return;
    }

    /* ======================================================
       NO HAY CANDIDATURA → MOSTRAR BOTÓN
    ====================================================== */
    if (!data.candidatura) {
      contenedor.innerHTML = `
        <div class="panel-card">
          <p>No has presentado ninguna candidatura todavía.</p>

          <a href="candidatura-nueva.html"
             class="btn login-btn"
             style="margin-top:1rem; display:inline-block;">
            Presentar candidatura
          </a>
        </div>
      `;
      return;
    }

    /* ======================================================
       HAY CANDIDATURA → MOSTRAR ESTADO
    ====================================================== */
    const c = data.candidatura;

    let html = `
      <div class="panel-card">
        <h3>${c.titulo_obra}</h3>
        <p><strong>Edición:</strong> ${c.edicion}</p>
        <p><strong>Estado:</strong> ${c.estado}</p>
        <p><strong>Sinopsis:</strong> ${c.sinopsis || "—"}</p>
    `;

    /* ======================================================
       RECHAZADA → SUBSANAR
    ====================================================== */
    if (c.estado === "rechazada") {
      html += `
        <hr>
        <p style="color:red;"><strong>Motivo del rechazo:</strong></p>
        <p>${c.motivo_rechazo || "No indicado"}</p>

        <label style="margin-top:1rem;"><strong>Subsanar candidatura</strong></label>
        <textarea id="mensaje-subsanacion" rows="4"
          placeholder="Explica qué has corregido..."></textarea>

        <button id="btn-subsanar" class="btn login-btn" style="margin-top:1rem;">
          Enviar subsanación
        </button>

        <p id="error-subsanar" style="color:red; margin-top:1rem;"></p>
      `;
    }

    html += `</div>`;
    contenedor.innerHTML = html;

    /* ======================================================
       BOTÓN SUBSANAR
    ====================================================== */
    const btn = document.getElementById("btn-subsanar");
    if (btn) {
      btn.addEventListener("click", async () => {
        const mensaje = document.getElementById("mensaje-subsanacion").value.trim();
        const error = document.getElementById("error-subsanar");

        error.textContent = "";

        if (mensaje === "") {
          error.textContent = "Debes escribir un mensaje de subsanación.";
          return;
        }

        const res = await fetch("../php/candidatura-subsanar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ mensaje })
        });

        const r = await res.json();

        if (r.ok) {
          await Swal.fire("Enviado", r.msg, "success");
          location.reload();
        } else {
          error.textContent = r.msg;
        }
      });
    }

  } catch (err) {
    console.error(err);
    contenedor.innerHTML = "<p>Error inesperado.</p>";
  }
});
