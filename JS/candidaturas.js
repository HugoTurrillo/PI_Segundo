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
        <div class="panel-card">
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

    activarBotones();
  }

  function activarBotones() {

    // ACEPTAR
    document.querySelectorAll(".btn-aceptar").forEach(btn => {
      btn.onclick = async () => {
        await fetch("../php/candidatura-aceptar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id_candidatura: btn.dataset.id })
        });

        await Swal.fire({
          icon: "success",
          title: "Candidatura aceptada",
          text: "La candidatura ha sido aceptada correctamente"
        });

        cargarCandidaturas();
      };
    });

    // RECHAZAR
    document.querySelectorAll(".btn-rechazar").forEach(btn => {
      btn.onclick = async () => {

        const { value: motivo } = await Swal.fire({
          title: "Rechazar candidatura",
          input: "textarea",
          inputLabel: "Motivo del rechazo",
          showCancelButton: true,
          confirmButtonText: "Rechazar",
          confirmButtonColor: "#FF3228",
          cancelButtonText: "Cancelar",
          cancelButtonColor: "#000000",
          inputValidator: value => {
            if (!value) return "Debes indicar un motivo";
          }
        });

        if (!motivo) return;

        await fetch("../php/candidatura-rechazar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            id: btn.dataset.id,
            motivo
          })
        });

        await Swal.fire({
          icon: "success",
          title: "Candidatura rechazada",
          text: "El participante podrá subsanar la candidatura"
        });

        cargarCandidaturas();
      };
    });
  }

  cargarCandidaturas();
});
