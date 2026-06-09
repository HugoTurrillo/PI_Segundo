/**
 * Cargo el postevento publicado o, si no hay, los datos de la gala para la página pública de la gala.
 */

document.addEventListener("DOMContentLoaded", async () => {
  const cont = document.getElementById("gala-detalle");
  if (!cont) return;

  try {
    const resPost = await fetch("../php/postevento-publico.php");
    const post = await resPost.json();

    if (post.ok) {
      const p = post.data;

      cont.innerHTML = `
        <h3>Resumen de la gala</h3>
        <p>${p.resumen}</p>

        <h4>Ganadores</h4>
        <ul>
          ${p.ganador_alumnos ? `<li><strong>Alumnos:</strong> ${p.ganador_alumnos} – ${p.corto_alumnos}</li>` : ""}
          ${p.ganador_alumni ? `<li><strong>Alumni:</strong> ${p.ganador_alumni} – ${p.corto_alumni}</li>` : ""}
          ${p.ganador_profesional ? `<li><strong>Profesionales:</strong> ${p.ganador_profesional} – ${p.corto_profesional}</li>` : ""}
        </ul>

        <p><strong>Edición:</strong> ${p.anio_edicion}</p>
        <p><strong>Participantes:</strong> ${p.numero_participantes}</p>
      `;
      return; //  ya se muestra el post-evento
    }

    // ============================
    // SI NO HAY POST-EVENTO → GALA NORMAL
    // ============================
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
    console.error(e);
    cont.innerHTML = "<p>Error al cargar la información de la gala.</p>";
  }
});
