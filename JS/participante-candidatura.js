document.addEventListener("DOMContentLoaded", async () => {

  const sinCandidatura = document.getElementById("sinCandidatura");
  const conCandidatura = document.getElementById("conCandidatura");

  try {
    const res = await fetch("../php/candidatura-mi-estado.php");
    const data = await res.json();

    if (!data.ok || !data.candidaturas || data.candidaturas.length === 0) {
      sinCandidatura.style.display = "block";
      return;
    }

    conCandidatura.style.display = "block";
    conCandidatura.innerHTML = "";

    data.candidaturas.forEach(c => {

      const card = document.createElement("div");
      card.classList.add("panel-card");

      card.innerHTML = `
        <h3>${c.titulo_obra}</h3>

        <span class="estado-badge estado-${c.estado}">
        ${c.estado === "en_proceso" ? "En proceso"
        : c.estado === "aceptada" ? "Aceptada"
        : c.estado === "rechazada" ? "Rechazada"
        : c.estado}
        </span>



        ${c.video_ruta ? `
  <video
    class="candidatura-video"
    controls
    poster="../php/${c.portada_ruta}">
    <source src="../php/${c.video_ruta}" type="video/mp4">
  </video>
` : ""}


        <h4>Sinopsis</h4>
        <p class="candidatura-sinopsis">${c.sinopsis}</p>

        ${c.estado === "rechazada" ? `
  <div style="margin-top:1rem;">
    <p style="color:red;"><strong>Motivo rechazo</strong></p>
    <p>${c.motivo_rechazo ?? ""}</p>

    <h4>Editar candidatura y subsanar</h4>

    <label>Nuevo título (opcional)</label>
    <input type="text" class="tituloEditado" value="${c.titulo_obra}">

    <label>Nueva sinopsis (opcional)</label>
    <textarea class="sinopsisEditada">${c.sinopsis}</textarea>

    <label>Portada actual</label>
    <img src="${c.portada_ruta}" style="width:100%;border-radius:8px;margin-bottom:1rem;">

    <label>Nueva portada (opcional)</label>
    <input type="file" class="portadaEditada" accept="image/*">

    <label>Mensaje de subsanación *</label>
    <textarea class="mensajeSubsanacion" required></textarea>

    <button class="btn login-btn btnSubsanar" data-id="${c.id_candidatura}">
      Enviar subsanación
    </button>
  </div>
` : ""}
      `;

      conCandidatura.appendChild(card);
    });

    // Activar botones de subsanar
    document.querySelectorAll(".btnSubsanar").forEach(btn => {
      btn.addEventListener("click", async () => {

        const card = btn.closest(".panel-card");

        const formData = new FormData();
        formData.append("id_candidatura", btn.dataset.id);
        formData.append("tituloEditado", card.querySelector(".tituloEditado").value);
        formData.append("sinopsisEditada", card.querySelector(".sinopsisEditada").value);
        formData.append("mensajeSubsanacion", card.querySelector(".mensajeSubsanacion").value);

        const portada = card.querySelector(".portadaEditada");
        if (portada.files.length > 0) {
          formData.append("portadaEditada", portada.files[0]);
        }

        const res = await fetch("../php/candidatura-subsanar.php", {
          method: "POST",
          body: formData
        });

        const r = await res.json();

        if (r.ok) {
          Swal.fire("Enviado", "La subsanación ha sido enviada", "success")
            .then(() => location.reload());
        } else {
          Swal.fire("Error", r.mensaje, "error");
        }
      });
    });

  } catch (e) {
    console.error(e);
  }

});