/**
 * Gestión de ganadores por podio: categoría, tabla de premios, asignar / cambiar / quitar con confirmación.
 */

let categoriaActual = null;

document.addEventListener("DOMContentLoaded", async () => {
  await cargarCategoriasPodio();

  document.getElementById("select-categoria-podio").addEventListener("change", async (e) => {
    const id = e.target.value;
    if (!id) {
      categoriaActual = null;
      document.getElementById("podio-info-categoria").textContent =
        "Selecciona una categoría para ver el podio.";
      document.getElementById("podio-container").innerHTML = "";
      return;
    }
    categoriaActual = parseInt(id, 10);
    await cargarPodio(categoriaActual);
  });
});

async function cargarCategoriasPodio() {
  const res = await fetch("../php/categorias-listar.php");
  const json = await res.json();
  const select = document.getElementById("select-categoria-podio");
  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));

  select.innerHTML = "<option value=''>Selecciona una categoría</option>";
  json.data.forEach(cat => {
    select.innerHTML += `<option value="${cat.id}">${esc(cat.nombre)}</option>`;
  });
}

async function cargarPodio(idCategoria) {
  const contenedor = document.getElementById("podio-container");
  const info = document.getElementById("podio-info-categoria");

  contenedor.innerHTML = "<p>Cargando podio…</p>";

  try {
    const res = await fetch("../php/ganadores-podio.php?id_categoria=" + idCategoria);
    const json = await res.json();

    if (!json.ok) {
      contenedor.innerHTML = `<p>${json.error || "Error al cargar"}</p>`;
      return;
    }

    const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
    const cat = json.categoria;

    info.innerHTML = `
      <strong>${esc(cat.nombre)}</strong> · ${esc(cat.premios)} premio(s)
      ${cat.premio_fisico ? " · Premio físico: " + esc(cat.premio_fisico) : ""}
    `;

    let filas = "";
    json.podio.forEach(slot => {
      const premio = slot.numero_premio;
      if (slot.ocupado) {
        filas += `
          <tr>
            <td class="podio-col-premio"><strong>${premio}º</strong></td>
            <td class="podio-col-obra">
              <button type="button" class="podio-link-video" data-id-candidatura="${slot.id_candidatura}">
                ${esc(slot.titulo_obra)}
              </button>
              <span class="podio-contacto">— ${esc(slot.nombre_contacto)}</span>
            </td>
            <td class="podio-col-acciones">
              <button type="button" class="btn login-btn btn-podio"
                data-accion="cambiar"
                data-premio="${premio}"
                data-id-ganador="${slot.id_ganador}"
                data-id-candidatura="${slot.id_candidatura}">
                Cambiar
              </button>
              <button type="button" class="btn login-btn btn-podio btn-podio-quitar"
                data-accion="quitar"
                data-premio="${premio}"
                data-id-ganador="${slot.id_ganador}"
                data-titulo="${esc(slot.titulo_obra)}">
                Quitar
              </button>
            </td>
          </tr>
        `;
      } else {
        filas += `
          <tr class="podio-fila-vacia">
            <td class="podio-col-premio"><strong>${premio}º</strong></td>
            <td class="podio-col-obra"><em class="podio-vacio">Vacío</em></td>
            <td class="podio-col-acciones">
              <button type="button" class="btn login-btn btn-podio"
                data-accion="asignar"
                data-premio="${premio}">
                Asignar
              </button>
            </td>
          </tr>
        `;
      }
    });

    contenedor.innerHTML = `
      <div class="podio-table-wrap">
        <table class="podio-table responsive-table">
          <thead>
            <tr>
              <th>Premio</th>
              <th>Ganador</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>${filas}</tbody>
        </table>
      </div>
    `;

    contenedor.querySelectorAll("[data-accion]").forEach(btn => {
      btn.addEventListener("click", () => {
        const accion = btn.dataset.accion;
        if (accion === "quitar") {
          quitarGanador(btn);
        } else {
          abrirModalCandidato({
            accion,
            numeroPremio: parseInt(btn.dataset.premio, 10),
            idGanador: btn.dataset.idGanador ? parseInt(btn.dataset.idGanador, 10) : 0,
            idCandidaturaActual: btn.dataset.idCandidatura
              ? parseInt(btn.dataset.idCandidatura, 10)
              : null,
          });
        }
      });
    });

    contenedor.querySelectorAll(".podio-link-video").forEach(btn => {
      btn.addEventListener("click", () => mostrarVideo(btn.dataset.idCandidatura));
    });
  } catch (e) {
    console.error(e);
    contenedor.innerHTML = "<p>Error inesperado al cargar el podio.</p>";
  }
}

async function mostrarVideo(idCandidatura) {
  const res = await fetch(`../php/candidatura-detalle.php?id=${idCandidatura}`);
  const data = await res.json();
  if (!data.ok) {
    Swal.fire("Error", data.msg || "Error al cargar", "error");
    return;
  }
  const c = data.candidatura;
  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
  Swal.fire({
    title: esc(c.titulo_obra),
    width: "900px",
    html: `
      <p><strong>Autor:</strong> ${esc(c.nombre_contacto)}</p>
      <p><strong>Email:</strong> ${esc(c.email_contacto)}</p>
      <p><strong>Perfil:</strong> ${esc(c.rol_participante || "—")}</p>
      <video controls poster="../${esc(c.portada_ruta)}" style="width:100%; max-height:400px;">
        <source src="../${esc(c.video_ruta)}" type="video/mp4">
      </video>
      <p><strong>Sinopsis:</strong></p>
      <p>${esc(c.sinopsis)}</p>
    `,
    confirmButtonText: "Cerrar",
  });
}

async function abrirModalCandidato({ accion, numeroPremio, idGanador, idCandidaturaActual }) {
  if (!categoriaActual) return;

  const res = await fetch(
    "../php/nominados-por-categoria.php?id_categoria=" + categoriaActual
  );
  const json = await res.json();
  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));

  if (!json.ok || json.data.length === 0) {
    Swal.fire("Sin nominados", "No hay candidaturas aceptadas para esta categoría.", "info");
    return;
  }

  let options = "";
  json.data.forEach(n => {
    const sinCat = Number(n.sin_categoria) === 1;
    const premioTxt =
      n.numero_premio && Number(n.numero_premio) !== numeroPremio
        ? ` (actualmente ${n.numero_premio}º premio)`
        : n.numero_premio && Number(n.numero_premio) === numeroPremio
          ? " (actual)"
          : "";
    const extra = sinCat ? " — sin categoría, se asignará aquí" : "";
    const selected =
      idCandidaturaActual && Number(n.id_candidatura) === Number(idCandidaturaActual)
        ? " selected"
        : "";
    options += `<option value="${n.id_candidatura}"${selected}>${esc(n.titulo_obra)} — ${esc(n.nombre_contacto)}${premioTxt}${extra}</option>`;
  });

  const titulo =
    accion === "asignar"
      ? `Asignar ${numeroPremio}º premio`
      : `Cambiar ${numeroPremio}º premio`;

  const { value: idCandidatura } = await Swal.fire({
    title: titulo,
    html: `
      <p style="text-align:left; margin-bottom:0.75rem; color:#555; font-size:0.95rem;">
        Elige la candidatura ganadora. Si ya tiene otro premio, se intercambiarán las posiciones.
      </p>
      <select id="swal-candidatura" class="swal2-input" style="width:100%; max-width:100%; box-sizing:border-box;">
        ${options}
      </select>
    `,
    showCancelButton: true,
    confirmButtonText: "Guardar",
    cancelButtonText: "Cancelar",
    focusConfirm: false,
    preConfirm: () => {
      const sel = document.getElementById("swal-candidatura");
      if (!sel || !sel.value) {
        Swal.showValidationMessage("Selecciona una candidatura");
        return false;
      }
      return parseInt(sel.value, 10);
    },
  });

  if (!idCandidatura) return;

  await guardarConConfirmacion({
    idCategoria: categoriaActual,
    numeroPremio,
    idCandidatura,
    idGanador,
  });
}

async function guardarConConfirmacion({ idCategoria, numeroPremio, idCandidatura, idGanador }) {
  const fdPreview = new FormData();
  fdPreview.append("id_categoria", idCategoria);
  fdPreview.append("numero_premio", numeroPremio);
  fdPreview.append("id_candidatura", idCandidatura);
  if (idGanador) fdPreview.append("id_ganador", idGanador);

  const resPreview = await fetch("../php/ganador-preview.php", {
    method: "POST",
    body: fdPreview,
  });
  const preview = await resPreview.json();

  if (!preview.ok) {
    Swal.fire("Error", preview.error, "error");
    return;
  }

  if (preview.sin_cambios) {
    Swal.fire("Sin cambios", "La candidatura seleccionada ya ocupa ese premio.", "info");
    return;
  }

  if (preview.requiere_confirmacion) {
    const htmlLineas = (preview.lineas || [])
      .map(l => `<li style="text-align:left; margin:0.35rem 0;">${l}</li>`)
      .join("");
    const confirm = await Swal.fire({
      title: "¿Confirmar cambio?",
      html: `<ul style="padding-left:1.2rem; margin:0;">${htmlLineas}</ul>`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí, confirmar",
      cancelButtonText: "Cancelar",
    });
    if (!confirm.isConfirmed) return;
  }

  const fd = new FormData();
  fd.append("id_categoria", idCategoria);
  fd.append("numero_premio", numeroPremio);
  fd.append("id_candidatura", idCandidatura);
  if (idGanador) fd.append("id_ganador", idGanador);

  const res = await fetch("../php/ganador-guardar.php", { method: "POST", body: fd });
  const json = await res.json();

  if (!json.ok) {
    Swal.fire("Error", json.error, "error");
    return;
  }

  Swal.fire("Correcto", json.msg, "success");
  await cargarPodio(idCategoria);
}

async function quitarGanador(btn) {
  const idGanador = parseInt(btn.dataset.idGanador, 10);
  const premio = btn.dataset.premio;
  const titulo = btn.dataset.titulo || "este ganador";

  const confirm = await Swal.fire({
    title: "¿Quitar del podio?",
    html: `<p style="text-align:left;">Se quitará <strong>${titulo}</strong> del <strong>${premio}º premio</strong>. El puesto quedará vacío.</p>`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, quitar",
    cancelButtonText: "Cancelar",
  });

  if (!confirm.isConfirmed) return;

  const fd = new FormData();
  fd.append("id_ganador", idGanador);

  const res = await fetch("../php/ganador-quitar.php", { method: "POST", body: fd });
  const json = await res.json();

  if (!json.ok) {
    Swal.fire("Error", json.error, "error");
    return;
  }

  Swal.fire("Hecho", json.msg, "success");
  if (categoriaActual) await cargarPodio(categoriaActual);
}
