document.addEventListener("DOMContentLoaded", async () => {

  const contenedor = document.getElementById("detalle-container");

  // Obtener ID de la URL
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  if (!id) {
    contenedor.innerHTML = "<p>Error: no se indicó candidatura.</p>";
    return;
  }

  // Cargar datos
  const res = await fetch(`../php/candidatura-detalle.php?id=${id}`);
  const data = await res.json();

  if (!data.ok) {
    contenedor.innerHTML = "<p>Error al cargar la candidatura.</p>";
    return;
  }

  const c = data.candidatura;

  contenedor.innerHTML = `
    <h3>${c.titulo_obra}</h3>

    <p><strong>Ficha técnica:</strong></p>
    <p>${c.ficha_tecnica}</p>

    <p><strong>Sinopsis:</strong></p>
    <p>${c.sinopsis}</p>

    <p><strong>Cartel:</strong></p>
    <img src="${c.cartel}" alt="Cartel" style="max-width:300px; border-radius:8px;">

    <p><strong>Expediente:</strong></p>
    <a href="${c.expediente}" target="_blank" class="btn login-btn">Ver expediente</a>

    <p><strong>Vídeo:</strong></p>
    <video src="${c.video}" controls style="max-width:100%; border-radius:8px;"></video>

    <hr>

    <p><strong>Nombre contacto:</strong> ${c.nombre_contacto}</p>
    <p><strong>Email contacto:</strong> ${c.email_contacto}</p>
    <p><strong>DNI:</strong> ${c.dni}</p>

    <p><strong>Estado actual:</strong> ${c.estado}</p>

    ${c.estado === "rechazada" ? `
      <p style="color:red;"><strong>Motivo rechazo:</strong> ${c.motivo_rechazo}</p>
    ` : ""}

    <div style="margin-top:20px; display:flex; gap:1rem;">
      <button id="btn-aceptar" class="btn login-btn">Aceptar</button>
      <button id="btn-rechazar" class="btn login-btn" style="background:#444;">Rechazar</button>
    </div>
  `;

  // Botón aceptar
  document.getElementById("btn-aceptar").addEventListener("click", async () => {
    await fetch("../php/candidatura-aceptar.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id_candidatura: id })
    });
    alert("Candidatura aceptada");
    window.location.href = "candidaturas.html";
  });

  // Botón rechazar
  document.getElementById("btn-rechazar").addEventListener("click", async () => {
    const motivo = prompt("Indica el motivo del rechazo:");
    if (!motivo) return;

    await fetch("../php/candidatura-rechazar.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id, motivo })
    });

    alert("Candidatura rechazada");
    window.location.href = "candidaturas.html";
  });

});