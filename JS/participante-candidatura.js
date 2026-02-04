document.addEventListener("DOMContentLoaded", async () => {

  const res = await fetch("../php/candidatura-mi-estado.php");
  const data = await res.json();

  if (!data.ok || !data.candidatura) {
    document.getElementById("sinCandidatura").style.display = "block";
    return;
  }

  const c = data.candidatura;
  document.getElementById("conCandidatura").style.display = "block";

  document.getElementById("titulo").textContent = c.titulo_obra;
  document.getElementById("estado").textContent = c.estado.replace("_", " ");
  document.getElementById("sinopsis").textContent = c.sinopsis;

  const video = document.getElementById("video");
  video.innerHTML = "";

  const source = document.createElement("source");
  source.src = ".." + c.video_ruta;
  source.type = "video/mp4";
  video.poster = ".." + c.portada_ruta;
  video.appendChild(source);
  video.load();

  /* ======================================================
     SI ESTÁ RECHAZADA → SUBSANAR
  ====================================================== */
  if (c.estado === "rechazada") {

    document.getElementById("rechazoBox").style.display = "block";
    document.getElementById("motivoRechazo").textContent = c.motivo_rechazo;

    document.getElementById("subsanarBox").style.display = "block";

    // 🔑 AQUÍ ESTÁ LA CLAVE
    const textareaSinopsis = document.getElementById("sinopsisEditada");

    // Forzamos que aparezca la sinopsis anterior
    textareaSinopsis.value = c.sinopsis ?? "";

    document.getElementById("btnSubsanar").onclick = async () => {

      const mensaje = document
        .getElementById("mensajeSubsanacion")
        .value
        .trim();

      if (!mensaje) {
        return Swal.fire("Error", "Mensaje obligatorio", "error");
      }

      const fd = new FormData();
      fd.append("mensaje", mensaje);

      const nuevaSinopsis = textareaSinopsis.value.trim();
      if (nuevaSinopsis !== "") {
        fd.append("sinopsis", nuevaSinopsis);
      }

      const videoNuevo = document.getElementById("videoEditado").files[0];
      if (videoNuevo) {
        fd.append("video", videoNuevo);
      }

      const portadaNueva = document.getElementById("portadaEditada").files[0];
      if (portadaNueva) {
        fd.append("portada", portadaNueva);
      }

      const r = await fetch("../php/candidatura-subsanar.php", {
        method: "POST",
        body: fd
      }).then(r => r.json());

      if (r.ok) {
        Swal.fire("Correcto", r.msg, "success");
        location.reload();
      } else {
        Swal.fire("Error", r.msg, "error");
      }
    };
  }

});
