document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("form-gala");

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

    // ======================================================
    // CARGAR DATOS DE LA GALA EXISTENTE
    // ======================================================
    async function cargarGala() {
        try {
            const res = await fetch("../php/gala-obtener.php");
            const data = await res.json();

            if (!data.ok) {
                errorGlobal.textContent = "No existe ninguna gala para editar.";
                return;
            }

            const gala = data.data;

            titulo.value = gala.titulo;
            fecha.value = gala.fecha;
            hora.value = gala.hora;
            lugar.value = gala.lugar;
            descripcion.value = gala.descripcion ?? "";

        } catch (err) {
            console.error(err);
            errorGlobal.textContent = "Error al cargar la gala.";
        }
    }

    cargarGala();

    // ======================================================
    // VALIDACIÓN FECHA Y HORA
    // ======================================================
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, "0");
    const dd = String(hoy.getDate()).padStart(2, "0");

    fecha.setAttribute("min", `${yyyy}-${mm}-${dd}`);

    fecha.addEventListener("change", validarFechaHora);
    hora.addEventListener("change", validarFechaHora);

    function validarFechaHora() {
        if (!fecha.value || !hora.value) return;

        const ahora = new Date();
        const fechaEvento = new Date(`${fecha.value}T${hora.value}`);

        if (fechaEvento <= ahora) {
            Swal.fire({
                icon: "error",
                title: "Fecha u hora no válida",
                text: "No puedes establecer una gala en una fecha pasada ni en una hora anterior a la actual."
            });

            hora.value = "";
        }
    }

    // ======================================================
    // SUBMIT FORMULARIO
    // ======================================================
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Limpiar errores
        [
            errorTitulo,
            errorFecha,
            errorHora,
            errorLugar,
            errorDescripcion,
            errorImagen,
            errorGlobal
        ].forEach(el => el.textContent = "");

        let valido = true;

        if (titulo.value.trim() === "") {
            errorTitulo.textContent = "El título es obligatorio.";
            valido = false;
        }

        if (fecha.value.trim() === "") {
            errorFecha.textContent = "La fecha es obligatoria.";
            valido = false;
        }

        if (hora.value.trim() === "") {
            errorHora.textContent = "La hora es obligatoria.";
            valido = false;
        }

        if (lugar.value.trim() === "") {
            errorLugar.textContent = "El lugar es obligatorio.";
            valido = false;
        }

        if (descripcion.value.length > 600) {
            errorDescripcion.textContent = "Máximo 600 caracteres.";
            valido = false;
        }

        if (!valido) {
            errorGlobal.textContent = "Hay errores en el formulario.";
            return;
        }

        // Enviar datos
        const datos = new FormData();
        datos.append("titulo", titulo.value);
        datos.append("fecha", fecha.value);
        datos.append("hora", hora.value);
        datos.append("lugar", lugar.value);
        datos.append("descripcion", descripcion.value);

        if (imagen.files.length > 0) {
            datos.append("imagen", imagen.files[0]);
        }

        try {
            const res = await fetch("../php/gala-editar.php", {
                method: "POST",
                body: datos
            });

            const r = await res.json();

            if (r.ok) {
                await Swal.fire({
                    icon: "success",
                    title: "Gala actualizada",
                    text: "Los cambios se han guardado correctamente."
                });

                window.location.href = "gala.html";
            } else {
                errorGlobal.textContent = r.msg;
            }

        } catch (err) {
            console.error(err);
            errorGlobal.textContent = "Error al guardar los cambios.";
        }
    });

});
