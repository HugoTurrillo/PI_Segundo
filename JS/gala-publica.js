document.addEventListener("DOMContentLoaded", async () => {
  const cont = document.getElementById("gala-detalle");

  try {
    // Intentar cargar POST-EVENTO PUBLICADO
    const resPost = await fetch("../php/postevento-publico.php");
    const post = await resPost.json();

    if (post.ok) {
      const p = post.data;

      cont.innerHTML = `
        <h3>Resumen de la gala</h3>
        <p>${p.resumen}</p>

        <h4>Ganadores</h4>
        <ul>
          <li><strong>Alumnos:</strong> ${p.ganador_alumnos} – ${p.corto_alumnos}</li>
          <li><strong>Alumni:</strong> ${p.ganador_alumni} – ${p.corto_alumni}</li>
        </ul>

        <p><strong>Edición:</strong> ${p.anio_edicion}</p>
        <p><strong>Participantes:</strong> ${p.numero_participantes}</p>
      `;
      return;
    }

    //  Si NO hay post-evento  mostrar gala normal
    const resGala = await fetch("../php/gala-listar.php");
    const gala = await resGala.json();

    if (!gala.ok) {
      cont.innerHTML = "<p>La gala se anunciará próximamente.</p>";
      return;
    }

    const g = gala.data;

    cont.innerHTML = `
      <h3>${g.titulo}</h3>
      <p><strong>Fecha:</strong> ${g.fecha}</p>
      <p><strong>Lugar:</strong> ${g.lugar}</p>
      <p>${g.descripcion ?? ""}</p>
    `;

  } catch (e) {
    cont.innerHTML = "<p>Error al cargar la gala.</p>";
  }
});
