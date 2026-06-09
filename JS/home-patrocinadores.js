/**
 * Cargo los patrocinadores del home y los muestro; escapo nombre, descripción y URL con escapeHtml.
 */

document.addEventListener("DOMContentLoaded", () => {
    const contenedor = document.getElementById("patrocinadores-home");

    fetch("../php/patrocinadores-listar.php")
        .then(r => r.json())
        .then(lista => {
            contenedor.innerHTML = "";

            const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
            lista.forEach(p => {
                contenedor.innerHTML += `
                    <div class="patro-item">
                        <a href="${esc(p.url_web)}" target="_blank" rel="noopener noreferrer">
                            <img src="uploads/${esc(p.logo_ruta)}" alt="${esc(p.nombre)}">
                        </a>
                        <h3>${esc(p.nombre)}</h3>
                        <p>${esc(p.descripcion)}</p>
                    </div>
                `;
            });
        })
        .catch(err => {
            console.error("Error cargando patrocinadores:", err);
            contenedor.innerHTML = "<p>Error al cargar patrocinadores</p>";
        });
});