document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // LISTAR CATEGORÍAS
    // ============================
    async function cargarCategorias() {
        const contenedor = document.querySelector(".panel-grid");
        if (!contenedor) return; // Solo en premios.html

        const respuesta = await fetch("../php/categorias-listar.php");
        const categorias = await respuesta.json();

        contenedor.innerHTML = "";

        categorias.forEach(cat => {
            contenedor.innerHTML += `
                <div class="panel-card">
                    <h3>${cat.nombre}</h3>
                    <p>Premios: ${cat.premios}</p>
                    <p>Premio físico: ${cat.premio_fisico}</p>

                    <div style="margin-top: 1rem; display:flex; gap:1rem;">
                        <a href="categoria-editar.html?id=${cat.id}" class="btn login-btn" style="padding:0.5rem 1rem;">Editar</a>

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
    }

    cargarCategorias();



    // ============================
    // ELIMINAR CATEGORÍA
    // ============================
    function activarBotonesEliminar() {
        document.querySelectorAll(".btn-eliminar-categoria").forEach(btn => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;

                if (!confirm("¿Seguro que quieres eliminar esta categoría?")) return;

                const res = await fetch(`../php/categoria-eliminar.php?id=${id}`);
                const r = await res.json();

                alert(r.msg);
                if (r.ok) cargarCategorias();
            });
        });
    }



    // ============================
    // FORMULARIO (CREAR / EDITAR)
    // ============================
    const form = document.getElementById("form-categoria");
    if (!form) return; // Solo en nueva o editar

    const nombre = document.getElementById("nombre");
    const premios = document.getElementById("premios");
    const premioFisico = document.getElementById("premio-fisico");

    const errorNombre = document.getElementById("error-nombre");
    const errorPremios = document.getElementById("error-premios");
    const errorPremioFisico = document.getElementById("error-premio-fisico");
    const errorGlobal = document.getElementById("error-global");



    // ============================
    // CARGAR CATEGORÍA PARA EDITAR
    // ============================
    async function cargarCategoriaEditar() {
        const params = new URLSearchParams(window.location.search);
        const id = params.get("id");

        if (!id) return; // No estamos en editar

        const res = await fetch(`../php/categoria-obtener.php?id=${id}`);
        const categoria = await res.json();

        if (!categoria || !categoria.id) {
            errorGlobal.textContent = "No se encontró la categoría.";
            return;
        }

        nombre.value = categoria.nombre;
        premios.value = categoria.premios;
        premioFisico.value = categoria.premio_fisico;

        form.dataset.id = id;
    }

    cargarCategoriaEditar();



    // ============================
    // SUBMIT (CREAR O EDITAR)
    // ============================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        let valido = true;

        errorNombre.textContent = "";
        errorPremios.textContent = "";
        errorPremioFisico.textContent = "";
        errorGlobal.textContent = "";

        if (nombre.value.trim() === "") {
            errorNombre.textContent = "El nombre no puede estar vacío.";
            valido = false;
        }

        if (premios.value.trim() === "") {
            errorPremios.textContent = "La descripción de premios no puede estar vacía.";
            valido = false;
        }

        if (premioFisico.value.trim() === "") {
            errorPremioFisico.textContent = "Indica si existe premio físico o no.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario.";
            return;
        }



        // ============================
        // EDITAR CATEGORÍA
        // ============================
        const id = form.dataset.id;

        if (id) {
            const datos = {
                id: id,
                nombre: nombre.value,
                premios: premios.value,
                premio_fisico: premioFisico.value
            };

            const respuesta = await fetch("../php/categoria-editar.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const resultado = await respuesta.json();

            if (resultado.ok) {
                alert("Categoría actualizada correctamente");
                window.location.href = "premios.html";
            } else {
                errorGlobal.textContent = resultado.msg;
            }

            return;
        }



        // ============================
        // CREAR CATEGORÍA
        // ============================
        const datos = {
            nombre: nombre.value,
            premios: premios.value,
            premio_fisico: premioFisico.value
        };

        const respuesta = await fetch("../php/categoria-nueva.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.ok) {
            alert("Categoría creada correctamente");
            window.location.href = "premios.html";
        } else {
            errorGlobal.textContent = resultado.msg;
        }
    });

});