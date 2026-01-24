document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("form-candidatura");

  form.addEventListener("submit", async e => {
    e.preventDefault();

    const titulo = document.getElementById("titulo_obra").value.trim();
    const nombre = document.getElementById("nombre_contacto").value.trim();
    const email = document.getElementById("email_contacto").value.trim();
    const dni = document.getElementById("dni").value.trim().toUpperCase();
    const sinopsis = document.getElementById("sinopsis").value.trim();

    const errTitulo = document.getElementById("error-titulo");
    const errNombre = document.getElementById("error-nombre");
    const errEmail = document.getElementById("error-email");
    const errDni = document.getElementById("error-dni");
    const errGlobal = document.getElementById("error-global");

    errTitulo.textContent = "";
    errNombre.textContent = "";
    errEmail.textContent = "";
    errDni.textContent = "";
    errGlobal.textContent = "";

    let valido = true;

    if (titulo === "") {
      errTitulo.textContent = "El título es obligatorio";
      valido = false;
    }

    if (nombre === "") {
      errNombre.textContent = "El nombre es obligatorio";
      valido = false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errEmail.textContent = "Email no válido";
      valido = false;
    }

    const dniRegex = /^[0-9]{8}[A-Z]$/;
    if (!dniRegex.test(dni)) {
      errDni.textContent = "DNI inválido (8 números y una letra)";
      valido = false;
    }

    if (!valido) {
      errGlobal.textContent = "Corrige los errores antes de enviar";
      return;
    }

    const res = await fetch("../php/candidatura-insertar.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        titulo_obra: titulo,
        nombre_contacto: nombre,
        email_contacto: email,
        dni: dni,
        sinopsis: sinopsis
      })
    });

    const r = await res.json();

    if (r.ok) {
      window.location.href = "participante.html";
    } else {
      errGlobal.textContent = r.mensaje || "Error al enviar candidatura";
    }
  });

});
