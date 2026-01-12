document.addEventListener("DOMContentLoaded", () => {

    const contenedor = document.getElementById("candidaturas-container");

    if (!contenedor) {
        console.error("❌ ERROR: No existe el contenedor #candidaturas-container en el HTML");
        return;
    }

    async function cargarCandidaturas() {
        try {
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
                                <button class="btn login-btn btn-aceptar" data-id="${c.id_candidatura}">
                                    Aceptar
                                </button>
                                <button class="btn login-btn btn-rechazar" data-id="${c.id_candidatura}" style="background:#444;">
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
                                <p><strong>Motivo rechazo:</strong> ${c.motivo_rechazo || "No indicado"}</p>
                            ` : ""}
                        </div>
                    </div>
                `;
            });

            activarBotones();

        } catch (error) {
            console.error("❌ Error cargando candidaturas:", error);
            contenedor.innerHTML = "<p>Error cargando candidaturas</p>";
        }
    }

    // ===============================
    // INSERTAR NUEVA CANDIDATURA
    // ===============================
    const formInsertar = document.getElementById("form-nueva-candidatura");

    if (formInsertar) {
        formInsertar.addEventListener("submit", async (e) => {
            e.preventDefault();

            const error = document.getElementById("error-insertar");

            const datos = {
                titulo_obra: document.getElementById("titulo_obra").value,
                nombre_contacto: document.getElementById("nombre_contacto").value,
                email_contacto: document.getElementById("email_contacto").value,
                dni: document.getElementById("dni").value,
                sinopsis: document.getElementById("sinopsis").value
            };

            try {
                const res = await fetch("../php/candidatura-insertar.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(datos)
                });

                const r = await res.json();

                if (!r.ok) {
                    error.textContent = "Error al insertar candidatura";
                    return;
                }

                formInsertar.reset();
                error.textContent = "";

                cargarCandidaturas();

            } catch (error) {
                console.error("❌ Error insertando candidatura:", error);
                error.textContent = "Error de conexión";
            }
        });
    }

    cargarCandidaturas();

    function activarBotones() {

        // ACEPTAR
        document.querySelectorAll(".btn-aceptar").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                await fetch(`../php/candidatura-aceptar.php?id=${id}`);
                cargarCandidaturas();
            });
        });

        // RECHAZAR
        document.querySelectorAll(".btn-rechazar").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;
                const motivo = prompt("Indica el motivo del rechazo:");

                if (!motivo) return;

                await fetch("../php/candidatura-rechazar.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id, motivo })
                });

                cargarCandidaturas();
            });
        });
    }

});
