document.addEventListener("DOMContentLoaded", async () => {

  const calendarEl = document.getElementById("calendar");
  if (!calendarEl) return;

  try {
    const res = await fetch("../php/eventos-publicos.php");
    const data = await res.json();

    if (!data.ok) {
      console.error("Error cargando eventos");
      return;
    }

    const eventosPorDia = data.eventos;

    // 🔹 Crear SOLO marcas por día
    const eventosCalendario = Object.keys(eventosPorDia).map(fecha => ({
      start: fecha,
      display: "background",
      backgroundColor: "#f44336" // rojo UEM
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

      events: eventosCalendario,

      dateClick(info) {
        const fecha = info.dateStr;
        const eventos = eventosPorDia[fecha];

        if (!eventos || eventos.length === 0) return;

        let html = `<div style="text-align:left;">`;

        eventos.forEach(ev => {
          html += `
            <div style="margin-bottom:1rem;">
              <strong>${ev.hora} – ${ev.titulo}</strong><br>
              <span>${ev.descripcion}</span>
            </div>
          `;
        });

        html += `</div>`;

        Swal.fire({
          title: `Eventos del ${fecha}`,
          html: html,
          icon: "info",
          confirmButtonText: "Cerrar"
        });
      }
    });

    calendar.render();

  } catch (e) {
    console.error("Error cargando calendario:", e);
  }
});
