document.addEventListener("DOMContentLoaded", async () => {

  const form = document.getElementById("form-candidatura");
  const error = document.getElementById("error-global");

  const inputNombre = document.getElementById("nombre_contacto");
  const inputEmail  = document.getElementById("email_contacto");

  /* ======================================================
     BLOQUEAR SI YA EXISTE CANDIDATURA
  ====================================================== */
  try {
    const resEstado = await fetch("../php/candidatura-mi-estado.php");
    const estadoData = await resEstado.json();

    if (estadoData.ok && estadoData.candidatura) {
      Swal.fire({
        icon: "info",
         iconColor: "#FF3228",
        title: "Candidatura ya enviada",
        text: "No puedes enviar otra candidatura. Si ha sido rechazada, edítala desde 'Mi candidatura'."
      }).then(() => {
        window.location.href = "participante_candidatura.html";
      });
      return;
    }
  } catch (e) {
    console.error(e);
  }

  /* ======================================================
     AUTORELLENAR USUARIO
  ====================================================== */
  try {
    const resUser = await fetch("../php/usuario-mis-datos.php");
    const userData = await resUser.json();

    if (userData.ok) {
      inputNombre.value = userData.usuario.nombre_completo;
      inputEmail.value  = userData.usuario.email;
      inputNombre.readOnly = true;
      inputEmail.readOnly  = true;
    }
  } catch (e) {
    console.error(e);
  }

  /* ======================================================
     ENVÍO FORMULARIO
  ====================================================== */
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    error.textContent = "";

    const formData = new FormData(form);

    const res = await fetch("../php/candidatura-insertar.php", {
      method: "POST",
      body: formData
    });

    const r = await res.json();

    if (r.ok) {
      window.location.href = "participante_candidatura.html";
    } else {
      error.textContent = r.mensaje;
    }
  });
});
