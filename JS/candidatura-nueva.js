document.addEventListener("DOMContentLoaded", () => {

  cargarCategorias();

  const form = document.getElementById("form-candidatura");
  const error = document.getElementById("error-global");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    error.textContent = "";

    const formData = new FormData(form);

    // DNI si existe en tu formulario
    const dniInput = document.getElementById("dni");
    if (dniInput) {
      formData.set("dni", dniInput.value.trim().toUpperCase());
    }

    try {
      const res = await fetch("../php/candidatura-insertar.php", {
        method: "POST",
        body: formData
      });

      const r = await res.json();

      if (r.ok) {
        window.location.href = "participante_candidatura.php";
      } else {
        error.textContent = r.mensaje || "Error al enviar candidatura";
      }

    } catch (err) {
      error.textContent = "Error de conexión con el servidor";
    }
  });

});


async function cargarCategorias() {
  try {
    const res = await fetch("../php/categorias-listar.php");
    const data = await res.json();

    if (!data.ok) return;

    const select = document.getElementById("categoria");

    data.data.forEach(cat => {
      const opt = document.createElement("option");
      opt.value = cat.id;
      opt.textContent = cat.nombre;
      select.appendChild(opt);
    });

  } catch (err) {
    console.error("Error cargando categorías:", err);
  }
}