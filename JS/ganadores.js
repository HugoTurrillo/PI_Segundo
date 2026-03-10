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
                <div class="panel-card ganador-card" data-id-candidatura="${g.id_candidatura}" style="cursor:pointer;">
                    <h3>${g.categoria}</h3>
                    <p><strong>Premio:</strong> ${g.numero_premio}º</p>
                    <p><strong>Ganador:</strong> ${g.titulo_obra}</p>
                    <p><strong>Contacto:</strong> ${g.nombre_contacto}</p>
                    <p style="font-size:0.9rem; color:#666; margin-top:0.5rem;">Clic en la tarjeta para ver el vídeo</p>
                    <a href="ganador_asignar.html?id_ganador=${g.id_ganador}"
                       class="btn login-btn"
                       style="margin-top:1rem;">
                       Editar ganador
                    </a>
                </div>
            `;
        });

        document.querySelectorAll(".ganador-card").forEach(card => {
            card.addEventListener("click", async (e) => {
                if (e.target.tagName === "A" || e.target.closest("a")) return;
                const id = card.dataset.idCandidatura;
                if (!id) return;
                const resDetalle = await fetch(`../php/candidatura-detalle.php?id=${id}`);
                const data = await resDetalle.json();
                if (!data.ok) {
                    Swal.fire("Error", data.msg || "Error al cargar", "error");
                    return;
                }
                const c = data.candidatura;
                Swal.fire({
                    title: c.titulo_obra,
                    width: "900px",
                    html: `
                        <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
                        <p><strong>Email:</strong> ${c.email_contacto}</p>
                        <p><strong>Perfil:</strong> ${c.rol_participante || "—"}</p>
                        <video controls poster="../${c.portada_ruta}" style="width:100%; max-height:400px;">
                            <source src="../${c.video_ruta}" type="video/mp4">
                        </video>
                        <p><strong>Sinopsis:</strong></p>
                        <p>${c.sinopsis}</p>
                    `,
                    confirmButtonText: "Cerrar"
                });
            });
        });

    } catch (e) {
        console.error(e);
        contenedor.innerHTML = "<p>Error inesperado</p>";
    }
}
