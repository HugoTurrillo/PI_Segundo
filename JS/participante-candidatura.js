document.addEventListener("DOMContentLoaded", async () => {

  try {
    const res = await fetch("../php/candidatura-mi-estado.php");
    const data = await res.json();

    if (!data.ok || !data.candidatura) {
      document.getElementById("sinCandidatura").style.display = "block";
      return;
    }

    const c = data.candidatura;

    document.getElementById("conCandidatura").style.display = "block";
    document.getElementById("titulo").textContent = c.titulo_obra;
    document.getElementById("estado").textContent = c.estado;
    document.getElementById("sinopsis").textContent = c.sinopsis;

    if (c.estado === "rechazada") {
      document.getElementById("rechazoBox").style.display = "block";
      document.getElementById("motivoRechazo").textContent = c.motivo_rechazo;

      document.getElementById("subsanarBox").style.display = "block";

      document.getElementById("btnSubsanar").addEventListener("click", async () => {
        const mensaje = document.getElementById("mensajeSubsanacion").value.trim();
        const error = document.getElementById("subsanarError");
        error.textContent = "";

        if (!mensaje) {
          error.textContent = "Debes escribir un mensaje de subsanación";
          return;
        }

        const res = await fetch("../php/candidatura-subsanar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ mensaje })
        });

        const r = await res.json();

        if (r.ok) {
          location.reload();
        } else {
          error.textContent = r.msg || "Error al enviar subsanación";
        }
      });
    }

  } catch (err) {
    console.error("Error cargando candidatura:", err);
    document.getElementById("sinCandidatura").style.display = "block";
  }

});
