// JS/registro.js

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formRegistro");
  const inputNombre = document.getElementById("nombre");
  const inputEmail = document.getElementById("email");
  const inputPassword = document.getElementById("password");
  const errorNombre = document.getElementById("error-nombre");
  const errorEmail = document.getElementById("error-email");
  const errorPassword = document.getElementById("error-password");
  const errorGlobal = document.getElementById("registroErrorGlobal");

  function validarNombre() {
    const valor = inputNombre.value.trim();
    errorNombre.textContent = "";
    if (valor === "") {
      errorNombre.textContent = "El nombre es obligatorio.";
      return false;
    }
    return true;
  }

  function validarEmail() {
    const valor = inputEmail.value.trim();
    errorEmail.textContent = "";
    if (valor === "") {
      errorEmail.textContent = "El correo es obligatorio.";
      return false;
    }
    return true;
  }

  function validarPassword() {
    const valor = inputPassword.value.trim();
    errorPassword.textContent = "";
    if (valor === "") {
      errorPassword.textContent = "La contraseña es obligatoria.";
      return false;
    }
    return true;
  }

  inputNombre.addEventListener("input", validarNombre);
  inputEmail.addEventListener("input", validarEmail);
  inputPassword.addEventListener("input", validarPassword);

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    errorGlobal.textContent = "";

    const okNombre = validarNombre();
    const okEmail = validarEmail();
    const okPassword = validarPassword();

    if (!okNombre || !okEmail || !okPassword) {
      errorGlobal.textContent = "Hay errores en el formulario. Revísalos antes de continuar.";
      return;
    }

    const datos = {
      nombre: inputNombre.value.trim(),
      email: inputEmail.value.trim(),
      password: inputPassword.value.trim()
    };

    fetch("../php/registro.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(datos)
    })
      .then(function (respuesta) {
        return respuesta.json();
      })
      .then(function (data) {
        if (data.ok) {
          errorGlobal.style.color = "green";
          errorGlobal.textContent = "Registro completado. Redirigiendo al login...";
          setTimeout(function () {
            window.location.href = "login.html";
          }, 1500);
        } else {
          errorGlobal.style.color = "";
          errorGlobal.textContent = data.mensaje || "Error al registrar usuario.";
        }
      })
      .catch(function () {
        errorGlobal.style.color = "";
        errorGlobal.textContent = "Error de comunicación con el servidor.";
      });
  });
});
