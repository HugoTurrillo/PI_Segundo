document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR NOTICIAS
    // ============================
    async function cargarNoticias() {
        const contenedor = document.getElementById("lista-noticias");
        if (!contenedor) return; // Solo se ejecuta en noticias.html

        const respuesta = await fetch("../php/noticias-listar.php");
        const noticias = await respuesta.json();

        contenedor.innerHTML = "";

        noticias.forEach(n => {
            contenedor.innerHTML += `
                <div class="panel-card">
                    <h3>${n.titulo}</h3>
                    <p>${n.contenido}</p>

                    <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                        <a href="noticia-editar.html?id_noticia=${n.id_noticia}" class="btn login-btn" style="padding: 0.5rem 1rem;">Editar</a>

                        <button class="btn login-btn btn-eliminar-noticia" 
                                data-id="${n.id_noticia}" 
                                style="padding: 0.5rem 1rem; background-color: #555;">
                            Eliminar
                        </button>
                    </div>
                </div>
            `;
        });

        activarBotonesEliminar();
    }

    cargarNoticias();



    // ============================
    // ELIMINAR NOTICIA
    // ============================
function activarBotonesEliminar() {
    document.querySelectorAll(".btn-eliminar-noticia").forEach(btn => {
        btn.addEventListener("click", async () => {
            const id = btn.dataset.id;

            const confirmacion = await Swal.fire({
                    title: "¿Eliminar noticia?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                });

                if (!confirmacion.isConfirmed) return;

            const res = await fetch(`../php/noticia-eliminar.php?id_noticia=${id}`);
            const r = await res.json();

            Swal.fire({
                    icon: r.ok ? "success" : "error",
                    title: r.ok ? "Eliminado" : "Error",
                    text: r.msg
                });
            if (r.ok) cargarNoticias();
        });
    });
}



    // ============================
    // FORMULARIO (CREAR / EDITAR)
    // ============================
    const form = document.getElementById("form-noticia");
    if (!form) return; // Solo se ejecuta en nueva o editar

    const titulo = document.getElementById("titulo");
    const contenido = document.getElementById("contenido");

    const errorTitulo = document.getElementById("error-titulo");
    const errorContenido = document.getElementById("error-contenido");
    const errorGlobal = document.getElementById("error-global");


// ============================
// CARGAR NOTICIA PARA EDITAR
// ============================
async function cargarNoticiaEditar() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id_noticia");  // <--- CORRECTO

    if (!id) return; 

    const res = await fetch(`../php/noticia-obtener.php?id_noticia=${id}`); // <--- CORRECTO
    const noticia = await res.json();

    if (!noticia || !noticia.id_noticia) {  
        errorGlobal.textContent = "No se encontró la noticia.";
        return;
    }

    // Rellenar formulario
    titulo.value = noticia.titulo;
    contenido.value = noticia.contenido;

    // Guardar ID para el submit
    form.dataset.id = noticia.id_noticia; 
}

cargarNoticiaEditar();




    // ============================
    // SUBMIT (CREAR O EDITAR)
    // ============================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        let valido = true;

        // Reset errores
        errorTitulo.textContent = "";
        errorContenido.textContent = "";
        errorGlobal.textContent = "";

        // VALIDACIONES
        if (titulo.value.trim() === "") {
            errorTitulo.textContent = "El título no puede estar vacío.";
            valido = false;
        }

        if (contenido.value.trim() === "") {
            errorContenido.textContent = "El contenido no puede estar vacío.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
            return;
        }



        // ============================
        // EDITAR NOTICIA
        // ============================
        const id = form.dataset.id;

        if (id) {
           const datos = {
                id_noticia: id,
                titulo: titulo.value,
                contenido: contenido.value
            };


            const respuesta = await fetch("../php/noticia-editar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const resultado = await respuesta.json();

            if (resultado.ok) {
                await Swal.fire({
                    icon: "success",
                    title: "Noticia editada",
                    text: "La noticia se ha actualizado correctamente"
                });

                window.location.href = "noticias.html";
            } else {
                errorGlobal.textContent = resultado.msg;
            }

            return;
        }



        // ============================
        // CREAR NOTICIA
        // ============================
        const datos = {
            titulo: titulo.value,
            contenido: contenido.value
        };

        const respuesta = await fetch("../php/noticia-nueva.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

            if (resultado.ok) {
                await Swal.fire({
                    icon: "success",
                    title: "Noticia editada",
                    text: "La noticia se ha creado correctamente"
                });
            window.location.href = "noticias.html";
        } else {
            errorGlobal.textContent = resultado.msg;
        }
    });

    // ============================
    // BOTÓN "VER NOTICIAS"
    // ============================
    const btnVerNoticias = document.getElementById("btn-ver-noticias");

    if (btnVerNoticias) {
        btnVerNoticias.addEventListener("click", async () => {

            const respuesta = await fetch("../php/noticias-listar.php", {
                method: "GET",
                headers: { "Content-Type": "application/json" }
            });

            const resultado = await respuesta.json();

            const contenedor = document.getElementById("lista-noticias");
            contenedor.innerHTML = "";

            if (Array.isArray(resultado)) {
                resultado.forEach(noticia => {
                    contenedor.innerHTML += `
                        <div class="panel-card">
                            <h3>${noticia.titulo}</h3>
                            <p>${noticia.contenido}</p>

                            <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                                <a href="noticia-editar.html?id_noticia=${noticia.id_noticia}" 
                                   class="btn login-btn" 
                                   style="padding: 0.5rem 1rem;">
                                   Editar
                                </a>

                                <button class="btn login-btn btn-eliminar-noticia" 
                                        data-id="${noticia.id}" 
                                        style="padding: 0.5rem 1rem; background-color: #555;">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    `;
                });

                activarBotonesEliminar();

            } else {
                errorGlobal.textContent = resultado.msg || "Error al cargar noticias.";
            }
        });
    }

});
