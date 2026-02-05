document.addEventListener("DOMContentLoaded", function () {

  const form = document.getElementById("formRegistro");

  const inputNombre = document.getElementById("nombre");
  const inputEmail = document.getElementById("email");
  const inputPassword = document.getElementById("password");
  const inputRol = document.getElementById("rol_participante");

  const errorNombre = document.getElementById("error-nombre");
  const errorEmail = document.getElementById("error-email");
  const errorPassword = document.getElementById("error-password");
  const errorRol = document.getElementById("error-rol");
  const errorGlobal = document.getElementById("registroErrorGlobal");

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
    if (!inputRol.value) {
      errorRol.textContent = "Debes seleccionar un perfil.";
      return false;
    }
    return true;
  }

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    errorGlobal.textContent = "";

    const okNombre = validarNombre();
    const okEmail = validarEmail();
    const okPassword = validarPassword();
    const okRol = validarRol();

    if (!okNombre || !okEmail || !okPassword || !okRol) {
      errorGlobal.textContent = "Hay errores en el formulario.";
      return;
    }

    const datos = {
      nombre: inputNombre.value.trim(),
      email: inputEmail.value.trim(),
      password: inputPassword.value.trim(),
      rol_participante: inputRol.value
    };

    fetch("../php/registro.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(datos)
    })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          errorGlobal.style.color = "green";
          errorGlobal.textContent = "Registro completado. Redirigiendo...";
          setTimeout(() => {
            window.location.href = "login.html";
          }, 1500);
        } else {
          errorGlobal.style.color = "";
          errorGlobal.textContent = data.mensaje;
        }
      })
      .catch(() => {
        errorGlobal.textContent = "Error de comunicación con el servidor.";
      });
  });

});
