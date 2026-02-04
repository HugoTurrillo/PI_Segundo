document.addEventListener("DOMContentLoaded", () => {
    cargarGanadores();
});

async function cargarGanadores() {
    const contenedor = document.getElementById("ganadores-container");
    if (!contenedor) return;

    try {
        const res = await fetch("../php/ganadores-listar.php");
        const json = await res.json();

        if (!json.ok) {
            contenedor.innerHTML = "<p>Error al cargar ganadores</p>";
            return;
        }

        contenedor.innerHTML = "";

        if (json.data.length === 0) {
            contenedor.innerHTML = "<p>No hay ganadores asignados.</p>";
            return;
        }

        json.data.forEach(g => {
            contenedor.innerHTML += `
                <div class="panel-card">
                    <h3>${g.categoria}</h3>
                    <p><strong>Premio:</strong> ${g.numero_premio}º</p>
                    <p><strong>Ganador:</strong> ${g.titulo_obra}</p>
                    <p><strong>Contacto:</strong> ${g.nombre_contacto}</p>

                    <a href="ganador_asignar.html?id_ganador=${g.id_ganador}"
                       class="btn login-btn"
                       style="margin-top:1rem;">
                       Editar ganador
                    </a>
                </div>
            `;
        });

    } catch (e) {
        console.error(e);
        contenedor.innerHTML = "<p>Error inesperado</p>";
    }
}
