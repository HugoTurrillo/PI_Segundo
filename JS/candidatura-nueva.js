document.addEventListener("DOMContentLoaded", async () => {

  const form = document.getElementById("form-candidatura");
  const error = document.getElementById("error-global");

  const inputNombre = document.getElementById("nombre_contacto");
  const inputEmail  = document.getElementById("email_contacto");

  /* ======================================================
     AUTORELLENAR DATOS DEL USUARIO LOGUEADO
  ====================================================== */
  try {
    const resUser = await fetch("../php/usuario-mis-datos.php");
    const userData = await resUser.json();

    if (userData.ok && userData.usuario) {
      inputNombre.value = userData.usuario.nombre_completo;
      inputEmail.value  = userData.usuario.email;

      inputNombre.readOnly = true;
      inputEmail.readOnly  = true;
    }
  } catch (e) {
    console.error("No se pudieron cargar los datos del usuario", e);
  }

  /* ======================================================
     ENVÍO DEL FORMULARIO (CON ARCHIVOS)
  ====================================================== */
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    error.textContent = "";

    const formData = new FormData(form);

    try {
      const res = await fetch("../php/candidatura-insertar.php", {
        method: "POST",
        body: formData
      });

      const text = await res.text();
      console.log("Respuesta cruda del servidor:", text);

      const r = JSON.parse(text);

      if (r.ok) {
        window.location.href = "participante_candidatura.html";
      } else {
        error.textContent = r.mensaje || "Error al enviar candidatura";
      }

    } catch (err) {
      console.error(err);
      error.textContent = "Error inesperado al enviar la candidatura";
    }
  });

});
