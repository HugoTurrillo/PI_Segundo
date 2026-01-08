document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-noticia");
    if (!form) return;

    const titulo = document.getElementById("titulo");
    const contenido = document.getElementById("contenido");

    const errorTitulo = document.getElementById("error-titulo");
    const errorContenido = document.getElementById("error-contenido");
    const errorGlobal = document.getElementById("error-global");

    form.addEventListener("submit", (e) => {
        let valido = true;

        errorTitulo.textContent = "";
        errorContenido.textContent = "";
        errorGlobal.textContent = "";

        if (titulo.value.trim() === "") {
            errorTitulo.textContent = "El título no puede estar vacío.";
            valido = false;
        }

        if (contenido.value.trim() === "") {
            errorContenido.textContent = "El contenido no puede estar vacío.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
            e.preventDefault();
        }
    });
});