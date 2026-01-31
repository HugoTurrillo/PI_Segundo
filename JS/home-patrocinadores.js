document.addEventListener("DOMContentLoaded", () => {
    const contenedor = document.getElementById("patrocinadores-home");

    fetch("../php/patrocinadores-listar.php")
        .then(r => r.json())
        .then(lista => {
            contenedor.innerHTML = "";

            lista.forEach(p => {
                contenedor.innerHTML += `
                    <a href="${p.url_web}" target="_blank" class="patro-item">
                        <img src="php/uploads/${p.logo_ruta}" alt="${p.nombre}">
                    </a>
                `;
            });
        })
        .catch(err => {
            console.error("Error cargando patrocinadores:", err);
            contenedor.innerHTML = "<p>Error al cargar patrocinadores</p>";
        });
});