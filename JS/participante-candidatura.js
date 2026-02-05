document.addEventListener("DOMContentLoaded", async () => {

  const sinCandidatura = document.getElementById("sinCandidatura");
  const conCandidatura = document.getElementById("conCandidatura");

  const titulo = document.getElementById("titulo");
  const estado = document.getElementById("estado");
  const sinopsis = document.getElementById("sinopsis");
  const motivoRechazo = document.getElementById("motivoRechazo");

  const video = document.getElementById("video");

  const rechazoBox = document.getElementById("rechazoBox");
  const subsanarBox = document.getElementById("subsanarBox");

  const tituloEditado = document.getElementById("tituloEditado");
  const sinopsisEditada = document.getElementById("sinopsisEditada");
  const portadaEditada = document.getElementById("portadaEditada");
  const mensajeSubsanacion = document.getElementById("mensajeSubsanacion");
  const btnSubsanar = document.getElementById("btnSubsanar");

  try {
    const res = await fetch("../php/candidatura-mi-estado.php");
    const data = await res.json();

    if (!data.ok || !data.candidatura) {
      sinCandidatura.style.display = "block";
      return;
    }

    const c = data.candidatura;

    conCandidatura.style.display = "block";
    titulo.textContent = c.titulo_obra;
    estado.textContent = c.estado;
    sinopsis.textContent = c.sinopsis;

    if (c.video_ruta) {
      video.src = c.video_ruta;
      video.style.display = "block";
    }

    if (c.estado === "rechazada") {
      rechazoBox.style.display = "block";
      motivoRechazo.textContent = c.motivo_rechazo ?? "";
      subsanarBox.style.display = "block";
    }

  } catch (e) {
    console.error(e);
  }

  btnSubsanar.addEventListener("click", async () => {

    const formData = new FormData();
    formData.append("tituloEditado", tituloEditado.value);
    formData.append("sinopsisEditada", sinopsisEditada.value);
    formData.append("mensajeSubsanacion", mensajeSubsanacion.value);

    if (portadaEditada.files.length > 0) {
      formData.append("portadaEditada", portadaEditada.files[0]);
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
