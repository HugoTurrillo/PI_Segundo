document.addEventListener("DOMContentLoaded", async () => {

    const contenedor = document.getElementById("contenedorPremios");
    if (!contenedor) return;

    try {
        const res = await fetch("../php/categorias-listar.php");
        const resultado = await res.json();

        if (!resultado.ok || resultado.data.length === 0) {
            contenedor.innerHTML = "<p>No hay categorías disponibles</p>";
            return;
        }

        const categorias = resultado.data;

        /* ===============================
           AGRUPAR EN BLOQUES DE 3
        ================================ */
        const grupos = [];
        for (let i = 0; i < categorias.length; i += 3) {
            grupos.push(categorias.slice(i, i + 3));
        }

        /* ===============================
           HTML CARRUSEL
        ================================ */
        contenedor.innerHTML = `
            <div class="premios-carousel">
                <div class="premios-track"></div>
                <div class="premios-dots"></div>
            </div>
        `;

        const track = contenedor.querySelector(".premios-track");
        const dots = contenedor.querySelector(".premios-dots");

        grupos.forEach((grupo, index) => {

            const slide = document.createElement("div");
            slide.className = "premios-slide";

            grupo.forEach(cat => {
                slide.innerHTML += `
                    <div class="premio-card">
                        <h3>${cat.nombre}</h3>
                        <p><strong>Premios:</strong> ${cat.premios}</p>
                        <p><strong>Premio físico:</strong> ${cat.premio_fisico}</p>
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
        const slides = document.querySelectorAll(".premios-slide");
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

    } catch (error) {
        console.error(error);
        contenedor.innerHTML = "<p>Error cargando categorías</p>";
    }
});
