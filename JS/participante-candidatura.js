document.addEventListener("DOMContentLoaded", async () => {

  try {
    const res = await fetch("../php/candidatura-mi-estado.php");
    const data = await res.json();

    // NO HAY CANDIDATURA
    if (!data.ok || !data.candidatura) {
      document.getElementById("sinCandidatura").style.display = "block";
      return;
    }

    const c = data.candidatura;

    // MOSTRAR BLOQUE
    document.getElementById("conCandidatura").style.display = "block";

    // TÍTULO
    document.getElementById("titulo").textContent = c.titulo_obra;

    // ESTADO
    const estadoEl = document.getElementById("estado");
    estadoEl.textContent = c.estado.replace("_", " ");

    if (c.estado === "aceptada") estadoEl.style.color = "green";
    if (c.estado === "rechazada") estadoEl.style.color = "red";
    if (c.estado === "en_proceso") estadoEl.style.color = "orange";

    // SINOPSIS
    document.getElementById("sinopsis").textContent = c.sinopsis;

    // VIDEO + PORTADA
    const video = document.getElementById("video");
    video.src = c.video_ruta;
    video.poster = c.portada_ruta;

    // RECHAZADA → SUBSANAR
    if (c.estado === "rechazada") {
      document.getElementById("rechazoBox").style.display = "block";
      document.getElementById("motivoRechazo").textContent = c.motivo_rechazo;

      document.getElementById("subsanarBox").style.display = "block";

      document.getElementById("btnSubsanar").onclick = async () => {
        const mensaje = document
          .getElementById("mensajeSubsanacion")
          .value.trim();

        if (!mensaje) {
          Swal.fire("Error", "Debes escribir un mensaje de subsanación", "error");
          return;
        }

        const r = await fetch("../php/candidatura-subsanar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ mensaje })
        }).then(r => r.json());

        if (r.ok) {
          Swal.fire("Enviado", r.msg, "success");
          location.reload();
        } else {
          Swal.fire("Error", r.msg, "error");
        }
      };
    }

  } catch (e) {
    console.error(e);
    document.getElementById("sinCandidatura").style.display = "block";
  }

});
