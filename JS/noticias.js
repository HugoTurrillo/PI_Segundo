document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR NOTICIAS (PANEL ORGANIZADOR)
    // ============================
    async function cargarNoticias() {
        const contenedor = document.getElementById("lista-noticias");
        if (!contenedor) return;

        const res = await fetch("../php/noticias-listar.php");
        const noticias = await res.json();

        contenedor.innerHTML = "";

        noticias.forEach(n => {
            contenedor.innerHTML += `
                <div class="panel-card noticia-card">

                    <img class="noticia-img-admin" 
                         src="../php/uploads_noticias/${n.imagen_ruta}" 
                         alt="${n.titulo}">

                    <h3>${n.titulo}</h3>
                    <p>${n.contenido}</p>

                    <div style="margin-top:1rem; display:flex; gap:1rem;">
                        <a href="noticia-editar.php?id_noticia=${n.id_noticia}"
                           class="btn login-btn">
                           Editar
                        </a>

                        <button class="btn login-btn btn-eliminar-noticia"
                                data-id="${n.id_noticia}"
                                style="background:#555;">
                            Eliminar
                        </button>
                    </div>
                </div>
            `;
        });

        activarEliminar();
    }

    cargarNoticias();

    // ============================
    // ELIMINAR NOTICIA
    // ============================
    function activarEliminar() {
        document.querySelectorAll(".btn-eliminar-noticia").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                const conf = await Swal.fire({
                    title: "¿Eliminar noticia?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    confirmButtonColor: "#FF3228",
                    cancelButtonText: "Cancelar",
                    cancelButtonColor: "#000000"
                });

                if (!conf.isConfirmed) return;

                const fd = new FormData();
                fd.append("id_noticia", id);

                const res = await fetch("../php/noticia-eliminar.php", {
                    method: "POST",
                    body: fd
                });

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
    if (!form) return;

    const titulo = document.getElementById("titulo");
    const contenido = document.getElementById("contenido");
    const imagen = document.getElementById("imagen");
    const errorGlobal = document.getElementById("error-global");

    async function cargarEditar() {
        const id = new URLSearchParams(window.location.search).get("id_noticia");
        if (!id) return;

        const fd = new FormData();
        fd.append("id_noticia", id);

        const res = await fetch("../php/noticia-obtener.php", {
            method: "POST",
            body: fd
        });

        const r = await res.json();
        if (!r.ok) {
            errorGlobal.textContent = r.msg;
            return;
        }

        titulo.value = r.noticia.titulo;
        contenido.value = r.noticia.contenido;
        form.dataset.id = id;

        // -----------------------------
        // MOSTRAR IMAGEN ACTUAL
        // -----------------------------
        const imgActual = document.getElementById("imagen-actual");

        if (r.noticia.imagen_ruta) {
            imgActual.src = "../php/uploads_noticias/" + r.noticia.imagen_ruta;
            imgActual.style.display = "block";
        } else {
            imgActual.style.display = "none";
        }
    }

    cargarEditar();

    form.addEventListener("submit", async e => {
        e.preventDefault();

        const id = form.dataset.id;
        const url = id ? "noticia-editar.php" : "noticia-nueva.php";

        const formData = new FormData();
        formData.append("titulo", titulo.value);
        formData.append("contenido", contenido.value);

        if (imagen && imagen.files.length > 0) {
            formData.append("imagen", imagen.files[0]);
        }

        if (id) {
            formData.append("id_noticia", id);
        }

        const res = await fetch(`../php/${url}`, {
            method: "POST",
            body: formData
        });

        const r = await res.json();

        if (r.ok) {
            await Swal.fire("OK", r.msg, "success");
            window.location.href = "noticias.php";
        } else {
            errorGlobal.textContent = r.msg;
        }
    });

});