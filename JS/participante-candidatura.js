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

  if (c.estado === "rechazada") {
    document.getElementById("rechazoBox").style.display = "block";
    document.getElementById("motivoRechazo").textContent = c.motivo_rechazo;
    document.getElementById("subsanarBox").style.display = "block";

    document.getElementById("btnSubsanar").onclick = async () => {
      const mensaje = document.getElementById("mensajeSubsanacion").value.trim();
      if (!mensaje) return Swal.fire("Error", "Mensaje obligatorio", "error");

      const fd = new FormData();
      fd.append("mensaje", mensaje);

      const sinopsis = document.getElementById("sinopsisEditada").value;
      if (sinopsis) fd.append("sinopsis", sinopsis);

      const v = document.getElementById("videoEditado").files[0];
      if (v) fd.append("video", v);

      const p = document.getElementById("portadaEditada").files[0];
      if (p) fd.append("portada", p);

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
