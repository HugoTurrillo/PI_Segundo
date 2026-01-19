document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR EVENTOS
    // ============================
    async function cargarEventos() {
        const contenedor = document.getElementById("lista-eventos");
        if (!contenedor) return; // Solo se ejecuta en eventos.html

        const respuesta = await fetch("../php/eventos-listar.php");
        const eventos = await respuesta.json();

        contenedor.innerHTML = "";

        eventos.forEach(ev => {
            contenedor.innerHTML += `
                <div class="panel-card">
                    <h3>${ev.titulo}</h3>
                    <p>Fecha: ${ev.fecha}</p>
                    <p>${ev.descripcion}</p>

                    <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                        <a href="evento-editar.html?id=${ev.id}" class="btn login-btn" style="padding: 0.5rem 1rem;">Editar</a>

                        <button class="btn login-btn btn-eliminar-evento" 
                                data-id="${ev.id}" 
                                style="padding: 0.5rem 1rem; background-color: #555;">
                            Eliminar
                        </button>
                    </div>
                </div>
            `;
        });

        activarBotonesEliminar();
    }

    cargarEventos();



    // ============================
    // ELIMINAR EVENTO
    // ============================
    function activarBotonesEliminar() {
        document.querySelectorAll(".btn-eliminar-evento").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                const confirmacion = await Swal.fire({
                    title: "¿Eliminar evento?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                });

                if (!confirmacion.isConfirmed) return;

                const res = await fetch(`../php/evento-eliminar.php?id=${id}`);
                const r = await res.json();

                Swal.fire({
                    icon: r.ok ? "success" : "error",
                    title: r.ok ? "Eliminado" : "Error",
                    text: r.msg
                });
                if (r.ok) cargarEventos();
            });
        });
    }



    // ============================
    // FORMULARIO (CREAR / EDITAR)
    // ============================
    const form = document.getElementById("form-evento");
    if (!form) return; // Solo se ejecuta en nuevo o editar

    const titulo = document.getElementById("titulo");
    const fecha = document.getElementById("fecha");
    const descripcion = document.getElementById("descripcion");

    const errorTitulo = document.getElementById("error-titulo");
    const errorFecha = document.getElementById("error-fecha");
    const errorDescripcion = document.getElementById("error-descripcion");
    const errorGlobal = document.getElementById("error-global");

    const hoy = new Date().toISOString().split("T")[0];
    const maxFecha = "2026-12-21";



    // ============================
    // CARGAR DATOS PARA EDITAR
    // ============================
    async function cargarEventoEditar() {
        const params = new URLSearchParams(window.location.search);
        const id = params.get("id");

        if (!id) return; // No estamos en editar

        const res = await fetch(`../php/evento-obtener.php?id=${id}`);
        const evento = await res.json();

        if (!evento || !evento.id) {
            errorGlobal.textContent = "No se encontró el evento.";
            return;
        }

        // Rellenar formulario
        titulo.value = evento.titulo;
        fecha.value = evento.fecha;
        descripcion.value = evento.descripcion;

        // Guardar ID para el submit
        form.dataset.id = id;
    }

    cargarEventoEditar();



    // ============================
    // SUBMIT (CREAR O EDITAR)
    // ============================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        let valido = true;

        // Reset errores
        errorTitulo.textContent = "";
        errorFecha.textContent = "";
        errorDescripcion.textContent = "";
        errorGlobal.textContent = "";

        // VALIDACIONES
        if (titulo.value.trim() === "") {
            errorTitulo.textContent = "El título no puede estar vacío.";
            valido = false;
        }

        if (fecha.value === "") {
            errorFecha.textContent = "La fecha es obligatoria.";
            valido = false;
        } else if (fecha.value < hoy) {
            errorFecha.textContent = "La fecha no puede ser anterior a hoy.";
            valido = false;
        } else if (fecha.value > maxFecha) {
            errorFecha.textContent = "La fecha no puede ser posterior al 21/12/2026.";
            valido = false;
        }

        if (descripcion.value.trim() === "") {
            errorDescripcion.textContent = "La descripción no puede estar vacía.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
            return;
        }



        // ============================
        // EDITAR EVENTO
        // ============================
        const id = form.dataset.id;

        if (id) {
            const datos = {
                id: id,
                titulo: titulo.value,
                fecha: fecha.value,
                descripcion: descripcion.value
            };

            const respuesta = await fetch("../php/evento-editar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const resultado = await respuesta.json();

            if (resultado.ok) {
                await Swal.fire({
                    icon: "success",
                    title: "Evento editado",
                    text: "El evento se ha editado correctamente"
                });

                window.location.href = "eventos.html";
            } else {
                errorGlobal.textContent = resultado.msg;
            }

            return;
        }



        // ============================
        // CREAR EVENTO
        // ============================
        const datos = {
            titulo: titulo.value,
            fecha: fecha.value,
            descripcion: descripcion.value
        };

        const respuesta = await fetch("../php/evento-nuevo.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.ok) {
            await Swal.fire({
                    icon: "success",
                    title: "Evento creado",
                    text: "El evento se ha creado correctamente"
                });
            window.location.href = "eventos.html";
        } else {
            errorGlobal.textContent = resultado.msg;
        }
    });

});