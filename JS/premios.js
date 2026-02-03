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
            console.log("RESPUESTA JS:", resultado);
            console.log("TIPO DE DATA:", typeof resultado.data);
            console.log("DATA:", resultado.data);


            if (!resultado.ok) {
                console.error("Error backend:", resultado.error);
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
                    <div class="panel-card">
                        <h3>${cat.nombre}</h3>
                        <p>Premios: ${cat.premios}</p>
                        <p>Premio físico: ${cat.premio_fisico}</p>

                        <div style="margin-top:1rem; display:flex; gap:1rem;">
                            <a href="categoria-editar.html?id=${cat.id}"
                               class="btn login-btn"
                               style="padding:0.5rem 1rem;">
                                Editar
                            </a>

                            <button class="btn login-btn btn-eliminar-categoria"
                                    data-id="${cat.id}"
                                    style="padding:0.5rem 1rem; background:#555;">
                                Eliminar
                            </button>
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
       ELIMINAR CATEGORÍA
    ====================================================== */
    function activarBotonesEliminar() {
        document.querySelectorAll(".btn-eliminar-categoria").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                if (!confirm("¿Seguro que quieres eliminar esta categoría?")) return;

                try {
                    const res = await fetch(`../php/categoria-eliminar.php?id=${id}`);
                    const resultado = await res.json();

                    if (!resultado.ok) {
                        alert(resultado.msg || "Error al eliminar");
                        return;
                    }

                    cargarCategorias();

                } catch (error) {
                    console.error(error);
                    alert("Error de conexión");
                }
            });
        });
    }



    /* ======================================================
       FORMULARIO (CREAR / EDITAR)
    ====================================================== */
    const form = document.getElementById("form-categoria");
    if (!form) return; // No estamos en nueva / editar

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

        // Reset errores
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
        let url = "../php/categoria-nueva.html";

        if (id) {
            datos.id = id;
            url = "../php/categoria-editar.html";
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
