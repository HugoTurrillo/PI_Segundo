document.addEventListener("DOMContentLoaded", async () => {

  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  if (!id) {
    document.getElementById("evento-titulo").textContent = "Evento no encontrado";
    return;
  }

  try {
    const res = await fetch(`../php/evento-detalle.php?id=${id}`);
    const data = await res.json();

    if (!data.ok) {
      document.getElementById("evento-titulo").textContent = "Evento no encontrado";
      return;
    }

    const evento = data.evento;

    document.getElementById("evento-titulo").textContent = evento.titulo;
    document.getElementById("evento-fecha").textContent = `Fecha: ${evento.fecha}`;
    document.getElementById("evento-descripcion").textContent = evento.descripcion;

  } catch (error) {
    console.error("Error cargando evento:", error);
    document.getElementById("evento-titulo").textContent = "Error cargando el evento";
  }

});
