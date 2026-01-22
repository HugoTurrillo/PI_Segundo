document.addEventListener("DOMContentLoaded", () => {

    const contenedor = document.getElementById("candidaturas-container");
    if (!contenedor) return;

    async function cargarCandidaturas() {
        const res = await fetch("../php/candidaturas-listar.php");
        const lista = await res.json();

        contenedor.innerHTML = "";

        lista.forEach(c => {
            contenedor.innerHTML += `
                <div class="panel-card">
                    <h3>${c.titulo_obra}</h3>
                    <p><strong>Autor:</strong> ${c.nombre_contacto}</p>
                    <p><strong>Email:</strong> ${c.email_contacto}</p>
                    <p><strong>Estado:</strong> ${c.estado}</p>

                    <div style="margin-top:1rem; display:flex; gap:1rem; flex-wrap:wrap;">
                        ${c.estado === "en_proceso" ? `
                            <button class="btn login-btn btn-aceptar"
                                    data-id="${c.id_candidatura}">
                                Aceptar
                            </button>
                            <button class="btn login-btn btn-rechazar"
                                    data-id="${c.id_candidatura}"
                                    style="background:#444;">
                                Rechazar
                            </button>
                        ` : ""}

                        ${c.estado === "aceptada" ? `
                            <a href="nominar-categoria.html?id_candidatura=${c.id_candidatura}"
                               class="btn login-btn" style="background:#1a73e8;">
                                Nominar a categoría
                            </a>
                        ` : ""}

                        ${c.estado === "rechazada" ? `
                            <p><strong>Motivo rechazo:</strong>
                               ${c.motivo_rechazo || "No indicado"}
                            </p>
                        ` : ""}
                    </div>
                </div>
            `;
        });

        activarBotones();
    }

    function activarBotones() {

        document.querySelectorAll(".btn-aceptar").forEach(btn => {
            btn.addEventListener("click", async () => {
                await fetch("../php/candidatura-aceptar.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id_candidatura: btn.dataset.id
                    })
                });
                cargarCandidaturas();
            });
        });

        document.querySelectorAll(".btn-rechazar").forEach(btn => {
            btn.addEventListener("click", async () => {
                const motivo = prompt("Indica el motivo del rechazo:");
                if (!motivo) return;

                await fetch("../php/candidatura-rechazar.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id: btn.dataset.id,
                        motivo
                    })
                });
                cargarCandidaturas();
            });
        });
    }

    cargarCandidaturas();
});
