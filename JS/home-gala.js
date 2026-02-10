document.addEventListener("DOMContentLoaded", async () => {
  const cont = document.getElementById("home-gala-card");
  if (!cont) return;

  try {
    const res = await fetch("../php/gala-listar.php");
    const data = await res.json();

    if (!data.ok) {
      cont.innerHTML = "<p>La gala se anunciará próximamente.</p>";
      return;
    }

    const g = data.data;

    cont.innerHTML = `
      <h3>${g.titulo}</h3>
      <p><strong>Fecha:</strong> ${g.fecha}</p>
      <p><strong>Lugar:</strong> ${g.lugar}</p>
      <p>${g.descripcion ?? ""}</p>

      <div style="margin-top:1.2rem;">
        <a href="../HTML/gala-publica.html" class="btn login-btn">

          Ver detalles de la gala
        </a>
      </div>
    `;

  } catch (e) {
    cont.innerHTML = "<p>Error al cargar la gala.</p>";
  }
});
