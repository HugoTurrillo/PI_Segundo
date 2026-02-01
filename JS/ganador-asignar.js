document.addEventListener("DOMContentLoaded", () => {
  cargarCategorias();

  document.getElementById("select_categoria").addEventListener("change", () => {
    const id_categoria = document.getElementById("select_categoria").value;
    if (id_categoria) {
      cargarCategoria(id_categoria);
      cargarPremios(id_categoria);
      cargarNominados(id_categoria);
    }
  });

  document.getElementById("form-ganador").addEventListener("submit", guardarGanador);
});

async function cargarCategorias() {
  const res = await fetch("../php/categorias-listar.php");
  const data = await res.json();

  const select = document.getElementById("select_categoria");
  select.innerHTML = "<option value=''>Selecciona una categoría</option>";

  data.data.forEach(cat => {
    select.innerHTML += `<option value="${cat.id}">${cat.nombre}</option>`;
  });
}

async function cargarCategoria(id) {
  const res = await fetch("../php/categoria-obtener.php?id=" + id);
  const data = await res.json();

  document.getElementById("info-categoria").innerHTML = `
    <strong>Categoría:</strong> ${data.data.nombre}<br>
    <strong>Premios disponibles:</strong> ${data.data.premios}
  `;
}

async function cargarPremios(id_categoria) {
  const res = await fetch("../php/categoria-obtener.php?id=" + id_categoria);
  const data = await res.json();

  const select = document.getElementById("numero_premio");
  select.innerHTML = "";

  for (let i = 1; i <= data.data.premios; i++) {
    select.innerHTML += `<option value="${i}">${i}º Premio</option>`;
  }
}

async function cargarNominados(id_categoria) {
  const res = await fetch("../php/nominados-por-categoria.php?id_categoria=" + id_categoria);
  const data = await res.json();

  const select = document.getElementById("id_candidatura");
  select.innerHTML = "";

  if (data.data.length === 0) {
    select.innerHTML = "<option>No hay nominados</option>";
    return;
  }

  data.data.forEach(n => {
    select.innerHTML += `
      <option value="${n.id_candidatura}">
        ${n.titulo_obra} — ${n.nombre_contacto}
      </option>
    `;
  });
}

async function guardarGanador(e) {
  e.preventDefault();

  const id_categoria = document.getElementById("select_categoria").value;

  if (!id_categoria) {
    Swal.fire("Error", "Selecciona una categoría", "error");
    return;
  }

  const formData = new FormData();
  formData.append("id_categoria", id_categoria);
  formData.append("numero_premio", document.getElementById("numero_premio").value);
  formData.append("id_candidatura", document.getElementById("id_candidatura").value);

  const res = await fetch("../php/ganador-guardar.php", {
    method: "POST",
    body: formData
  });

  const data = await res.json();

  if (!data.ok) {
    Swal.fire("Error", data.error, "error");
    return;
  }

  await Swal.fire("Correcto", "Ganador asignado correctamente", "success");
  window.location.href = "ganadores.php";
}
