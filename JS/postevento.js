/**
 * Gestiono el formulario del postevento: construyo el JSON de ganadores, envío el FormData y manejo la galería de imágenes.
 */

document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("form-postevento");
  if (!form) return;

  const resumen = document.getElementById("resumen");

  const ganadorAlumnos = document.getElementById("ganador_alumnos");
  const cortoAlumnos = document.getElementById("corto_alumnos");

  const ganadorAlumni = document.getElementById("ganador_alumni");
  const cortoAlumni = document.getElementById("corto_alumni");

  const ganadorProfesional = document.getElementById("ganador_profesional");
  const cortoProfesional = document.getElementById("corto_profesional");

  const imagenes = document.getElementById("imagenes");
  const anio = document.getElementById("anio");
  const participantes = document.getElementById("participantes");
  const ganadoresJson = document.getElementById("ganadores_json");
  const cortosGanadores = document.getElementById("cortos_ganadores");

  const errorGlobal = document.getElementById("error-global");

  function construirGanadoresJSON() {
    const data = {
      alumnos: {
        ganador: ganadorAlumnos.value.trim(),
        corto: cortoAlumnos.value.trim()
      },
      alumni: {
        ganador: ganadorAlumni.value.trim(),
        corto: cortoAlumni.value.trim()
      },
      profesional: {
        ganador: ganadorProfesional.value.trim(),
        corto: cortoProfesional.value.trim()
      }
    };
    ganadoresJson.value = JSON.stringify(data, null, 2);
  }

  [
    ganadorAlumnos,
    cortoAlumnos,
    ganadorAlumni,
    cortoAlumni,
    ganadorProfesional,
    cortoProfesional
  ].forEach(input => {
    input.addEventListener("input", construirGanadoresJSON);
  });

  construirGanadoresJSON();

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorGlobal.textContent = "";

    let valido = true;

    if (resumen.value.trim() === "") {
      valido = false;
      errorGlobal.textContent = "El resumen es obligatorio.";
    }

    if (anio.value.trim() === "") {
      valido = false;
      errorGlobal.textContent = "El año de la edición es obligatorio.";
    }

    if (participantes.value.trim() === "") {
      valido = false;
      errorGlobal.textContent = "El número de participantes es obligatorio.";
    }

    if (!valido) {
      Swal.fire("Error", errorGlobal.textContent, "error");
      return;
    }

    construirGanadoresJSON();

    const datos = new FormData();
    datos.append("resumen", resumen.value.trim());
    datos.append("ganador_alumnos", ganadorAlumnos.value.trim());
    datos.append("corto_alumnos", cortoAlumnos.value.trim());
    datos.append("ganador_alumni", ganadorAlumni.value.trim());
    datos.append("corto_alumni", cortoAlumni.value.trim());
    datos.append("ganador_profesional", ganadorProfesional.value.trim());
    datos.append("corto_profesional", cortoProfesional.value.trim());
    datos.append("anio", anio.value.trim());
    datos.append("participantes", participantes.value.trim());
    datos.append("ganadores_json", ganadoresJson.value);

    if (imagenes.files && imagenes.files.length > 0) {
      for (let i = 0; i < imagenes.files.length; i++) {
        datos.append("imagenes[]", imagenes.files[i]);
      }
    }

    if (cortosGanadores.files && cortosGanadores.files.length > 0) {
      for (let i = 0; i < cortosGanadores.files.length; i++) {
        datos.append("cortos_ganadores[]", cortosGanadores.files[i]);
      }
    }

    try {
      const res = await fetch("../php/postevento-guardar.php", {
        method: "POST",
        body: datos
      });

      const r = await res.json();

      if (r.ok) {
        await Swal.fire({
          icon: "success",
          title: "Post‑evento guardado",
          text: r.msg || "La información del post‑evento se ha guardado correctamente."
        });
        window.location.href = "gala-postevento.html";
      } else {
        errorGlobal.textContent = r.msg || "Error al guardar el post‑evento.";
        Swal.fire("Error", errorGlobal.textContent, "error");
      }

    } catch (err) {
      console.error(err);
      errorGlobal.textContent = "Error de comunicación con el servidor.";
      Swal.fire("Error", errorGlobal.textContent, "error");
    }

  });

});
