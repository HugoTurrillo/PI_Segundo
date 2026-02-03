document.addEventListener("DOMContentLoaded", () => {

    /* ======================================================
       LISTAR EVENTOS (eventos.html)
    ====================================================== */
    async function cargarEventos() {
        const contenedor = document.getElementById("lista-eventos");
        if (!contenedor) return;

        try {
            const respuesta = await fetch("../php/eventos-listar.php");
            const data = await respuesta.json();

            if (!data.ok) {
                contenedor.innerHTML = "<p>Error al cargar eventos</p>";
                return;
            }

            const eventos = data.eventos || [];
            contenedor.innerHTML = "";

            if (eventos.length === 0) {
                contenedor.innerHTML = "<p>No hay eventos creados.</p>";
                return;
            }

            eventos.forEach(ev => {
                contenedor.innerHTML += `
                    <div class="panel-card">
                        <h3>${ev.titulo}</h3>
                        <p><strong>Fecha:</strong> ${ev.fecha}</p>
                        <p><strong>Hora:</strong> ${ev.hora}</p>
                        <p>${ev.descripcion}</p>

                        <div style="margin-top:1rem; display:flex; gap:1rem;">
                            <a href="evento-editar.html?id=${ev.id}"
                               class="btn login-btn"
                               style="padding:0.5rem 1rem;">
                               Editar
                            </a>

                            <button class="btn login-btn btn-eliminar-evento"
                                    data-id="${ev.id}"
                                    style="padding:0.5rem 1rem; background:#555;">
                                Eliminar
                            </button>
                        </div>
                    </div>
                `;
            });

            activarEliminar();

        } catch (e) {
            console.error(e);
            contenedor.innerHTML = "<p>Error inesperado</p>";
        }
    }

    cargarEventos();


    /* ======================================================
       ELIMINAR EVENTO
    ====================================================== */
    function activarEliminar() {
        document.querySelectorAll(".btn-eliminar-evento").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                const conf = await Swal.fire({
                    title: "¿Eliminar evento?",
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

                Swal.fire({
                    icon: r.ok ? "success" : "error",
                    title: r.ok ? "Eliminado" : "Error",
                    text: r.mensaje
                });

                if (r.ok) cargarEventos();
            });
        });
    }


    /* ======================================================
       FORMULARIO CREAR / EDITAR (evento-nuevo / evento-editar)
    ====================================================== */
    const form = document.getElementById("form-evento");
    if (!form) return;

    const titulo = document.getElementById("titulo");
    const fecha = document.getElementById("fecha");
    const hora = document.getElementById("hora");
    const descripcion = document.getElementById("descripcion");

    const errorTitulo = document.getElementById("error-titulo");
    const errorFecha = document.getElementById("error-fecha");
    const errorHora = document.getElementById("error-hora");
    const errorDescripcion = document.getElementById("error-descripcion");
    const errorGlobal = document.getElementById("error-global");

    const hoy = new Date().toISOString().split("T")[0];
    const maxFecha = "2026-12-21";


    /* ============================
       CARGAR EVENTO PARA EDITAR
    ============================ */
    async function cargarEventoEditar() {
        const params = new URLSearchParams(window.location.search);
        const id = params.get("id");
        if (!id) return;

        const res = await fetch(`../php/evento-obtener.php?id=${id}`);
        const data = await res.json();

        if (!data.ok || !data.evento) {
            errorGlobal.textContent = "Evento no encontrado";
            return;
        }

        const ev = data.evento;
        titulo.value = ev.titulo;
        fecha.value = ev.fecha;
        hora.value = ev.hora;
        descripcion.value = ev.descripcion;

        form.dataset.id = id;
    }

    cargarEventoEditar();


    /* ============================
       SUBMIT
    ============================ */
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        errorTitulo.textContent = "";
        errorFecha.textContent = "";
        errorHora.textContent = "";
        errorDescripcion.textContent = "";
        errorGlobal.textContent = "";

        let valido = true;

        if (!titulo.value.trim()) {
            errorTitulo.textContent = "El título es obligatorio";
            valido = false;
        }

        if (!fecha.value) {
            errorFecha.textContent = "La fecha es obligatoria";
            valido = false;
        } else if (fecha.value < hoy || fecha.value > maxFecha) {
            errorFecha.textContent = "Fecha fuera de rango";
            valido = false;
        }

        if (!hora.value) {
            errorHora.textContent = "La hora es obligatoria";
            valido = false;
        }

        if (!descripcion.value.trim()) {
            errorDescripcion.textContent = "La descripción es obligatoria";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Revisa los errores del formulario";
            return;
        }

        const datos = {
            titulo: titulo.value,
            fecha: fecha.value,
            hora: hora.value,
            descripcion: descripcion.value
        };

        const id = form.dataset.id;

        /* ============================
           EDITAR
        ============================ */
        if (id) {
            datos.id = id;

            const res = await fetch("../php/evento-editar.html", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const r = await res.json();

            if (r.ok) {
                await Swal.fire("OK", "Evento editado correctamente", "success");
                window.location.href = "eventos.html";
            } else {
                errorGlobal.textContent = r.mensaje;
            }
            return;
        }

        /* ============================
           CREAR (con confirmación)
        ============================ */
        const res = await fetch("../php/evento-nuevo.html", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        const r = await res.json();

        if (r.confirmar) {
            const conf = await Swal.fire({
                title: "Evento duplicado",
                text: r.mensaje,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, crear",
                cancelButtonText: "Cancelar"
            });

            if (!conf.isConfirmed) return;

            await fetch("../php/evento-nuevo.html?forzar=1", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            await Swal.fire("Creado", "Evento creado correctamente", "success");
            window.location.href = "eventos.html";
            return;
        }

        if (r.ok) {
            await Swal.fire("Creado", "Evento creado correctamente", "success");
            window.location.href = "eventos.html";
        } else {
            errorGlobal.textContent = r.mensaje;
        }
    });

});
