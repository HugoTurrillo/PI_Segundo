/**
 * Cargo las noticias del home en un carrusel, agrupadas de 3 en 3, y al clic muestro el popup con el detalle; escapo títulos e imagen.
 */

document.addEventListener("DOMContentLoaded", async () => {

    const contenedor = document.getElementById("contenedorNoticias");

    try {
        const res = await fetch("../php/noticias-listar.php");
        const noticias = await res.json();

        if (!Array.isArray(noticias) || noticias.length === 0) {
            contenedor.innerHTML = "<p>No hay noticias disponibles</p>";
            return;
        }

        /* ===============================
           AGRUPAR EN BLOQUES DE 3
        ================================ */
        const grupos = [];
        for (let i = 0; i < noticias.length; i += 3) {
            grupos.push(noticias.slice(i, i + 3));
        }

        /* ===============================
           HTML CARRUSEL
        ================================ */
        contenedor.innerHTML = `
            <div class="news-carousel">
                <div class="news-track"></div>
                <div class="news-dots"></div>
            </div>
        `;

        const track = contenedor.querySelector(".news-track");
        const dots = contenedor.querySelector(".news-dots");

        grupos.forEach((grupo, index) => {

            const slide = document.createElement("div");
            slide.className = "news-slide";

            const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
            grupo.forEach(n => {
                slide.innerHTML += `
                    <div class="news-card" data-id="${n.id_noticia}">
                        <img src="uploads_noticias/${n.imagen_ruta}" alt="${esc(n.titulo)}">
                        <h3>${esc(n.titulo)}</h3>
                    </div>
                `;
            });

            track.appendChild(slide);

            const dot = document.createElement("span");
            dot.className = index === 0 ? "dot active" : "dot";
            dot.dataset.index = index;
            dots.appendChild(dot);
        });

        let current = 0;
        const slides = document.querySelectorAll(".news-slide");
        const allDots = document.querySelectorAll(".dot");

        function mostrarSlide(i) {
            track.style.transform = `translateX(-${i * 100}%)`;
            allDots.forEach(d => d.classList.remove("active"));
            allDots[i].classList.add("active");
            current = i;
        }

        allDots.forEach(d => {
            d.addEventListener("click", () => {
                mostrarSlide(+d.dataset.index);
            });
        });

        /* ===============================
           AUTO PLAY
        ================================ */
        setInterval(() => {
            current = (current + 1) % slides.length;
            mostrarSlide(current);
        }, 5000);

        /* ===============================
           POPUP NOTICIA
        ================================ */
        document.querySelectorAll(".news-card").forEach(card => {
            card.addEventListener("click", async () => {

                const fd = new FormData();
                fd.append("id_noticia", card.dataset.id);

                const r = await fetch("../php/noticia-obtener.php", {
                    method: "POST",
                    body: fd
                });

                const data = await r.json();
                if (!data.ok) return;

                const n = data.noticia;

                Swal.fire({
                    title: n.titulo,
                    imageUrl: "uploads_noticias/" + n.imagen_ruta,
                    imageWidth: 600,
                    html: `<p>${n.contenido}</p>`,
                    showCloseButton: true,
                    confirmButtonText: "Cerrar"
                });
            });
        });

    } catch {
        contenedor.innerHTML = "<p>Error cargando noticias</p>";
    }
});
