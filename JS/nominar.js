document.addEventListener("DOMContentLoaded", async () => {

    const params = new URLSearchParams(window.location.search);
    const id_candidatura = params.get("id_candidatura");

    const select = document.getElementById("id_categoria");
    const infoBox = document.getElementById("info-candidatura");

    // ============================
    // 1. Cargar datos de la candidatura
    // ============================
    try {
        const resC = await fetch(`../php/candidatura-obtener.php?id=${id_candidatura}`);
        const jsonC = await resC.json();

        if (!jsonC.ok) {
            infoBox.textContent = "Error cargando la candidatura";
        } else {
            const c = jsonC.data;

            infoBox.innerHTML = `
                <h3>${c.titulo_obra}</h3>
                <p><strong>Participante:</strong> ${c.nombre_contacto}</p>
                <p><strong>Email:</strong> ${c.email_contacto}</p>
            `;
        }
    } catch (err) {
        console.error("Error cargando candidatura:", err);
        infoBox.textContent = "Error cargando la candidatura";
    }

    // ============================
    // 2. Cargar categorías
    // ============================
    const res = await fetch("../php/categorias-listar.php");
    const json = await res.json();

    const categorias = json.data;

    if (!Array.isArray(categorias)) {
        console.error("El backend no devolvió un array:", json);
        return;
    }

    categorias.forEach(c => {
        select.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
    });

    // ============================
    // 3. Nominar
    // ============================
    document.getElementById("form-nominacion").addEventListener("submit", async e => {
        e.preventDefault();

        const id_categoria = select.value;

        await fetch("../php/candidatura-nominar.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_candidatura, id_categoria })
        });

        alert("Candidatura nominada correctamente");
        window.location.href = "ganadores.html";
    });

});
