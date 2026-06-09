/**
 * Cargo las candidaturas del organizador, muestro popup de detalle al clic y gestiono aceptar/rechazar; escapo datos con escapeHtml.
 */

document.addEventListener("DOMContentLoaded", () => {

  const contenedor = document.getElementById("candidaturas-container");
  if (!contenedor) return;

  async function cargarCandidaturas() {
    const categoria = document.getElementById("filtroCategoria")?.value || "todas";

    const res = await fetch(`../php/candidaturas-listar.php?categoria=${categoria}`);
    const lista = await res.json();

    contenedor.innerHTML = "";

    const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
    lista.forEach(c => {

      let bloqueNominacion = "";

      if (c.estado === "aceptada" && !c.id_categoria) {
        bloqueNominacion = `
          <a href="nominar-categoria.html?id_candidatura=${c.id_candidatura}"
             class="btn login-btn" style="background:#000;">
            Nominar a categoría
          </a>
        `;
      }

      contenedor.innerHTML += `
        <div class="panel-card candidatura-card"
             data-id="${c.id_candidatura}"
             style="cursor:pointer;">

          <h3>${esc(c.titulo_obra)}</h3>
          <p><strong>Autor:</strong> ${esc(c.nombre_contacto)}</p>
          <p><strong>Email:</strong> ${esc(c.email_contacto)}</p>
          <p><strong>Perfil:</strong> ${esc(c.rol_participante)}</p>
          <span class="estado-badge estado-${c.estado}">
          ${
            c.estado === "en_proceso" ? "En proceso"
            : c.estado === "aceptada" ? "Aceptada"
            : c.estado === "rechazada" ? "Rechazada"
            : c.estado
          }
        </span>

          


          ${c.estado === "rechazada" ? `
            <p style="color:red;">
              <strong>Motivo rechazo:</strong><br>
              ${esc(c.motivo_rechazo)}
            </p>
          ` : ""}

          ${c.mensaje_subsanacion ? `
            <p style="color:green;">
              <strong>Subsanación:</strong><br>
              ${esc(c.mensaje_subsanacion)}
            </p>
          ` : ""}

          <div style="margin-top:1rem; display:flex; gap:1rem; flex-wrap:wrap;">
            ${c.estado === "en_proceso" ? `
              <button class="btn login-btn btn-aceptar"
                      data-id="${c.id_candidatura}">
                Aceptar
              </button>

              <button class="btn login-btn btn-rechazar"
                      data-id="${c.id_candidatura}"
                      style="background:#444;">
                Rechazar
              </button>
            ` : ""}

            ${bloqueNominacion}
          </div>
        </div>
      `;
    });

    activarClicks();
    activarBotones();
  }

  const filtro = document.getElementById("filtroCategoria");
  if (filtro) {
    filtro.addEventListener("change", cargarCandidaturas);
  }

  function activarClicks() {
    document.querySelectorAll(".candidatura-card").forEach(card => {
      card.addEventListener("click", async (e) => {
        if (e.target.tagName === "BUTTON" || e.target.tagName === "A") return;

        const id = card.dataset.id;

        const res = await fetch(`../php/candidatura-detalle.php?id=${id}`);
        const data = await res.json();

        if (!data.ok) {
          Swal.fire("Error", data.msg, "error");
          return;
        }

        const c = data.candidatura;
        const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));

        Swal.fire({
          title: esc(c.titulo_obra),
          width: "900px",
          html: `
            <p><strong>Autor:</strong> ${esc(c.nombre_contacto)}</p>
            <p><strong>Email:</strong> ${esc(c.email_contacto)}</p>
            <p><strong>Perfil:</strong> ${esc(c.rol_participante)}</p>

           <video controls poster="../${esc(c.portada_ruta)}">
  <source src="../${esc(c.video_ruta)}" type="video/mp4">
</video>




            <p><strong>Sinopsis:</strong></p>
            <p>${esc(c.sinopsis)}</p>
          `,
          confirmButtonText: "Cerrar"
        });
      });
    });
  }

  function activarBotones() {
    document.querySelectorAll(".btn-aceptar").forEach(btn => {
      btn.onclick = async (e) => {
        e.stopPropagation();
        await fetch("../php/candidatura-aceptar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id_candidatura: btn.dataset.id })
        });
        Swal.fire("Aceptada", "Candidatura aceptada", "success");
        cargarCandidaturas();
      };
    });

    document.querySelectorAll(".btn-rechazar").forEach(btn => {
      btn.onclick = async (e) => {
        e.stopPropagation();

        const { value: motivo } = await Swal.fire({
          title: "Rechazar candidatura",
          input: "textarea",
          showCancelButton: true,
          confirmButtonColor: "#FF3228",
          inputValidator: v => !v && "Debes indicar un motivo"
        });

        if (!motivo) return;

        await fetch("../php/candidatura-rechazar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: btn.dataset.id, motivo })
        });

        Swal.fire("Rechazada", "Candidatura rechazada", "success");
        cargarCandidaturas();
      };
    });
  }

  cargarCandidaturas();
});