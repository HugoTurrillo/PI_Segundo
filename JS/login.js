/**
 * Gestiono el formulario de login: valido campos y envío los datos al servidor; si todo va bien redirijo al panel correspondiente.
 */

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formLogin");
  const inputEmail = document.getElementById("expediente");
  const inputPassword = document.getElementById("password");
  const errorEmail = document.getElementById("error-expediente");
  const errorPassword = document.getElementById("error-password");
  const errorGlobal = document.getElementById("loginErrorGlobal");

  function validarEmail() {
    const valor = inputEmail.value.trim();
    errorEmail.textContent = "";

    if (valor === "") {
      errorEmail.textContent = "El correo o expediente es obligatorio.";
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

  inputEmail.addEventListener("input", validarEmail);
  inputPassword.addEventListener("input", validarPassword);

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    errorGlobal.textContent = "";

    const okEmail = validarEmail();
    const okPassword = validarPassword();

    if (!okEmail || !okPassword) {
      errorGlobal.textContent = "No se puede iniciar sesión aún, revise los errores.";
      return;
    }

    const datos = {
      email: inputEmail.value.trim(),
      password: inputPassword.value.trim()
    };

    fetch("../php/login.php", {
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
          window.location.href = data.redireccion;
        } else {
          errorGlobal.textContent = data.mensaje || "Email o contraseña incorrectos.";
        }
      })
      .catch(function () {
        errorGlobal.textContent = "Error de comunicación con el servidor.";
      });
  });
});
