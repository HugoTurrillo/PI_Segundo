document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR PATROCINADORES
    // ============================
    async function cargarPatrocinadores() {

        const contenedor = document.querySelector(".panel-grid");
        if (!contenedor) return;

        const respuesta = await fetch("../php/patrocinadores-listar.php");
        const patrocinadores = await respuesta.json();

        contenedor.innerHTML = "";

        patrocinadores.forEach(p => {
            contenedor.innerHTML += `
                <div class="panel-card">
                   
                    <img src="../php/uploads/${p.logo_ruta}"
                         alt="Logo patrocinador" 
                         style="width: 100%; max-height: 120px; object-fit: contain; margin-bottom: 1rem;">

                    <h3>${p.nombre}</h3>

                    <p><strong>Web:</strong> 
                        <a href="${p.url_web}" target="_blank">${p.url_web}</a>
                    </p>

                    <p>${p.descripcion}</p>

                    <div style="margin-top: 1rem; display:flex; gap:1rem;">
                        <a href="patrocinador-editar.php?id=${p.id_patrocinador}" 
                           class="btn login-btn" 
                           style="padding:0.5rem 1rem;">Editar</a>

                        <button class="btn login-btn btn-eliminar-patrocinador" 
                                data-id="${p.id_patrocinador}" 
                                style="padding:0.5rem 1rem; background:#555;">
                            Eliminar
                        </button>
                    </div>
                </div>
            `;
        });

        activarBotonesEliminar();
    }

    cargarPatrocinadores();



    // ============================
    // ELIMINAR PATROCINADOR
    // ============================
    function activarBotonesEliminar() {
        document.querySelectorAll(".btn-eliminar-patrocinador").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                const confirmacion = await Swal.fire({
                    title: "¿Eliminar patrocinador?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                });

                if (!confirmacion.isConfirmed) return;

                const res = await fetch(`../php/patrocinador-eliminar.php?id=${id}`);
                const r = await res.json();

                Swal.fire({
                    icon: r.ok ? "success" : "error",
                    title: r.ok ? "Eliminado" : "Error",
                    text: r.msg
                });

                if (r.ok) cargarPatrocinadores();
            });
        });
    }



    // ============================
    // FORMULARIO (CREAR / EDITAR)
    // ============================
    const form = document.getElementById("form-patrocinador");

    if (!form) return;

    const nombre = document.getElementById("nombre");
    const logo = document.getElementById("logo");
    const enlace = document.getElementById("enlace");
    const descripcion = document.getElementById("descripcion");

    const errorNombre = document.getElementById("error-nombre");
    const errorLogo = document.getElementById("error-logo");
    const errorEnlace = document.getElementById("error-enlace");
    const errorDescripcion = document.getElementById("error-descripcion");
    const errorGlobal = document.getElementById("error-global");



    // ============================
    // CARGAR PATROCINADOR PARA EDITAR
    // ============================
    async function cargarPatrocinadorEditar() {
        const params = new URLSearchParams(window.location.search);
        const id = params.get("id");

        if (!id) return;

        const res = await fetch(`../php/patrocinador-obtener.php?id=${id}`);
        const patrocinador = await res.json();

        if (!patrocinador || !patrocinador.id_patrocinador) {
            errorGlobal.textContent = "No se encontró el patrocinador.";
            return;
        }

        nombre.value = patrocinador.nombre;
        enlace.value = patrocinador.url_web;
        descripcion.value = patrocinador.descripcion;

        form.dataset.id = id;
    }

    cargarPatrocinadorEditar();



    // ============================
    // SUBMIT (CREAR O EDITAR)
    // ============================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        let valido = true;

        errorNombre.textContent = "";
        errorLogo.textContent = "";
        errorEnlace.textContent = "";
        errorDescripcion.textContent = "";
        errorGlobal.textContent = "";

        const esNuevo = !form.dataset.id;

        if (nombre.value.trim() === "") {
            errorNombre.textContent = "El nombre es obligatorio.";
            valido = false;
        }

        if (esNuevo && (!logo.files || logo.files.length === 0)) {
            errorLogo.textContent = "Debes subir un logo.";
            valido = false;
        }

        if (enlace.value.trim() === "") {
            errorEnlace.textContent = "El enlace es obligatorio.";
            valido = false;
        } else if (!enlace.value.startsWith("http://") && !enlace.value.startsWith("https://")) {
            errorEnlace.textContent = "El enlace debe comenzar por http:// o https://";
            valido = false;
        }

        if (descripcion.value.length > 500) {
            errorDescripcion.textContent = "La descripción no puede superar los 500 caracteres.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario.";
            return;
        }

        const datos = new FormData();
        datos.append("nombre", nombre.value);
        datos.append("enlace", enlace.value);
        datos.append("descripcion", descripcion.value);

        if (logo.files.length > 0) {
            datos.append("logo", logo.files[0]);
        }

        const id = form.dataset.id;

        if (id) {
            datos.append("id", id);

            const respuesta = await fetch("../php/patrocinador-editar.php", {
                method: "POST",
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.ok) {
                await Swal.fire({
                    icon: "success",
                    title: "Patrocinador actualizado",
                    text: "Los datos se han guardado correctamente"
                });

                window.location.href = "patrocinadores.php";
            } else {
                errorGlobal.textContent = resultado.msg;
            }

            return;
        }

        const respuesta = await fetch("../php/patrocinador-nuevo.php", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (resultado.ok) {
            await Swal.fire({
                icon: "success",
                title: "Patrocinador creado",
                text: "El patrocinador se ha registrado correctamente"
            });

            window.location.href = "patrocinadores.php";
        } else {
            errorGlobal.textContent = resultado.msg;
        }
    });

});
