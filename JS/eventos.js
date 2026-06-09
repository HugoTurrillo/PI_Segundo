/**
 * Cargo los eventos del organizador, muestro el formulario de alta/edición y envío los datos al servidor.
 */

document.addEventListener("DOMContentLoaded", () => {

  const lista = document.getElementById("lista-eventos");
  const form = document.getElementById("form-evento");

  async function cargarEventos() {
    if (!lista) return;

    const res = await fetch("../php/eventos-listar.php");
    const data = await res.json();

    lista.innerHTML = "";

    data.eventos.forEach(ev => {
      lista.innerHTML += `
        <div class="panel-card">
          <h3>${ev.titulo}</h3>
          <p><strong>Fecha:</strong> ${ev.fecha}</p>
          <p><strong>Hora:</strong> ${ev.hora}</p>
          <p>${ev.descripcion}</p>

          <div class="evento-acciones">
            <a href="evento-editar.html?id=${ev.id}" class="btn login-btn">Editar</a>
            <button class="btn login-btn btn-eliminar" data-id="${ev.id}">Eliminar</button>
          </div>
        </div>
      `;
    });

    activarEliminar();
  }

  cargarEventos();

  /* =====================================================
     ELIMINAR EVENTO
  ===================================================== */
  function activarEliminar() {
    document.querySelectorAll(".btn-eliminar").forEach(btn => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;

        const conf = await Swal.fire({
          title: "¿Eliminar evento?",
          text: "Esta acción no se puede deshacer",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar"
        });

        if (!conf.isConfirmed) return;

        const res = await fetch("../php/evento-eliminar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id })
        });

        const r = await res.json();

        if (r.ok) {
          await Swal.fire("Eliminado", "Evento eliminado correctamente", "success");
          cargarEventos();
        } else {
          Swal.fire("Error", r.mensaje, "error");
        }
      });
    });
  }

  /* =====================================================
     FORMULARIO CREAR / EDITAR
  ===================================================== */
  if (!form) return;

  const titulo = document.getElementById("titulo");
  const fecha = document.getElementById("fecha");
  const hora = document.getElementById("hora");
  const descripcion = document.getElementById("descripcion");
  const errorGlobal = document.getElementById("error-global");

  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  /* ===== CARGAR PARA EDITAR ===== */
  if (id) {
    fetch(`../php/evento-obtener.php?id=${id}`)
      .then(r => r.json())
      .then(data => {
        const ev = data.evento;
        titulo.value = ev.titulo;
        fecha.value = ev.fecha;
        hora.value = ev.hora;
        descripcion.value = ev.descripcion;
        form.dataset.id = id;
      });
  }

  /* ===== SUBMIT ===== */
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const datos = {
      titulo: titulo.value,
      fecha: fecha.value,
      hora: hora.value,
      descripcion: descripcion.value
    };

    let url = "../php/evento-nuevo.php";

    if (form.dataset.id) {
      datos.id = form.dataset.id;
      url = "../php/evento-editar.php";
    }

    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(datos)
    });

    const r = await res.json();

    if (r.ok) {
      await Swal.fire("OK", "Evento guardado correctamente", "success");
      window.location.href = "eventos.html";
    } else {
      errorGlobal.textContent = r.mensaje;
    }
  });

});
