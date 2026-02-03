document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR EVENTOS DE GALA
    // ============================
    async function cargarGala() {
        const contenedor = document.querySelector(".panel-grid");
        if (!contenedor) return;

        try {
            const respuesta = await fetch("../php/gala-listar.php");
            const eventos = await respuesta.json();

            contenedor.innerHTML = "";

            // ===== TARJETA PARA SELECCIONAR EVENTO EXISTENTE =====
            contenedor.innerHTML += `
                <div class="panel-card" style="border:2px dashed #ccc; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">

                    <h3>Seleccionar evento existente</h3>
                    <p>Elige un evento ya creado para añadirlo a la gala.</p>

                    <a href="eventos.html" 
                       class="btn login-btn"
                       style="margin-top:1rem;">
                       Seleccionar evento
                    </a>
                </div>
            `;

            // ===== EVENTOS DE GALA =====
            eventos.forEach(ev => {
                contenedor.innerHTML += `
                    <div class="panel-card">

                        <img src="../uploads/${ev.imagen}" 
                             alt="Imagen gala"
                             style="width:100%; max-height:150px; object-fit:cover; border-radius:6px; margin-bottom:1rem;">

                        <h3>${ev.titulo}</h3>

                        <p><strong>Fecha:</strong> ${ev.fecha}</p>
                        <p><strong>Hora:</strong> ${ev.hora}</p>
                        <p><strong>Lugar:</strong> ${ev.lugar}</p>
                        <p>${ev.descripcion ?? ""}</p>

                        <div style="margin-top:1rem; display:flex; gap:1rem;">
                            <a href="gala-editar.html?id=${ev.id}"
                               class="btn login-btn"
                               style="padding:0.5rem 1rem;">
                                Editar
                            </a>

                            <button class="btn login-btn btn-eliminar-gala"
                                    data-id="${ev.id}"
                                    style="padding:0.5rem 1rem; background:#555;">
                                Eliminar
                            </button>
                        </div>
                    </div>
                `;
            });

            activarBotonesEliminar();

        } catch (err) {
            console.error(err);
            contenedor.innerHTML = "<p>Error cargando las galas.</p>";
        }
    }

    cargarGala();


    // ============================
    // ELIMINAR EVENTO DE GALA
    // ============================
    function activarBotonesEliminar() {
        document.querySelectorAll(".btn-eliminar-gala").forEach(btn => {

            btn.addEventListener("click", async () => {

                const id = btn.dataset.id;

                const confirmacion = await Swal.fire({
                    title: "¿Eliminar gala?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                });

                if (!confirmacion.isConfirmed) return;

                try {

                    const res = await fetch("../php/gala-eliminar.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: id })
                    });

                    const r = await res.json();

                    await Swal.fire({
                        icon: r.ok ? "success" : "error",
                        title: r.ok ? "Eliminado" : "Error",
                        text: r.msg
                    });

                    if (r.ok) cargarGala();

                } catch (err) {
                    console.error(err);
                    Swal.fire("Error", "No se pudo eliminar la gala", "error");
                }
            });
        });
    }


    // ============================
    // FORMULARIO (CREAR / EDITAR)
    // ============================
    const form = document.getElementById("form-gala");
    if (!form) return;

    const titulo = document.getElementById("titulo");
    const fecha = document.getElementById("fecha");
    const hora = document.getElementById("hora");
    const lugar = document.getElementById("lugar");
    const descripcion = document.getElementById("descripcion");
    const imagen = document.getElementById("imagen");

    const errorTitulo = document.getElementById("error-titulo");
    const errorFecha = document.getElementById("error-fecha");
    const errorHora = document.getElementById("error-hora");
    const errorLugar = document.getElementById("error-lugar");
    const errorDescripcion = document.getElementById("error-descripcion");
    const errorImagen = document.getElementById("error-imagen");
    const errorGlobal = document.getElementById("error-global");


    // ============================
    // BLOQUEO FECHA Y HORA PASADAS
    // ============================
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, "0");
    const dd = String(hoy.getDate()).padStart(2, "0");

    const fechaMinima = `${yyyy}-${mm}-${dd}`;
    fecha.setAttribute("min", fechaMinima);

    fecha.addEventListener("change", validarFechaHora);
    hora.addEventListener("change", validarFechaHora);

    function validarFechaHora() {

        if (!fecha.value || !hora.value) return;

        const ahora = new Date();
        const fechaEvento = new Date(`${fecha.value}T${hora.value}`);

        if (fechaEvento <= ahora) {
            Swal.fire({
                icon: "error",
                title: "Fecha u hora no válida",
                text: "No puedes crear un evento en una fecha pasada ni en una hora anterior a la actual."
            });

            hora.value = "";
        }
    }


    // ============================
    // CARGAR EVENTO PARA EDITAR
    // ============================
    async function cargarGalaEditar() {

        const params = new URLSearchParams(window.location.search);
        const id = params.get("id");
        if (!id) return;

        try {

            const res = await fetch("../php/gala-obtener.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: id })
            });

            const gala = await res.json();

            if (!gala || !gala.id) {
                errorGlobal.textContent = "No se encontró el evento.";
                return;
            }

            titulo.value = gala.titulo;
            fecha.value = gala.fecha;
            hora.value = gala.hora;
            lugar.value = gala.lugar;
            descripcion.value = gala.descripcion ?? "";

            form.dataset.id = id;

        } catch (err) {
            console.error(err);
            errorGlobal.textContent = "Error al cargar el evento.";
        }
    }

    cargarGalaEditar();


    // ============================
    // SUBMIT (CREAR O EDITAR)
    // ============================
    form.addEventListener("submit", async (e) => {

        e.preventDefault();

        let valido = true;

        [
            errorTitulo,
            errorFecha,
            errorHora,
            errorLugar,
            errorDescripcion,
            errorImagen,
            errorGlobal
        ].forEach(el => el.textContent = "");

        const esNuevo = window.location.pathname.includes("gala-nueva");

        if (titulo.value.trim() === "") {
            errorTitulo.textContent = "El título es obligatorio.";
            valido = false;
        }

        if (fecha.value.trim() === "") {
            errorFecha.textContent = "La fecha es obligatoria.";
            valido = false;
        }

        if (hora.value.trim() === "") {
            errorHora.textContent = "La hora es obligatoria.";
            valido = false;
        }

        if (lugar.value.trim() === "") {
            errorLugar.textContent = "El lugar es obligatorio.";
            valido = false;
        }

        if (descripcion.value.length > 600) {
            errorDescripcion.textContent = "Máximo 600 caracteres.";
            valido = false;
        }

        if (esNuevo && (!imagen.files || imagen.files.length === 0)) {
            errorImagen.textContent = "Debes subir una imagen.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario.";
            return;
        }

        const datos = new FormData();
        datos.append("titulo", titulo.value);
        datos.append("fecha", fecha.value);
        datos.append("hora", hora.value);
        datos.append("lugar", lugar.value);
        datos.append("descripcion", descripcion.value);

        if (imagen.files.length > 0) {
            datos.append("imagen", imagen.files[0]);
        }

        const id = form.dataset.id;

        try {

            const respuesta = await fetch(
                id ? "../php/gala-editar.php" : "../php/gala-nueva.php",
                {
                    method: "POST",
                    body: (() => {
                        if (id) datos.append("id", id);
                        return datos;
                    })()
                }
            );

            const resultado = await respuesta.json();

            if (resultado.ok) {

                await Swal.fire({
                    icon: "success",
                    title: id ? "Evento actualizado" : "Gala creada",
                    text: id
                        ? "La gala se ha actualizado correctamente"
                        : "La gala se ha creado correctamente"
                });

                window.location.href = "gala.html";

            } else {
                errorGlobal.textContent = resultado.msg;
            }

        } catch (err) {
            console.error(err);
            errorGlobal.textContent = "Error al guardar la gala.";
        }
    });

});
