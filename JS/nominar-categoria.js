document.addEventListener("DOMContentLoaded", async () => {

  const params = new URLSearchParams(window.location.search);
  const idCandidatura = params.get("id_candidatura");

  const info = document.getElementById("info-candidatura");
  const select = document.getElementById("categoria");
  const error = document.getElementById("error-global");
  const btn = document.getElementById("btn-nominar");

  if (!idCandidatura) {
    info.textContent = "ID de candidatura no válido.";
    return;
  }

  // =========================
  // CARGAR CANDIDATURA
  // =========================
  try {
    const res = await fetch(`../php/candidatura-obtener.php?id=${idCandidatura}`);
    const data = await res.json();

    if (!data.ok) {
      info.textContent = "Error cargando la candidatura.";
      return;
    }

    const c = data.data;

    info.innerHTML = `
      <h3>${c.titulo_obra}</h3>
      <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
      <p><strong>Email:</strong> ${c.email_contacto}</p>
    `;

  } catch {
    info.textContent = "Error cargando la candidatura.";
    return;
  }

  // =========================
  // CARGAR CATEGORÍAS
  // =========================
  const resCat = await fetch("../php/categorias-listar.php");
  const dataCat = await resCat.json();

  dataCat.data.forEach(cat => {
    const opt = document.createElement("option");
    opt.value = cat.id;
    opt.textContent = cat.nombre;
    select.appendChild(opt);
  });

  // =========================
  // NOMINAR
  // =========================
  btn.addEventListener("click", async () => {

    const idCategoria = select.value;

    if (!idCategoria) {
      error.textContent = "Selecciona una categoría.";
      return;
    }

    const res = await fetch("../php/candidatura-nominar.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id_candidatura: idCandidatura,
        id_categoria: idCategoria
      })
    });

    const r = await res.json();

    if (r.ok) {
      window.location.href = "candidatura.html";
    } else {
      error.textContent = r.msg;
    }
  });

});
