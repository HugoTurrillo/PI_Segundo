document.addEventListener("DOMContentLoaded", async () => {

  const form = document.getElementById("form-candidatura");
  const error = document.getElementById("error-global");

  const inputNombre  = document.getElementById("nombre_contacto");
  const inputEmail   = document.getElementById("email_contacto");
  const inputPerfil  = document.getElementById("perfil_participante");

  /* AUTORELLENAR */
  try {
    const resUser = await fetch("../php/usuario-mis-datos.php");
    const userData = await resUser.json();

    if (userData.ok) {
      inputNombre.value = userData.usuario.nombre_completo;
      inputEmail.value  = userData.usuario.email;
      inputPerfil.value = userData.usuario.rol_participante ?? "";
    }
  } catch (e) {
    console.error(e);
  }

  /* ENVÍO */
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
