document.addEventListener("DOMContentLoaded", async () => {

  const params = new URLSearchParams(window.location.search);
  const idGanador = params.get("id_ganador");

  const tituloForm = document.getElementById("titulo-form");
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
  });

  document
    .getElementById("form-ganador")
    .addEventListener("submit", e => guardarGanador(e, idGanador));

  /*  BOTÓN SALIR SIN GUARDAR */
  const btnCancelar = document.getElementById("btn-cancelar");
  if (btnCancelar) {
    btnCancelar.addEventListener("click", () => {
      window.location.href = "ganadores.html";
    });
  }

  if (idGanador) {
    await cargarGanadorEditar(idGanador);
  }
});


async function cargarCategorias() {
  const res = await fetch("../php/categorias-listar.php");
  const json = await res.json();

  const select = document.getElementById("select_categoria");
  select.innerHTML = "<option value=''>Selecciona una categoría</option>";

  json.data.forEach(cat => {
    select.innerHTML += `<option value="${cat.id}">${cat.nombre}</option>`;
  });
}

async function cargarCategoria(id) {
  const res = await fetch("../php/categoria-obtener.php?id=" + id);
  const json = await res.json();

  if (!json.ok) return;

  document.getElementById("info-categoria").innerHTML = `
    <strong>Categoría:</strong> ${json.data.nombre}<br>
    <strong>Premios:</strong> ${json.data.premios}
  `;
}

async function cargarPremios(idCategoria) {
  const res = await fetch("../php/categoria-obtener.php?id=" + idCategoria);
  const json = await res.json();

  const select = document.getElementById("numero_premio");
  select.innerHTML = "";

  for (let i = 1; i <= json.data.premios; i++) {
    select.innerHTML += `<option value="${i}">${i}º Premio</option>`;
  }
}

async function cargarNominados(idCategoria) {
  const res = await fetch("../php/nominados-por-categoria.php?id_categoria=" + idCategoria);
  const json = await res.json();

  const select = document.getElementById("id_candidatura");
  select.innerHTML = "";

  if (!json.ok || json.data.length === 0) {
    select.innerHTML = "<option>No hay nominados</option>";
    return;
  }

  json.data.forEach(n => {
    const sinCategoria = Number(n.sin_categoria) === 1;
    const etiqueta = sinCategoria
      ? `${n.titulo_obra} — ${n.nombre_contacto} (sin categoría, se asignará a esta)`
      : `${n.titulo_obra} — ${n.nombre_contacto}`;
    select.innerHTML += `
      <option value="${n.id_candidatura}">${etiqueta}</option>
    `;
  });
}

async function cargarGanadorEditar(id) {
  const res = await fetch("../php/ganador-obtener.php?id=" + id);
  const json = await res.json();

  if (!json.ok) {
    Swal.fire("Error", json.error, "error");
    return;
  }

  const g = json.data;

  document.getElementById("select_categoria").value = g.id_categoria;
  await cargarCategoria(g.id_categoria);
  await cargarPremios(g.id_categoria);
  await cargarNominados(g.id_categoria);

  document.getElementById("numero_premio").value = g.numero_premio;
  document.getElementById("id_candidatura").value = g.id_candidatura;
}

async function guardarGanador(e, idGanador) {
  e.preventDefault();

  const fd = new FormData();
  fd.append("id_categoria", document.getElementById("select_categoria").value);
  fd.append("numero_premio", document.getElementById("numero_premio").value);
  fd.append("id_candidatura", document.getElementById("id_candidatura").value);

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
