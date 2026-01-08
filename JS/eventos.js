document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-evento");
    if (!form) return;

    const titulo = document.getElementById("titulo");
    const fecha = document.getElementById("fecha");
    const descripcion = document.getElementById("descripcion");

    const errorTitulo = document.getElementById("error-titulo");
    const errorFecha = document.getElementById("error-fecha");
    const errorDescripcion = document.getElementById("error-descripcion");
    const errorGlobal = document.getElementById("error-global");

    const hoy = new Date().toISOString().split("T")[0];
    const maxFecha = "2026-12-21";

    form.addEventListener("submit", (e) => {
        let valido = true;

        errorTitulo.textContent = "";
        errorFecha.textContent = "";
        errorDescripcion.textContent = "";
        errorGlobal.textContent = "";

        if (titulo.value.trim() === "") {
            errorTitulo.textContent = "El título no puede estar vacío.";
            valido = false;
        }

        if (fecha.value === "") {
            errorFecha.textContent = "La fecha es obligatoria.";
            valido = false;
        } else if (fecha.value < hoy) {
            errorFecha.textContent = "La fecha no puede ser anterior a hoy.";
            valido = false;
        } else if (fecha.value > maxFecha) {
            errorFecha.textContent = "La fecha no puede ser posterior al 21/12/2026.";
            valido = false;
        }

        if (descripcion.value.trim() === "") {
            errorDescripcion.textContent = "La descripción no puede estar vacía.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
            e.preventDefault();
        }
    });
});