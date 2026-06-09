/**
 * Gestiono el formulario para asignar o editar un ganador: cargo categorías, premios y nominados; al editar precargo todos los datos.
 */

document.addEventListener("DOMContentLoaded", async () => {

  const params = new URLSearchParams(window.location.search);
  const idGanador = params.get("id_ganador");

  const tituloForm = document.getElementById("titulo-form");
  const infoGanador = document.getElementById("info-ganador-actual");

  if (idGanador && tituloForm) {
    tituloForm.textContent = "Editar ganador";
  }

  await cargarCategorias();

  document.getElementById("select_categoria").addEventListener("change", async () => {
    const id = document.getElementById("select_categoria").value;
    if (!id) return;

    await cargarCategoria(id);
    await cargarPremios(id);
    await cargarNominados(id);
    if (infoGanador) infoGanador.hidden = true;
  });

  document
    .getElementById("form-ganador")
    .addEventListener("submit", e => guardarGanador(e, idGanador));

  const btnCancelar = document.getElementById("btn-cancelar");
  if (btnCancelar) {
    btnCancelar.addEventListener("click", () => {
      window.location.href = "ganadores.html";
    });
  }

  if (idGanador) {
    await cargarGanadorEditar(idGanador);
  } else if (infoGanador) {
    infoGanador.hidden = true;
  }
});


function seleccionarValor(selectEl, valor) {
  if (!selectEl || valor == null || valor === "") return false;
  const v = String(valor);
  for (const opt of selectEl.options) {
    if (opt.value === v) {
      selectEl.value = v;
      return true;
    }
  }
  return false;
}

function mostrarResumenGanador(g) {
  const info = document.getElementById("info-ganador-actual");
  if (!info || !g) return;

  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
  info.hidden = false;
  info.innerHTML = `
    <p style="margin:0 0 0.5rem; color:#666; font-size:0.9rem;">Datos actuales del ganador</p>
    <p style="margin:0.25rem 0;"><strong>Categoría:</strong> ${esc(g.categoria)}</p>
    <p style="margin:0.25rem 0;"><strong>Premio:</strong> ${esc(g.numero_premio)}º</p>
    <p style="margin:0.25rem 0;"><strong>Obra:</strong> ${esc(g.titulo_obra)}</p>
    <p style="margin:0.25rem 0;"><strong>Contacto:</strong> ${esc(g.nombre_contacto)}</p>
  `;
}

async function cargarCategorias() {
  const res = await fetch("../php/categorias-listar.php");
  const json = await res.json();

  const select = document.getElementById("select_categoria");
  select.innerHTML = "<option value=''>Selecciona una categoría</option>";

  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
  json.data.forEach(cat => {
    select.innerHTML += `<option value="${cat.id}">${esc(cat.nombre)}</option>`;
  });
}

async function cargarCategoria(id) {
  const res = await fetch("../php/categoria-obtener.php?id=" + id);
  const json = await res.json();

  if (!json.ok) return;

  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
  document.getElementById("info-categoria").innerHTML = `
    <strong>Categoría:</strong> ${esc(json.data.nombre)}<br>
    <strong>Premios:</strong> ${esc(json.data.premios)}
  `;
}

async function cargarPremios(idCategoria, premioSeleccionado = null) {
  const res = await fetch("../php/categoria-obtener.php?id=" + idCategoria);
  const json = await res.json();

  const select = document.getElementById("numero_premio");
  select.innerHTML = "";

  for (let i = 1; i <= json.data.premios; i++) {
    select.innerHTML += `<option value="${i}">${i}º Premio</option>`;
  }

  if (premioSeleccionado != null) {
    seleccionarValor(select, premioSeleccionado);
  }
}

async function cargarNominados(idCategoria, candidaturaSeleccionada = null, datosCandidatura = null) {
  const res = await fetch("../php/nominados-por-categoria.php?id_categoria=" + idCategoria);
  const json = await res.json();

  const select = document.getElementById("id_candidatura");
  select.innerHTML = "";

  if (!json.ok || json.data.length === 0) {
    if (candidaturaSeleccionada && datosCandidatura) {
      const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
      const etiqueta = `${esc(datosCandidatura.titulo_obra)} — ${esc(datosCandidatura.nombre_contacto)}`;
      select.innerHTML = `<option value="${candidaturaSeleccionada}">${etiqueta}</option>`;
      seleccionarValor(select, candidaturaSeleccionada);
    } else {
      select.innerHTML = "<option value=''>No hay nominados</option>";
    }
    return;
  }

  const esc = window.escapeHtml || (s => (s == null ? "" : String(s)));
  json.data.forEach(n => {
    const sinCategoria = Number(n.sin_categoria) === 1;
    const etiqueta = sinCategoria
      ? `${esc(n.titulo_obra)} — ${esc(n.nombre_contacto)} (sin categoría, se asignará a esta)`
      : `${esc(n.titulo_obra)} — ${esc(n.nombre_contacto)}`;
    select.innerHTML += `
      <option value="${n.id_candidatura}">${etiqueta}</option>
    `;
  });

  if (candidaturaSeleccionada != null) {
    if (!seleccionarValor(select, candidaturaSeleccionada) && datosCandidatura) {
      const etiqueta = `${esc(datosCandidatura.titulo_obra)} — ${esc(datosCandidatura.nombre_contacto)}`;
      select.innerHTML += `
        <option value="${candidaturaSeleccionada}">${etiqueta}</option>
      `;
      seleccionarValor(select, candidaturaSeleccionada);
    }
  }
}

async function cargarGanadorEditar(id) {
  const res = await fetch("../php/ganador-obtener.php?id=" + id);
  const json = await res.json();

  if (!json.ok) {
    Swal.fire("Error", json.error, "error");
    return;
  }

  const g = json.data;

  mostrarResumenGanador(g);

  seleccionarValor(document.getElementById("select_categoria"), g.id_categoria);

  await cargarCategoria(g.id_categoria);
  await cargarPremios(g.id_categoria, g.numero_premio);
  await cargarNominados(g.id_categoria, g.id_candidatura, {
    titulo_obra: g.titulo_obra,
    nombre_contacto: g.nombre_contacto
  });

  seleccionarValor(document.getElementById("numero_premio"), g.numero_premio);
  seleccionarValor(document.getElementById("id_candidatura"), g.id_candidatura);
}

async function guardarGanador(e, idGanador) {
  e.preventDefault();

  const idCategoria = document.getElementById("select_categoria").value;
  const numeroPremio = document.getElementById("numero_premio").value;
  const idCandidatura = document.getElementById("id_candidatura").value;

  if (!idCategoria) {
    Swal.fire("Error", "Selecciona una categoría", "error");
    return;
  }
  if (!numeroPremio) {
    Swal.fire("Error", "Selecciona un premio", "error");
    return;
  }
  if (!idCandidatura || !/^\d+$/.test(String(idCandidatura))) {
    Swal.fire("Error", "Selecciona un nominado válido", "error");
    return;
  }

  const fd = new FormData();
  fd.append("id_categoria", idCategoria);
  fd.append("numero_premio", numeroPremio);
  fd.append("id_candidatura", idCandidatura);

  if (idGanador) {
    fd.append("id_ganador", idGanador);
  }

  const res = await fetch("../php/ganador-guardar.php", {
    method: "POST",
    body: fd
  });

  const json = await res.json();

  if (!json.ok) {
    Swal.fire("Error", json.error, "error");
    return;
  }

  Swal.fire("Correcto", json.msg, "success").then(() => {
    window.location.href = "ganadores.html";
  });
}
