document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("form-candidatura");
  if (!form) return;

  form.addEventListener("submit", async e => {
    e.preventDefault();

    const errGlobal = document.getElementById("error-global");
    errGlobal.textContent = "";

    // Creamos FormData para enviar archivos
    const datos = new FormData();

    datos.append("titulo_obra", document.getElementById("titulo_obra").value.trim());
    datos.append("ficha_tecnica", document.getElementById("ficha_tecnica").value.trim());
    datos.append("sinopsis", document.getElementById("sinopsis").value.trim());
    datos.append("nombre_contacto", document.getElementById("nombre_contacto").value.trim());
    datos.append("email_contacto", document.getElementById("email_contacto").value.trim());
    datos.append("dni", document.getElementById("dni").value.trim().toUpperCase());

    // Archivos
    datos.append("cartel", document.getElementById("cartel").files[0]);
    datos.append("expediente", document.getElementById("expediente").files[0]);
    datos.append("video", document.getElementById("video").files[0]);

    try {
      const res = await fetch("../php/candidatura-insertar.php", {
        method: "POST",
        body: datos
      });

      const r = await res.json();

      if (r.ok) {
        window.location.href = "participante_candidatura.html";
      } else {
        errGlobal.textContent = r.msg || "Error al enviar la candidatura";
      }

    } catch (error) {
      errGlobal.textContent = "Error de conexión con el servidor";
    }

  });

});