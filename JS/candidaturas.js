document.addEventListener("DOMContentLoaded", () => {

  const contenedor = document.getElementById("candidaturas-container");
  if (!contenedor) return;

  async function cargarCandidaturas() {
    const categoria = document.getElementById("filtroCategoria")?.value || "todas";

    const res = await fetch(`../php/candidaturas-listar.php?categoria=${categoria}`);
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

      contenedor.innerHTML += `
        <div class="panel-card candidatura-card"
             data-id="${c.id_candidatura}"
             style="cursor:pointer;">

          <h3>${c.titulo_obra}</h3>
          <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
          <p><strong>Email:</strong> ${c.email_contacto}</p>
          <p><strong>Perfil:</strong> ${c.rol_participante}</p>
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

        Swal.fire({
          title: c.titulo_obra,
          width: "900px",
          html: `
            <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
            <p><strong>Email:</strong> ${c.email_contacto}</p>
            <p><strong>Perfil:</strong> ${c.rol_participante}</p>

            <video controls poster="../php/${c.portada_ruta}"

                   style="width:100%;margin:1rem 0;">
             <source src="../php/${c.video_ruta}" type="video/mp4">

            </video>

            <p><strong>Sinopsis:</strong></p>
            <p>${c.sinopsis}</p>
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