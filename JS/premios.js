document.addEventListener("DOMContentLoaded", () => {

    /* ============================
       VALIDACIÓN DE CATEGORÍAS
       ============================ */
    const formCategoria = document.getElementById("form-categoria");

    if (formCategoria) {
        const nombre = document.getElementById("nombre");
        const premios = document.getElementById("premios");
        const premioFisico = document.getElementById("premio-fisico");

        const errorNombre = document.getElementById("error-nombre");
        const errorPremios = document.getElementById("error-premios");
        const errorPremioFisico = document.getElementById("error-premio-fisico");
        const errorGlobal = document.getElementById("error-global");

        formCategoria.addEventListener("submit", (e) => {
            let valido = true;

            errorNombre.textContent = "";
            errorPremios.textContent = "";
            errorPremioFisico.textContent = "";
            errorGlobal.textContent = "";

            if (nombre.value.trim() === "") {
                errorNombre.textContent = "El nombre de la categoría no puede estar vacío.";
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
                errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
                e.preventDefault();
            }
        });
    }


    /* ==========================================
       VALIDACIÓN GANADOR CARRERA PROFESIONAL
       ========================================== */
    const formCarrera = document.getElementById("form-carrera");

    if (formCarrera) {
        const nombre = document.getElementById("nombre");
        const email = document.getElementById("email");
        const telefono = document.getElementById("telefono");
        const video = document.getElementById("video");

        const errorNombre = document.getElementById("error-nombre");
        const errorEmail = document.getElementById("error-email");
        const errorTelefono = document.getElementById("error-telefono");
        const errorVideo = document.getElementById("error-video");
        const errorGlobal = document.getElementById("error-global");

        formCarrera.addEventListener("submit", (e) => {
            let valido = true;

            errorNombre.textContent = "";
            errorEmail.textContent = "";
            errorTelefono.textContent = "";
            errorVideo.textContent = "";
            errorGlobal.textContent = "";

            if (nombre.value.trim() === "") {
                errorNombre.textContent = "El nombre no puede estar vacío.";
                valido = false;
            }

            if (email.value.trim() === "") {
                errorEmail.textContent = "El correo electrónico es obligatorio.";
                valido = false;
            } else if (!email.value.includes("@") || !email.value.includes(".")) {
                errorEmail.textContent = "Introduce un correo electrónico válido.";
                valido = false;
            }

            if (telefono.value.trim() === "") {
                errorTelefono.textContent = "El teléfono es obligatorio.";
                valido = false;
            } else if (telefono.value.length < 9) {
                errorTelefono.textContent = "Introduce un teléfono válido.";
                valido = false;
            }

            if (!video.files || video.files.length === 0) {
                errorVideo.textContent = "Debes subir un vídeo del recorrido profesional.";
                valido = false;
            }

            if (!valido) {
                errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
                e.preventDefault();
            }
        });
    }

});