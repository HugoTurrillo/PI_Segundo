document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR NOTICIAS
    // ============================
    async function cargarNoticias() {
        const contenedor = document.getElementById("lista-noticias");
        if (!contenedor) return;

        const res = await fetch("../php/noticias-listar.php");
        const noticias = await res.json();

        contenedor.innerHTML = "";

        noticias.forEach(n => {
            contenedor.innerHTML += `
                <div class="panel-card">
                    <h3>${n.titulo}</h3>
                    <p>${n.contenido}</p>

                    <div style="margin-top:1rem; display:flex; gap:1rem;">
                        <a href="noticia-editar.html?id_noticia=${n.id_noticia}"
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
                    cancelButtonText: "Cancelar"
                });

                if (!conf.isConfirmed) return;

                const res = await fetch("../php/noticia-eliminar.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id_noticia: id })
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
    // FORMULARIO
    // ============================
    const form = document.getElementById("form-noticia");
    if (!form) return;

    const titulo = document.getElementById("titulo");
    const contenido = document.getElementById("contenido");
    const errorGlobal = document.getElementById("error-global");

    async function cargarEditar() {
        const id = new URLSearchParams(window.location.search).get("id_noticia");
        if (!id) return;

        const res = await fetch("../php/noticia-obtener.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_noticia: id })
        });

        const r = await res.json();
        if (!r.ok) {
            errorGlobal.textContent = r.msg;
            return;
        }

        titulo.value = r.noticia.titulo;
        contenido.value = r.noticia.contenido;
        form.dataset.id = id;
    }

    cargarEditar();

    form.addEventListener("submit", async e => {
        e.preventDefault();

        const id = form.dataset.id;
        const url = id ? "noticia-editar.php" : "noticia-nueva.php";

        const res = await fetch(`../php/${url}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id_noticia: id,
                titulo: titulo.value,
                contenido: contenido.value
            })
        });

        const r = await res.json();

        if (r.ok) {
            await Swal.fire("OK", r.msg, "success");
            window.location.href = "noticias.html";
        } else {
            errorGlobal.textContent = r.msg;
        }
    });

});
