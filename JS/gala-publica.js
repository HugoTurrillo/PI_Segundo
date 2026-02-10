document.addEventListener("DOMContentLoaded", async () => {
  const cont = document.getElementById("gala-detalle");

  try {
    const res = await fetch("../php/gala-listar.php");
    const json = await res.json();

    if (!json.ok) {
      cont.innerHTML = "<p>No hay información de la gala disponible.</p>";
      return;
    }

    const g = json.data;

    cont.innerHTML = `
      <h3>${g.titulo}</h3>
      <p><strong>Fecha:</strong> ${g.fecha}</p>
      <p><strong>Hora:</strong> ${g.hora}</p>
      <p><strong>Lugar:</strong> ${g.lugar}</p>
      <p>${g.descripcion ?? ""}</p>
    `;

  } catch (e) {
    cont.innerHTML = "<p>Error cargando la información de la gala.</p>";
  }
});
