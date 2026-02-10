document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("form-seccion");

    const titulo = document.getElementById("titulo");
    const hora = document.getElementById("hora");
    const sala = document.getElementById("sala");
    const descripcion = document.getElementById("descripcion");

    const errorTitulo = document.getElementById("error-titulo");
    const errorHora = document.getElementById("error-hora");
    const errorSala = document.getElementById("error-sala");
    const errorDescripcion = document.getElementById("error-descripcion");
    const errorGlobal = document.getElementById("error-global");

    const params = new URLSearchParams(window.location.search);
    const idSeccion = params.get("id");
    const idGala = params.get("id_gala");

    // ======================================================
    // SI ES EDICIÓN → CARGAR DATOS DE LA SECCIÓN
    // ======================================================
    if (idSeccion) {
        cargarSeccion();
    }

    async function cargarSeccion() {
        try {
            const res = await fetch("../php/gala-secciones-listar.php?id_gala=0"); 
            
            

            const res2 = await fetch("../php/gala-secciones-listar.php?id_gala=1");
            const data = await res2.json();

            if (!data.ok) {
                errorGlobal.textContent = "Error cargando la sección.";
                return;
            }

            const seccion = data.data.find(s => s.id == idSeccion);

            if (!seccion) {
                errorGlobal.textContent = "No se encontró la sección.";
                return;
            }

            titulo.value = seccion.titulo;
            hora.value = seccion.hora;
            sala.value = seccion.sala;
            descripcion.value = seccion.descripcion ?? "";

        } catch (err) {
            console.error(err);
            errorGlobal.textContent = "Error al cargar la sección.";
        }
    }

    // ======================================================
    // VALIDACIÓN
    // ======================================================
    function validar() {
        let valido = true;

        [
            errorTitulo,
            errorHora,
            errorSala,
            errorDescripcion,
            errorGlobal
        ].forEach(el => el.textContent = "");

        if (titulo.value.trim() === "") {
            errorTitulo.textContent = "El título es obligatorio.";
            valido = false;
        }

        if (hora.value.trim() === "") {
            errorHora.textContent = "La hora es obligatoria.";
            valido = false;
        }

        if (sala.value.trim() === "") {
            errorSala.textContent = "La sala es obligatoria.";
            valido = false;
        }

        if (descripcion.value.length > 600) {
            errorDescripcion.textContent = "Máximo 600 caracteres.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario.";
        }

        return valido;
    }

    // ======================================================
    // SUBMIT FORMULARIO
    // ======================================================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (!validar()) return;

        const datos = {
            titulo: titulo.value,
            hora: hora.value,
            sala: sala.value,
            descripcion: descripcion.value
        };

        let url = "";
        let metodo = "POST";

        if (idSeccion) {
            // EDITAR
            datos.id = idSeccion;
            url = "../php/gala-seccion-editar.php";
        } else {
            // CREAR
            datos.id_gala = idGala;
            url = "../php/gala-seccion-crear.php";
        }

        try {
            const res = await fetch(url, {
                method: metodo,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            const r = await res.json();

            if (r.ok) {
                await Swal.fire({
                    icon: "success",
                    title: idSeccion ? "Sección actualizada" : "Sección creada",
                    text: idSeccion
                        ? "Los cambios se han guardado correctamente."
                        : "La sección se ha añadido correctamente."
                });

                window.location.href = "gala.html";
            } else {
                errorGlobal.textContent = r.msg;
            }

        } catch (err) {
            console.error(err);
            errorGlobal.textContent = "Error al guardar la sección.";
        }
    });

});
