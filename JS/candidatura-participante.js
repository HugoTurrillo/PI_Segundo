document.addEventListener("DOMContentLoaded", async () => {

    const sinCandidatura = document.getElementById("sinCandidatura");
    const conCandidatura = document.getElementById("conCandidatura");

    const tituloCorto = document.getElementById("tituloCorto");
    const estadoCandidatura = document.getElementById("estadoCandidatura");
    const motivoRechazoBox = document.getElementById("motivoRechazoBox");
    const motivoRechazo = document.getElementById("motivoRechazo");
    const categoria = document.getElementById("categoria");
    const descripcion = document.getElementById("descripcion");
    const subsanacionBox = document.getElementById("subsanacionBox");
    const candidaturaIdInput = document.getElementById("candidaturaIdInput");

    try {
        const res = await fetch("../php/obtener_candidatura.php");
        const data = await res.json();

        // NO HAY CANDIDATURA
        if (!data.ok) {
            sinCandidatura.style.display = "block";
            conCandidatura.style.display = "none";
            return;
        }

        // SÍ HAY CANDIDATURA
        const c = data.candidatura;

        sinCandidatura.style.display = "none";
        conCandidatura.style.display = "block";

        // Rellenar datos
        tituloCorto.textContent = c.titulo_obra;
        descripcion.textContent = c.sinopsis;
        categoria.textContent = c.categoria_nombre || "Sin categoría asignada";
        estadoCandidatura.textContent = c.estado;

        if (c.estado) {
            estadoCandidatura.classList.add(c.estado.trim().toLowerCase());
        }

        // Guardar ID para subsanación
        candidaturaIdInput.value = c.id_candidatura;

        // Mostrar motivo de rechazo si existe
        if (c.estado === "rechazada" && c.motivo_rechazo) {
            motivoRechazoBox.style.display = "block";
            motivoRechazo.textContent = c.motivo_rechazo;
            subsanacionBox.style.display = "block";
        } else {
            motivoRechazoBox.style.display = "none";
            subsanacionBox.style.display = "none";
        }

    } catch (error) {
        console.error("Error cargando candidatura:", error);
        sinCandidatura.style.display = "block";
        conCandidatura.style.display = "none";
    }

});