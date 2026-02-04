document.addEventListener("DOMContentLoaded", () => {

  const contenedor = document.getElementById("candidaturas-container");
  if (!contenedor) return;

  async function cargarCandidaturas() {
    const res = await fetch("../php/candidaturas-listar.php");
    const lista = await res.json();

    contenedor.innerHTML = "";

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

      if (c.estado === "aceptada" && c.id_categoria) {
        bloqueNominacion = `
          <p><strong>Categoría nominada:</strong> ${c.categoria_nombre}</p>
          <a href="nominar-categoria.html?id_candidatura=${c.id_candidatura}"
             class="btn login-btn" style="background:#FF3228;">
            Editar nominación
          </a>
        `;
      }

      contenedor.innerHTML += `
        <div class="panel-card candidatura-card"
             data-id="${c.id_candidatura}"
             style="cursor:pointer;">

          <h3>${c.titulo_obra}</h3>
          <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
          <p><strong>Email:</strong> ${c.email_contacto}</p>
          <p><strong>Estado:</strong> ${c.estado}</p>

          ${c.estado === "rechazada" ? `
            <p style="color:red;">
              <strong>Motivo rechazo:</strong><br>
              ${c.motivo_rechazo}
            </p>
          ` : ""}

          ${c.mensaje_subsanacion ? `
            <p style="color:green;">
              <strong>Subsanación:</strong><br>
              ${c.mensaje_subsanacion}
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

  /* ===============================
     CLICK → POPUP CON VIDEO + PORTADA
  =============================== */
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

        // 🔑 RUTA CORREGIDA (CLAVE)
        const videoRuta   = ".." + c.video_ruta;
        const portadaRuta = ".." + c.portada_ruta;

        Swal.fire({
          title: c.titulo_obra,
          width: "900px",
          html: `
            <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
            <p><strong>Email:</strong> ${c.email_contacto}</p>

            <video
              controls
              poster="${portadaRuta}"
              style="
                width:100%;
                border-radius:8px;
                margin:1rem 0;
                background:#000;
              ">
              <source src="${videoRuta}" type="video/mp4">
              Tu navegador no soporta vídeo
            </video>

            <p style="margin-top:1rem;"><strong>Sinopsis:</strong></p>
            <p>${c.sinopsis}</p>
          `,
          showCloseButton: true,
          confirmButtonText: "Cerrar"
        });

      });
    });
  }

  /* ===============================
     BOTONES ACEPTAR / RECHAZAR
  =============================== */
  function activarBotones() {

    document.querySelectorAll(".btn-aceptar").forEach(btn => {
      btn.onclick = async (e) => {
        e.stopPropagation();

        await fetch("../php/candidatura-aceptar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id_candidatura: btn.dataset.id })
        });

        Swal.fire("Aceptada", "Candidatura aceptada correctamente", "success");
        cargarCandidaturas();
      };
    });

    document.querySelectorAll(".btn-rechazar").forEach(btn => {
      btn.onclick = async (e) => {
        e.stopPropagation();

        const { value: motivo } = await Swal.fire({
          title: "Rechazar candidatura",
          input: "textarea",
          inputLabel: "Motivo del rechazo",
          showCancelButton: true,
          confirmButtonText: "Rechazar",
          confirmButtonColor: "#FF3228",
          cancelButtonText: "Cancelar",
          inputValidator: v => !v && "Debes indicar un motivo"
        });

        if (!motivo) return;

        await fetch("../php/candidatura-rechazar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: btn.dataset.id, motivo })
        });

        Swal.fire("Rechazada", "La candidatura ha sido rechazada", "success");
        cargarCandidaturas();
      };
    });
  }

  cargarCandidaturas();
});
