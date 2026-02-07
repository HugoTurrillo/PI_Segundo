document.addEventListener("DOMContentLoaded", function () {

  const form = document.getElementById("formRegistro");

  // CAMPOS DE USUARIO
  const inputNombre = document.getElementById("nombre");
  const inputEmail = document.getElementById("email");
  const inputPassword = document.getElementById("password");
  const inputDni = document.getElementById("dni");
  const inputExpediente = document.getElementById("numero_expediente");

  const errorNombre = document.getElementById("error-nombre");
  const errorEmail = document.getElementById("error-email");
  const errorPassword = document.getElementById("error-password");
  const errorRol = document.getElementById("error-rol");
  const errorExpediente = document.getElementById("error-numero-expediente");

  // CAMPOS DE CANDIDATURA
  const inputTitulo = document.getElementById("titulo_obra");
  const inputSinopsis = document.getElementById("sinopsis");
  const inputCategoria = document.getElementById("id_categoria");
  const inputVideo = document.getElementById("video");
  const inputPortada = document.getElementById("portada");

  const errorGlobal = document.getElementById("registroErrorGlobal");

  /* ============================
     VALIDACIONES USUARIO
  ============================ */

  function validarNombre() {
    errorNombre.textContent = "";
    if (inputNombre.value.trim() === "") {
      errorNombre.textContent = "El nombre es obligatorio.";
      return false;
    }
    return true;
  }

  function validarEmail() {
    errorEmail.textContent = "";
    if (inputEmail.value.trim() === "") {
      errorEmail.textContent = "El correo es obligatorio.";
      return false;
    }
    return true;
  }

  function validarPassword() {
    errorPassword.textContent = "";
    if (inputPassword.value.trim() === "") {
      errorPassword.textContent = "La contraseña es obligatoria.";
      return false;
    }
    return true;
  }

  function validarRol() {
    errorRol.textContent = "";
    // ESTE CAMPO YA NO EXISTE, PERO LO MANTENGO POR SI LO USAS EN EL FUTURO
    return true;
  }

  function validarExpediente() {
    errorExpediente.textContent = "";
    if (inputExpediente.value.trim() === "") {
      errorExpediente.textContent = "El número de expediente es obligatorio.";
      return false;
    }
    return true;
  }

  /* ============================
     VALIDACIONES CANDIDATURA
  ============================ */

  function validarTitulo() {
    if (inputTitulo.value.trim() === "") {
      return "El título es obligatorio.";
    }
    return "";
  }

  function validarSinopsis() {
    if (inputSinopsis.value.trim() === "") {
      return "La sinopsis es obligatoria.";
    }
    return "";
  }

  function validarDni() {
    if (inputDni.value.trim() === "") {
      return "El DNI es obligatorio.";
    }
    return "";
  }

  function validarCategoria() {
    if (!inputCategoria.value) {
      return "Debes seleccionar una categoría.";
    }
    return "";
  }

  function validarVideo() {
    if (!inputVideo.files.length) {
      return "Debes subir un vídeo.";
    }
    return "";
  }

  function validarPortada() {
    if (!inputPortada.files.length) {
      return "Debes subir una portada.";
    }
    return "";
  }

  /* ============================
     ENVÍO DEL FORMULARIO
  ============================ */

  form.addEventListener("submit", async function (event) {
    event.preventDefault();
    errorGlobal.textContent = "";

    // Validaciones usuario
    const okNombre = validarNombre();
    const okEmail = validarEmail();
    const okPassword = validarPassword();
    const okRol = validarRol();
    const okExpediente = validarExpediente();

    // Validaciones candidatura
    const erroresCandidatura = [
      validarTitulo(),
      validarSinopsis(),
      validarDni(),
      validarCategoria(),
      validarVideo(),
      validarPortada()
    ].filter(e => e !== "");

    if (!okNombre || !okEmail || !okPassword || !okRol || !okExpediente || erroresCandidatura.length > 0) {
      errorGlobal.textContent = "Hay errores en el formulario.";
      return;
    }

    // Enviar datos con FormData
    const formData = new FormData(form);

    try {
      const res = await fetch("../php/registro.php", {
        method: "POST",
        body: formData
      });

      const data = await res.json();

      if (data.ok) {
        Swal.fire("Registro completado", "Tu candidatura ha sido enviada", "success");
        setTimeout(() => {
          window.location.href = "login.html";
        }, 1500);
      } else {
        errorGlobal.textContent = data.mensaje;
      }

    } catch (err) {
      errorGlobal.textContent = "Error de comunicación con el servidor.";
    }
  });

});