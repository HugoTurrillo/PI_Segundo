/**
 * Calendario público con FullCalendar.
 */

document.addEventListener("DOMContentLoaded", async () => {

  const calendarEl = document.getElementById("calendar");
  const avisoEl = document.getElementById("calendario-aviso");
  if (!calendarEl) return;

  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));

  function esMovil() {
    return window.matchMedia("(max-width: 768px)").matches;
  }

  /** Altura fija: FullCalendar falla con aspectRatio si el contenedor aún no tiene ancho */
  function alturaCalendario() {
    return esMovil() ? 520 : 650;
  }

  function mesActualStr() {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
  }

  function calcularFechaInicial(eventosPorDia) {
    const fechas = Object.keys(eventosPorDia).sort();
    if (!fechas.length) return undefined;

    const mesHoy = mesActualStr();
    if (fechas.some(f => f.startsWith(mesHoy))) return undefined;

    const meses = [...new Set(fechas.map(f => f.slice(0, 7)))].sort();
    const futuro = meses.find(m => m >= mesHoy);
    const mesElegido = futuro || meses[meses.length - 1];
    return `${mesElegido}-01`;
  }

  function construirEventos(eventosPorDia) {
    const eventos = [];

    Object.keys(eventosPorDia).forEach(fecha => {
      eventos.push({
        start: fecha,
        display: "background",
        classNames: ["fc-event-background-ue"],
      });

      eventosPorDia[fecha].forEach((ev, i) => {
        const hora = (ev.hora || "").substring(0, 5);
        eventos.push({
          id: `ev-${fecha}-${i}`,
          start: fecha,
          title: `${hora} – ${ev.titulo}`,
          allDay: true,
        });
      });
    });

    return eventos;
  }

  function mostrarEventosDia(fecha, eventosPorDia) {
    const eventos = eventosPorDia[fecha];
    if (!eventos || eventos.length === 0) return;

    let html = `<div class="eventos-fecha">`;
    eventos.forEach(ev => {
      html += `
        <div class="evento-item">
          <strong>${esc((ev.hora || "").substring(0, 5))} – ${esc(ev.titulo)}</strong><br>
          <span>${esc(ev.descripcion)}</span>
        </div>
      `;
    });
    html += `</div>`;

    Swal.fire({
      title: `Eventos del ${fecha}`,
      html: html,
      icon: "info",
      confirmButtonText: "Cerrar",
    });
  }

  function actualizarAviso(eventosPorDia, fechaVista) {
    if (!avisoEl) return;

    const mesVista = fechaVista.slice(0, 7);
    const hayEventos = Object.keys(eventosPorDia).some(f => f.startsWith(mesVista));

    if (hayEventos) {
      avisoEl.hidden = true;
      return;
    }

    avisoEl.hidden = false;
    avisoEl.textContent = Object.keys(eventosPorDia).length
      ? "Este mes no tiene eventos. Usa las flechas ← → para ir a otro mes."
      : "Aún no hay eventos programados en el festival.";
  }

  try {
    const res = await fetch("../php/eventos-publicos.php");
    const data = await res.json();

    if (!data.ok) {
      calendarEl.innerHTML = "<p>No se pudieron cargar los eventos.</p>";
      return;
    }

    const eventosPorDia = data.eventos;
    const eventosCalendario = construirEventos(eventosPorDia);
    const fechaInicial = calcularFechaInicial(eventosPorDia);

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      initialDate: fechaInicial,
      locale: "es",
      height: alturaCalendario(),
      handleWindowResize: true,
      dayMaxEvents: 2,
      fixedWeekCount: false,

      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,listMonth",
      },

      buttonText: {
        today: "Hoy",
        month: "Mes",
        list: "Lista",
      },

      views: {
        dayGridMonth: {
          eventDisplay: "background",
        },
        listMonth: {
          noEventsText: "No hay eventos programados en este mes.",
        },
      },

      events: eventosCalendario,

      datesSet(arg) {
        actualizarAviso(eventosPorDia, arg.startStr);
      },

      dateClick(info) {
        mostrarEventosDia(info.dateStr, eventosPorDia);
      },

      eventClick(info) {
        info.jsEvent.preventDefault();
        mostrarEventosDia(info.event.startStr, eventosPorDia);
      },
    });

    calendar.render();

    requestAnimationFrame(() => calendar.updateSize());

    window.addEventListener("resize", () => {
      calendar.setOption("height", alturaCalendario());
    });

  } catch (e) {
    console.error("Error cargando calendario:", e);
    calendarEl.innerHTML = "<p>Error al cargar el calendario.</p>";
  }
});
