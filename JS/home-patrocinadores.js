document.addEventListener("DOMContentLoaded", () => {
    const contenedor = document.getElementById("patrocinadores-home");

    fetch("patrocinadores-listar.php")
        .then(r => r.json())
        .then(lista => {
            contenedor.innerHTML = "";

            lista.forEach(p => {
                contenedor.innerHTML += `
                    <div class="patro-item">
                        <a href="${p.url_web}" target="_blank">
                            <img src="uploads/${p.logo_ruta}" alt="${p.nombre}">
                        </a>
                        <h3>${p.nombre}</h3>
                        <p>${p.descripcion}</p>
                    </div>
                `;
            });
        })
        .catch(err => {
            console.error("Error cargando patrocinadores:", err);
            contenedor.innerHTML = "<p>Error al cargar patrocinadores</p>";
        });
});