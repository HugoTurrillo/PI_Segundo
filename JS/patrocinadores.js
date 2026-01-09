document.addEventListener("DOMContentLoaded", () => {

    /* ==========================================
       VALIDACIÓN FORMULARIO PATROCINADORES
       ========================================== */

    const formPatrocinador = document.getElementById("form-patrocinador");

    if (formPatrocinador) {

        const nombre = document.getElementById("nombre");
        const logo = document.getElementById("logo");
        const enlace = document.getElementById("enlace");
        const descripcion = document.getElementById("descripcion");

        const errorNombre = document.getElementById("error-nombre");
        const errorLogo = document.getElementById("error-logo");
        const errorEnlace = document.getElementById("error-enlace");
        const errorDescripcion = document.getElementById("error-descripcion");
        const errorGlobal = document.getElementById("error-global");

        formPatrocinador.addEventListener("submit", (e) => {
            let valido = true;

            // Limpiar errores
            errorNombre.textContent = "";
            errorLogo.textContent = "";
            errorEnlace.textContent = "";
            errorDescripcion.textContent = "";
            errorGlobal.textContent = "";

            /* ============================
               VALIDACIÓN NOMBRE
            ============================ */
            if (nombre.value.trim() === "") {
                errorNombre.textContent = "El nombre del patrocinador es obligatorio.";
                valido = false;
            }

            /* ============================
               VALIDACIÓN LOGO
               - Obligatorio solo en NUEVO
               - Opcional en EDITAR
            ============================ */
            const esNuevo = window.location.pathname.includes("patrocinador-nuevo");

            if (esNuevo && (!logo.files || logo.files.length === 0)) {
                errorLogo.textContent = "Debes subir un logo para el patrocinador.";
                valido = false;
            }

            /* ============================
               VALIDACIÓN ENLACE
            ============================ */
            if (enlace.value.trim() === "") {
                errorEnlace.textContent = "El enlace web es obligatorio.";
                valido = false;
            } else if (!enlace.value.startsWith("http://") && !enlace.value.startsWith("https://")) {
                errorEnlace.textContent = "El enlace debe comenzar por http:// o https://";
                valido = false;
            }

            /* ============================
               DESCRIPCIÓN (opcional)
            ============================ */
            if (descripcion.value.length > 500) {
                errorDescripcion.textContent = "La descripción no puede superar los 500 caracteres.";
                valido = false;
            }

            /* ============================
               ERROR GLOBAL
            ============================ */
            if (!valido) {
                errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
                e.preventDefault();
            }
        });
    }

});