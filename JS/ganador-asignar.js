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

  if (idGanador) {
    await cargarGanadorEditar(idGanador);
  }
});

/* ======================================================
   CARGAR TODAS LAS CATEGORÍAS
====================================================== */
async function cargarCategorias() {
  try {
    const res = await fetch("../php/categorias-listar.php");
    const json = await res.json();

    const select = document.getElementById("select_categoria");
    select.innerHTML = "<option value=''>Selecciona una categoría</option>";

    if (!json.ok) {
      Swal.fire("Error", "No se pudieron cargar categorías", "error");
      return;
    }

    json.data.forEach(cat => {
      select.innerHTML += `
        <option value="${cat.id}">
          ${cat.nombre}
        </option>
      `;
    });

  } catch (e) {
    console.error(e);
    Swal.fire("Error", "Error cargando categorías", "error");
  }
}

/* ======================================================
   INFO DE CATEGORÍA
====================================================== */
async function cargarCategoria(id) {
  try {
    const res = await fetch("../php/categoria-obtener.php?id=" + id);
    const json = await res.json();

    if (!json.ok) return;

    document.getElementById("info-categoria").innerHTML = `
      <strong>Categoría:</strong> ${json.data.nombre}<br>
      <strong>Premios:</strong> ${json.data.premios}
    `;

  } catch (e) {
    console.error(e);
  }
}

/* ======================================================
   PREMIOS SEGÚN CATEGORÍA
====================================================== */
async function cargarPremios(idCategoria) {
  try {
    const res = await fetch("../php/categoria-obtener.php?id=" + idCategoria);
    const json = await res.json();

    const select = document.getElementById("numero_premio");
    select.innerHTML = "";

    for (let i = 1; i <= json.data.premios; i++) {
      select.innerHTML += `<option value="${i}">${i}º Premio</option>`;
    }

  } catch (e) {
    console.error(e);
  }
}

/* ======================================================
   NOMINADOS POR CATEGORÍA
====================================================== */
async function cargarNominados(idCategoria) {
  try {
    const res = await fetch("../php/nominados-por-categoria.php?id_categoria=" + idCategoria);
    const json = await res.json();

    const select = document.getElementById("id_candidatura");
    select.innerHTML = "";

    if (!json.ok || json.data.length === 0) {
      select.innerHTML = "<option>No hay nominados</option>";
      return;
    }

    json.data.forEach(n => {
      select.innerHTML += `
        <option value="${n.id_candidatura}">
          ${n.titulo_obra} — ${n.nombre_contacto}
        </option>
      `;
    });

  } catch (e) {
    console.error(e);
  }
}

/* ======================================================
   CARGAR GANADOR PARA EDITAR
====================================================== */
async function cargarGanadorEditar(id) {
  try {
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

  } catch (e) {
    console.error(e);
  }
}

/* ======================================================
   GUARDAR (CREAR O EDITAR)
====================================================== */
async function guardarGanador(e, idGanador) {
  e.preventDefault();

  const fd = new FormData();
  fd.append("id_categoria", document.getElementById("select_categoria").value);
  fd.append("numero_premio", document.getElementById("numero_premio").value);
  fd.append("id_candidatura", document.getElementById("id_candidatura").value);

  if (idGanador) {
    fd.append("id_ganador", idGanador);
  }

  try {
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

  } catch (e) {
    console.error(e);
    Swal.fire("Error", "Error de conexión", "error");
  }
}
