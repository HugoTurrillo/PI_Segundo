/**
 * Cargo los patrocinadores, muestro formularios de alta/edición y gestiono subida de logo y eliminación.
 */

document.addEventListener("DOMContentLoaded", () => {

    async function cargarPatrocinadores() {
        const contenedor = document.querySelector(".panel-grid");
        if (!contenedor) return;

        const respuesta = await fetch("../php/patrocinadores-listar.php");
        const patrocinadores = await respuesta.json();

        contenedor.innerHTML = "";

        patrocinadores.forEach(p => {
            contenedor.innerHTML += `
                <div class="panel-card patrocinador-card">

                    <img class="patrocinador-logo"
                         src="../php/uploads/${p.logo_ruta}"
                         alt="${p.nombre}">

                    <h3>${p.nombre}</h3>

                    <p><strong>Web:</strong>
                        <a href="${p.url_web}" target="_blank">${p.url_web}</a>
                    </p>

                    <p>${p.descripcion || ""}</p>

                    <div class="patrocinador-acciones">
                        <a href="patrocinador-editar.html?id=${p.id_patrocinador}"
                            class="btn-accion btn-editar">Editar</a>

                        <button class="btn-accion btn-eliminar btn-eliminar-patrocinador"
                                data-id="${p.id_patrocinador}">
                            Eliminar
                        </button>
                    </div>
                </div>
            `;
        });

        activarEliminar();
    }

    cargarPatrocinadores();

    /* ======================================================
       ELIMINAR
    ====================================================== */
    function activarEliminar() {
        document.querySelectorAll(".btn-eliminar-patrocinador").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                const conf = await Swal.fire({
                    title: "¿Eliminar patrocinador?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                });

                if (!conf.isConfirmed) return;

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

    /* ======================================================
       FORMULARIO CREAR / EDITAR
    ====================================================== */
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

    /* ============================
       CARGAR PARA EDITAR
    ============================ */
    async function cargarEditar() {
        const id = new URLSearchParams(window.location.search).get("id");
        if (!id) return;

        const res = await fetch(`../php/patrocinador-obtener.php?id=${id}`);
        const p = await res.json();

        if (!p || !p.id_patrocinador) {
            errorGlobal.textContent = "Patrocinador no encontrado";
            return;
        }

        nombre.value = p.nombre;
        enlace.value = p.url_web;
        descripcion.value = p.descripcion || "";
        form.dataset.id = id;
    }

    cargarEditar();

    /* ============================
       SUBMIT
    ============================ */
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Limpiar errores
        errorNombre.textContent = "";
        errorLogo.textContent = "";
        errorEnlace.textContent = "";
        errorDescripcion.textContent = "";
        errorGlobal.textContent = "";

        let valido = true;
        const esNuevo = !form.dataset.id;

        if (!nombre.value.trim()) {
            errorNombre.textContent = "El nombre es obligatorio";
            valido = false;
        }

        if (esNuevo && (!logo.files || logo.files.length === 0)) {
            errorLogo.textContent = "Debes subir un logo";
            valido = false;
        }

        if (!enlace.value.startsWith("http://") && !enlace.value.startsWith("https://")) {
            errorEnlace.textContent = "El enlace debe empezar por http:// o https://";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Revisa los errores del formulario";
            return;
        }

        const datos = new FormData();
        datos.append("nombre", nombre.value);
        datos.append("enlace", enlace.value);
        datos.append("descripcion", descripcion.value);

        if (logo.files.length > 0) {
            datos.append("logo", logo.files[0]);
        }

        // EDITAR
        if (form.dataset.id) {
            datos.append("id", form.dataset.id);

            const res = await fetch("../php/patrocinador-editar.php", {
                method: "POST",
                body: datos
            });

            const r = await res.json();

            if (r.ok) {
                await Swal.fire("OK", "Patrocinador actualizado", "success");
                window.location.href = "patrocinadores.html";
            } else {
                errorGlobal.textContent = r.msg;
            }
            return;
        }

        // CREAR
        let res = await fetch("../php/patrocinador-nuevo.php", {
            method: "POST",
            body: datos
        });

        let r = await res.json();

        if (r.confirmar) {
            const conf = await Swal.fire({
                title: "Patrocinador duplicado",
                text: r.msg,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, crear",
                cancelButtonText: "Cancelar"
            });

            if (!conf.isConfirmed) return;

            res = await fetch("../php/patrocinador-nuevo.php?forzar=1", {
                method: "POST",
                body: datos
            });

            await Swal.fire("Creado", "Patrocinador creado correctamente", "success");
            window.location.href = "patrocinadores.html";
            return;
        }

        if (r.ok) {
            await Swal.fire("Creado", "Patrocinador creado correctamente", "success");
            window.location.href = "patrocinadores.html";
        } else {
            errorGlobal.textContent = r.msg;
        }
    });

});
