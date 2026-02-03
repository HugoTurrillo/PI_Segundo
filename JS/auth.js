(async function () {

    try {
      const res = await fetch("../php/auth-status.php", {
        credentials: "include"
      });
  
      const data = await res.json();
  
      // No logueado
      if (!data.auth) {
        window.location.href = "../php/home.php";
        return;
      }
  
      // Control por rol
      const rolRequerido = document.body.dataset.rol;
  
      if (rolRequerido && data.rol !== rolRequerido) {
        window.location.href = "../php/home.php";
        return;
      }
  
      
  
    } catch (e) {
      console.error("Error comprobando autenticación", e);
      window.location.href = "../php/home.php";
    }
  
  })();
  