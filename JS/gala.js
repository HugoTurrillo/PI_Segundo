document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR EVENTOS DE GALA
    // ============================
    async function cargarGala() {
        const contenedor = document.querySelector(".panel-grid");
        if (!contenedor) return; // Solo en gala.html

        // RUTA CORRECTA DESDE /HTML/
        const respuesta = await fetch("../php/gala-listar.php");
        const eventos = await respuesta.json();

        contenedor.innerHTML = "";

        eventos.forEach(ev => {
            contenedor.innerHTML += `
                <div class="panel-card">

                    <img src="../uploads/${ev.imagen}" 
                         alt="Imagen gala" 
                         style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 6px; margin-bottom: 1rem;">

                    <h3>${ev.titulo}</h3>

                    <p><strong>Fecha:</strong> ${ev.fecha}</p>
                    <p><strong>Hora:</strong> ${ev.hora}</p>
                    <p><strong>Lugar:</strong> ${ev.lugar}</p>
                    <p>${ev.descripcion}</p>

                    <div style="margin-top: 1rem; display:flex; gap:1rem;">
                        <a href="gala-editar.html?id=${ev.id}" 
                           class="btn login-btn" 
                           style="padding:0.5rem 1rem;">Editar</a>

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

                const res = await fetch(`../php/gala-eliminar.php?id=${id}`);
                const r = await res.json();

                Swal.fire({
                    icon: r.ok ? "success" : "error",
                    title: r.ok ? "Eliminado" : "Error",
                    text: r.msg
                });
                if (r.ok) cargarGala();
            });
        });
    }



    // ============================
    // FORMULARIO (CREAR / EDITAR)
    // ============================
    const form = document.getElementById("form-gala");

    // Solo ejecutamos esta parte si estamos en gala-nueva o gala-editar
    if (form) {

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
        // CARGAR EVENTO PARA EDITAR
        // ============================
        async function cargarGalaEditar() {
            const params = new URLSearchParams(window.location.search);
            const id = params.get("id");

            if (!id) return;

            const res = await fetch(`../php/gala-obtener.php?id=${id}`);
            const gala = await res.json();

            if (!gala || !gala.id) {
                errorGlobal.textContent = "No se encontró el evento.";
                return;
            }

            titulo.value = gala.titulo;
            fecha.value = gala.fecha;
            hora.value = gala.hora;
            lugar.value = gala.lugar;
            descripcion.value = gala.descripcion;

            form.dataset.id = id;
        }

        cargarGalaEditar();



        // ============================
        // SUBMIT (CREAR O EDITAR)
        // ============================
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            let valido = true;

            errorTitulo.textContent = "";
            errorFecha.textContent = "";
            errorHora.textContent = "";
            errorLugar.textContent = "";
            errorDescripcion.textContent = "";
            errorImagen.textContent = "";
            errorGlobal.textContent = "";

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
                errorDescripcion.textContent = "La descripción no puede superar los 600 caracteres.";
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



            // ============================
            // FORM DATA (para imagen)
            // ============================
            const datos = new FormData();
            datos.append("titulo", titulo.value);
            datos.append("fecha", fecha.value);
            datos.append("hora", hora.value);
            datos.append("lugar", lugar.value);
            datos.append("descripcion", descripcion.value);

            if (imagen.files.length > 0) {
                datos.append("imagen", imagen.files[0]);
            }



            // ============================
            // EDITAR
            // ============================
            const id = form.dataset.id;

            if (id) {
                datos.append("id", id);

                const respuesta = await fetch("../php/gala-editar.php", {
                    method: "POST",
                    body: datos
                });

                const resultado = await respuesta.json();

                if (resultado.ok) {
                    await Swal.fire({
                        icon: "success",
                        title: "Evento actualizado",
                        text: "La gala se ha actualizado correctamente"
                    });

                    window.location.href = "gala.html";
                } else {
                    errorGlobal.textContent = resultado.msg;
                }

                return;
            }



            // ============================
            // CREAR
            // ============================
            const respuesta = await fetch("../php/gala-nueva.php", {
                method: "POST",
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.ok) {
                await Swal.fire({
                    icon: "success",
                    title: "Gala creada",
                    text: "La gala se ha creado correctamente"
                });

                window.location.href = "gala.html";
            } else {
                errorGlobal.textContent = resultado.msg;
            }
        });
    }

});
