document.addEventListener("DOMContentLoaded", () => {
    cargarGanadores();
});

/* ======================================================
   LISTAR GANADORES
====================================================== */
async function cargarGanadores() {
    const contenedor = document.getElementById("ganadores-container");
    if (!contenedor) return;

    try {
        const respuesta = await fetch("../php/ganadores-listar.php");
        const resultado = await respuesta.json();

        if (!resultado.ok) {
            contenedor.innerHTML = "<p>Error al cargar ganadores</p>";
            return;
        }

        const ganadores = resultado.data;
        contenedor.innerHTML = "";

        
        console.log("Ganadores recibidos:", ganadores);

        if (ganadores.length === 0) {
            contenedor.innerHTML = "<p>No hay ganadores asignados.</p>";
            return;
        }

        ganadores.forEach(g => {

            //DEBUG 2: ver si entra al bucle
            console.log("Pintando ganador:", g);

            contenedor.innerHTML += `
                <div class="panel-card">
                    <h3>${g.categoria}</h3>
                    <p><strong>Premio:</strong> ${g.numero_premio}</p>
                    <p><strong>Ganador:</strong> ${g.titulo_obra}</p>
                    <p><strong>Contacto:</strong> ${g.nombre_contacto}</p>

                    <div style="margin-top:1rem; display:flex; gap:1rem;">
                        <a href="ganador_asignar.html?id=${g.id_categoria}"
                           class="btn login-btn"
                           style="padding:0.5rem 1rem;">
                           Reasignar ganador
                        </a>
                    </div>
                </div>
            `;
        });

    } catch (error) {
        console.error(error);
        contenedor.innerHTML = "<p>Error inesperado</p>";
    }
}
