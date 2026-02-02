document.addEventListener("DOMContentLoaded", async () => {
    const contenedor = document.getElementById("contenedorNoticias");

    try {
        const response = await fetch("noticias-listar.php");
        const noticias = await response.json();

        if (!Array.isArray(noticias)) {
            contenedor.innerHTML = "<p>Error al cargar noticias</p>";
            return;
        }

        noticias.forEach(noticia => {
            const div = document.createElement("div");
            div.classList.add("noticia");

            div.innerHTML = `
                <img class="noticia-img" src="uploads_noticias/${noticia.imagen_ruta}" alt="${noticia.titulo}">
                <h3>${noticia.titulo}</h3>
                <p class="fecha">${formatearFecha(noticia.fecha_publicacion)}</p>
                <p class="contenido">${noticia.contenido}</p>
                <hr>
            `;

            contenedor.appendChild(div);
        });

    } catch (error) {
        contenedor.innerHTML = "<p>Error de conexión con el servidor</p>";
    }
});

function formatearFecha(fecha) {
    const f = new Date(fecha);
    return f.toLocaleDateString("es-ES", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
}