document.addEventListener("DOMContentLoaded", () => {

    fetch("../php/obtener_candidatura.php")
        .then(res => res.json())
        .then(data => {

            // Si no hay candidatura → mostrar mensaje
            if (!data.ok || !data.candidatura) {
                document.getElementById("sinCandidatura").style.display = "block";
                return;
            }

            const c = data.candidatura;

            // Mostrar contenedor principal
            document.getElementById("conCandidatura").style.display = "block";

            // Datos básicos
            document.getElementById("tituloCorto").textContent = c.titulo_obra;
            document.getElementById("descripcion").textContent = c.sinopsis;

            // Estado
            const estadoSpan = document.getElementById("estadoCandidatura");
            estadoSpan.textContent = c.estado;

            // Categoría (puede ser null)
            document.getElementById("categoria").textContent =
                c.categoria_nombre ? c.categoria_nombre : "Sin categoría";

            // ============================
            // ESTADO: RECHAZADA
            // ============================
            if (c.estado === "rechazada") {
                document.getElementById("motivoRechazoBox").style.display = "block";
                document.getElementById("motivoRechazo").textContent =
                    c.motivo_rechazo || "No indicado";

                document.getElementById("subsanacionBox").style.display = "block";
                document.getElementById("candidaturaIdInput").value = c.id_candidatura;
            }

            // ============================
            // ESTADO: EN PROCESO (subsanación enviada)
            // ============================
            if (c.estado === "en_proceso") {
                estadoSpan.textContent = "Subsanación enviada. Pendiente de revisión.";
            }

            // ============================
            // ESTADO: ACEPTADA
            // ============================
            if (c.estado === "aceptada") {
                estadoSpan.textContent = "Candidatura aceptada";
            }

        })
        .catch(err => console.error("Error en fetch:", err));
});