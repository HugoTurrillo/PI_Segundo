document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("form-candidatura");
  const error = document.getElementById("error-global");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    error.textContent = "";

    const data = {
      titulo_obra: document.getElementById("titulo_obra").value.trim(),
      sinopsis: document.getElementById("sinopsis").value.trim(),
      nombre_contacto: document.getElementById("nombre_contacto").value.trim(),
      email_contacto: document.getElementById("email_contacto").value.trim(),
      dni: document.getElementById("dni").value.trim().toUpperCase()
    };

    const res = await fetch("../php/candidatura-insertar.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });

    const r = await res.json();

    if (r.ok) {
      window.location.href = "participante_candidatura.html";
    } else {
      error.textContent = r.mensaje;
    }
  });

});
