document.addEventListener("DOMContentLoaded", () => {

    /* ==========================================
       VALIDACIÓN FORMULARIO GALA
       ========================================== */

    const formGala = document.getElementById("form-gala");

    if (formGala) {

        const titulo = document.getElementById("titulo");
        const fecha = document.getElementById("fecha");
        const hora = document.getElementById("hora");
        const lugar = document.getElementById("lugar");
        const descripcion = document.getElementById("descripcion");
        const imagen = document.getElementById("imagen");

        const errorTitulo = document.getElementById("error-titulo");
        const errorFecha = document.getElementById("error-fecha");
        const errorHora = document.getElementById("error-hora");
        const errorLugar = document.getElementById("error-lugar");
        const errorDescripcion = document.getElementById("error-descripcion");
        const errorImagen = document.getElementById("error-imagen");
        const errorGlobal = document.getElementById("error-global");

        formGala.addEventListener("submit", (e) => {
            let valido = true;

            // Limpiar errores
            errorTitulo.textContent = "";
            errorFecha.textContent = "";
            errorHora.textContent = "";
            errorLugar.textContent = "";
            errorDescripcion.textContent = "";
            errorImagen.textContent = "";
            errorGlobal.textContent = "";

            /* ============================
               VALIDACIÓN TÍTULO
            ============================ */
            if (titulo.value.trim() === "") {
                errorTitulo.textContent = "El título del evento es obligatorio.";
                valido = false;
            }

            /* ============================
               VALIDACIÓN FECHA
            ============================ */
            if (fecha.value.trim() === "") {
                errorFecha.textContent = "La fecha es obligatoria.";
                valido = false;
            }

            /* ============================
               VALIDACIÓN HORA
            ============================ */
            if (hora.value.trim() === "") {
                errorHora.textContent = "La hora es obligatoria.";
                valido = false;
            }

            /* ============================
               VALIDACIÓN LUGAR
            ============================ */
            if (lugar.value.trim() === "") {
                errorLugar.textContent = "El lugar es obligatorio.";
                valido = false;
            }

            /* ============================
               VALIDACIÓN DESCRIPCIÓN
            ============================ */
            if (descripcion.value.length > 600) {
                errorDescripcion.textContent = "La descripción no puede superar los 600 caracteres.";
                valido = false;
            }

            /* ============================
               VALIDACIÓN IMAGEN
               - Obligatoria solo en NUEVO
            ============================ */
            const esNuevo = window.location.pathname.includes("gala-nueva");

            if (esNuevo && (!imagen.files || imagen.files.length === 0)) {
                errorImagen.textContent = "Debes subir una imagen para el evento.";
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