/**
 * Cargo los datos del usuario con usuario-mis-datos.php y envío las actualizaciones con usuario-actualizar-datos.php.
 */

document.addEventListener("DOMContentLoaded", async () => {

  const nombre = document.getElementById("nombre");
  const email = document.getElementById("email");
  const password = document.getElementById("password");
  const rol = document.getElementById("rol");
  const dni = document.getElementById("dni");
  const numExp = document.getElementById("numero_expediente");

  const errorNombre = document.getElementById("error-nombre");
  const errorPassword = document.getElementById("error-password");
  const errorGlobal = document.getElementById("error-global");

  try {
    const res = await fetch("../php/usuario-mis-datos.php", {
      credentials: "include"
    });
    const data = await res.json();

    if (!data.ok) {
      errorGlobal.textContent = data.mensaje;
      return;
    }

    nombre.value = data.usuario.nombre_completo;
    email.value = data.usuario.email;
    rol.value = data.usuario.rol;

    //rellenar DNI y expediente
    dni.value = data.usuario.dni;
    numExp.value = data.usuario.numero_expediente;

  } catch {
    errorGlobal.textContent = "Error cargando los datos";
  }

  document.getElementById("form-mis-datos").addEventListener("submit", async e => {
    e.preventDefault();

    errorNombre.textContent = "";
    errorPassword.textContent = "";
    errorGlobal.textContent = "";

    let valido = true;

    if (nombre.value.trim() === "") {
      errorNombre.textContent = "El nombre es obligatorio";
      valido = false;
    }

    if (password.value && password.value.length < 6) {
      errorPassword.textContent = "La contraseña debe tener al menos 6 caracteres";
      valido = false;
    }

    if (!valido) {
      errorGlobal.textContent = "Corrige los errores";
      return;
    }

    const res = await fetch("../php/usuario-actualizar-datos.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({
        nombre: nombre.value.trim(),
        password: password.value.trim()
      })
    });

    const r = await res.json();

    if (r.ok) {
      await Swal.fire("Guardado", "Datos actualizados correctamente", "success");
      password.value = "";
    } else {
      errorGlobal.textContent = r.mensaje;
    }
  });

});