document.addEventListener("DOMContentLoaded", async () => {

  const calendarEl = document.getElementById("calendar");
  if (!calendarEl) return;

  try {
    const res = await fetch("../php/eventos-publicos.php");
    const data = await res.json();

    console.log("Eventos recibidos:", data);

    if (!data.ok) {
      console.error("Error en PHP:", data);
      return;
    }

    const eventos = data.eventos.map(ev => ({
      id: ev.id,
      title: ev.titulo,
      start: ev.fecha
    }));

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      locale: "es",
      height: "auto",

      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: ""
      },

      events: eventos,

      eventClick(info) {
        info.jsEvent.preventDefault();
        window.location.href = `evento.html?id=${info.event.id}`;
      }
    });

    calendar.render();

  } catch (error) {
    console.error("Error cargando calendario:", error);
  }
});
