document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("form-candidatura");
  const errorGlobal = document.getElementById("error-global");

  if (!form) {
    console.error("No existe el formulario");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorGlobal.textContent = "";

    const titulo   = document.getElementById("titulo_obra").value.trim();
    const nombre   = document.getElementById("nombre_contacto").value.trim();
    const email    = document.getElementById("email_contacto").value.trim();
    const dni      = document.getElementById("dni").value.trim().toUpperCase();
    const sinopsis = document.getElementById("sinopsis").value.trim();

    // =====================
    // VALIDACIONES
    // =====================
    if (!titulo || !nombre || !email || !dni) {
      errorGlobal.textContent = "Todos los campos son obligatorios.";
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errorGlobal.textContent = "El email no es válido.";
      return;
    }

    const dniRegex = /^[0-9]{8}[A-Z]$/;
    if (!dniRegex.test(dni)) {
      errorGlobal.textContent = "El DNI debe tener 8 números y una letra.";
      return;
    }

    // =====================
    // ENVÍO AL BACKEND
    // =====================
    try {
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
        errorGlobal.textContent = r.mensaje || "Error al enviar candidatura";
      }

    } catch (err) {
      console.error(err);
      errorGlobal.textContent = "Error de comunicación con el servidor.";
    }
  });
});
