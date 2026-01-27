document.addEventListener("DOMContentLoaded", () => {

  const contenedor = document.getElementById("candidaturas-container");
  if (!contenedor) return;

  async function cargarCandidaturas() {
    const res = await fetch("../php/candidaturas-listar.php");
    const lista = await res.json();

    contenedor.innerHTML = "";

    lista.forEach(c => {

      let bloqueNominacion = "";

      // CASO 1: aceptada y NO nominada
      if (c.estado === "aceptada" && !c.id_categoria) {
        bloqueNominacion = `
          <a href="nominar-categoria.html?id_candidatura=${c.id_candidatura}"
             class="btn login-btn"
             style="background:#000000;">
             Nominar a categoría
          </a>
        `;
      }

      // CASO 2: aceptada y YA nominada
      if (c.estado === "aceptada" && c.id_categoria) {
        bloqueNominacion = `
          <p><strong>Categoría nominada:</strong> ${c.categoria_nombre}</p>

          <a href="nominar-categoria.html?id_candidatura=${c.id_candidatura}"
             class="btn login-btn"
             style="background:#FF3228;">
             Editar nominación
          </a>
        `;
      }

      contenedor.innerHTML += `
        <div class="panel-card">
          <h3>${c.titulo_obra}</h3>

          <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
          <p><strong>Email:</strong> ${c.email_contacto}</p>
          <p><strong>Estado:</strong> ${c.estado}</p>

          ${c.estado === "rechazada" ? `
            <p style="color:red;">
              <strong>Motivo rechazo:</strong> ${c.motivo_rechazo || "No indicado"}
            </p>
          ` : ""}

          <div style="margin-top:1rem; display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">

            <!-- NUEVO BOTÓN -->
            <button class="btn login-btn btn-ver"
                    data-id="${c.id_candidatura}"
                    style="background:#1a73e8;">
              Ver candidatura
            </button>

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

    activarBotones();
  }

  function activarBotones() {

    // BOTÓN VER CANDIDATURA
    document.querySelectorAll(".btn-ver").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        window.location.href = `candidatura_detalle.html?id=${id}`;
      });
    });

    document.querySelectorAll(".btn-aceptar").forEach(btn => {
      btn.addEventListener("click", async () => {
        await fetch("../php/candidatura-aceptar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id_candidatura: btn.dataset.id })
        });
        cargarCandidaturas();
      });
    });

    document.querySelectorAll(".btn-rechazar").forEach(btn => {
      btn.addEventListener("click", async () => {
        const motivo = prompt("Indica el motivo del rechazo:");
        if (!motivo) return;

        await fetch("../php/candidatura-rechazar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            id: btn.dataset.id,
            motivo
          })
        });
        cargarCandidaturas();
      });
    });
  }

  cargarCandidaturas();
});