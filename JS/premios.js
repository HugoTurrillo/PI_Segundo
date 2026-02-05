document.addEventListener("DOMContentLoaded", () => {

    /* ======================================================
       LISTAR CATEGORÍAS
    ====================================================== */
    async function cargarCategorias() {
        const contenedor = document.getElementById("categorias-container");
        if (!contenedor) return;

        try {
            const respuesta = await fetch("../php/categorias-listar.php");
            const resultado = await respuesta.json();

            if (!resultado.ok) {
                contenedor.innerHTML = "<p>Error al cargar categorías</p>";
                return;
            }

            const categorias = resultado.data;
            contenedor.innerHTML = "";

            if (categorias.length === 0) {
                contenedor.innerHTML = "<p>No hay categorías creadas.</p>";
                return;
            }

            categorias.forEach(cat => {
                contenedor.innerHTML += `
                    <div class="panel-card categoria-card">
                        <h3>${cat.nombre}</h3>
                        <p>Premios: ${cat.premios}</p>
                        <p>Premio físico: ${cat.premio_fisico}</p>

                        <div class="categoria-acciones">
                            <a href="categoria-editar.html?id=${cat.id}" class="btn login-btn">Editar</a>
                            <button class="btn login-btn btn-eliminar-categoria" data-id="${cat.id}">Eliminar</button>
                        </div>
                    </div>
                `;
            });

            activarBotonesEliminar();

        } catch (error) {
            console.error("Error JS:", error);
            contenedor.innerHTML = "<p>Error inesperado</p>";
        }
    }

    cargarCategorias();

    /* ======================================================
       AÑADIR CATEGORÍA (SweetAlert2)
    ====================================================== */
    const btnAdd = document.getElementById("btn-add-categoria");

    if (btnAdd) {
        btnAdd.addEventListener("click", () => {
            Swal.fire({
                title: "Nueva categoría",
                html: `
                    <input id="cat-nombre" class="swal2-input" placeholder="Nombre">
                    <input id="cat-premios" type="number" class="swal2-input" placeholder="Número de premios">
                    <input id="cat-fisico" type="number" class="swal2-input" placeholder="Premios físicos">
                `,
                confirmButtonText: "Crear",
                showCancelButton: true,
                preConfirm: () => {
                    const nombre = document.getElementById("cat-nombre").value.trim();
                    const premios = document.getElementById("cat-premios").value.trim();
                    const fisico = document.getElementById("cat-fisico").value.trim();

                    if (!nombre || !premios || !fisico) {
                        Swal.showValidationMessage("Todos los campos son obligatorios");
                        return false;
                    }

                    return { nombre, premios, premio_fisico: fisico };
                }
            }).then(async res => {
                if (!res.isConfirmed) return;

                try {
                    const respuesta = await fetch("../php/categoria-nueva.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(res.value)
                    });

                    const resultado = await respuesta.json();

                    if (!resultado.ok) {
                        Swal.fire("Error", resultado.msg || "No se pudo crear", "error");
                        return;
                    }

                    Swal.fire("Creada", "La categoría ha sido añadida", "success");
                    cargarCategorias();

                } catch (error) {
                    console.error(error);
                    Swal.fire("Error", "Error de conexión", "error");
                }
            });
        });
    }

    /* ======================================================
       ELIMINAR CATEGORÍA
    ====================================================== */
    function activarBotonesEliminar() {
        document.querySelectorAll(".btn-eliminar-categoria").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                const confirm = await Swal.fire({
                    title: "¿Eliminar categoría?",
                    text: "Esta acción no se puede deshacer",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Eliminar",
                    cancelButtonText: "Cancelar"
                });

                if (!confirm.isConfirmed) return;

                try {
                    const res = await fetch(`../php/categoria-eliminar.php?id=${id}`);
                    const resultado = await res.json();

                    if (!resultado.ok) {
                        Swal.fire("Error", resultado.msg || "No se pudo eliminar", "error");
                        return;
                    }

                    Swal.fire("Eliminada", "La categoría ha sido borrada", "success");
                    cargarCategorias();

                } catch (error) {
                    console.error(error);
                    Swal.fire("Error", "Error de conexión", "error");
                }
            });
        });
    }

    /* ======================================================
       FORMULARIO (CREAR / EDITAR)
    ====================================================== */
    const form = document.getElementById("form-categoria");
    if (!form) return;

    const nombre = document.getElementById("nombre");
    const premios = document.getElementById("premios");
    const premioFisico = document.getElementById("premio-fisico");

    const errorNombre = document.getElementById("error-nombre");
    const errorPremios = document.getElementById("error-premios");
    const errorPremioFisico = document.getElementById("error-premio-fisico");
    const errorGlobal = document.getElementById("error-global");

    /* ======================================================
       CARGAR CATEGORÍA PARA EDITAR
    ====================================================== */
    async function cargarCategoriaEditar() {
        const params = new URLSearchParams(window.location.search);
        const id = params.get("id");
        if (!id) return;

        try {
            const res = await fetch(`../php/categoria-obtener.php?id=${id}`);
            const resultado = await res.json();

            if (!resultado.ok) {
                errorGlobal.textContent = resultado.error || "No se encontró la categoría";
                return;
            }

            const categoria = resultado.data;

            nombre.value = categoria.nombre;
            premios.value = categoria.premios;
            premioFisico.value = categoria.premio_fisico;

            form.dataset.id = id;

        } catch (error) {
            console.error(error);
            errorGlobal.textContent = "Error al cargar la categoría";
        }
    }

    cargarCategoriaEditar();

    /* ======================================================
       SUBMIT (CREAR O EDITAR)
    ====================================================== */
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        errorNombre.textContent = "";
        errorPremios.textContent = "";
        errorPremioFisico.textContent = "";
        errorGlobal.textContent = "";

        let valido = true;

        if (nombre.value.trim() === "") {
            errorNombre.textContent = "El nombre no puede estar vacío";
            valido = false;
        }

        if (premios.value.trim() === "") {
            errorPremios.textContent = "Indica los premios";
            valido = false;
        }

        if (premioFisico.value.trim() === "") {
            errorPremioFisico.textContent = "Indica si hay premio físico";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Corrige los errores del formulario";
            return;
        }

        const datos = {
            nombre: nombre.value,
            premios: premios.value,
            premio_fisico: premioFisico.value
        };

        const id = form.dataset.id;
        let url = "../php/categoria-nueva.php";

        if (id) {
            datos.id = id;
            url = "../php/categoria-editar.php";
        }

        try {
            const respuesta = await fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const resultado = await respuesta.json();

            if (!resultado.ok) {
                errorGlobal.textContent = resultado.msg || "Error al guardar";
                return;
            }

            window.location.href = "premios.html";

        } catch (error) {
            console.error(error);
            errorGlobal.textContent = "Error de conexión";
        }
    });

});
