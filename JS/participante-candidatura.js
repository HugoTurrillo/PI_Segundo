document.addEventListener("DOMContentLoaded", async () => {

    try {
      const res = await fetch("../php/candidatura-mi-estado.php");
      const data = await res.json();
  
      if (!data.ok || !data.candidatura) {
        document.getElementById("sinCandidatura").style.display = "block";
        return;
      }
  
      const c = data.candidatura;
  
      document.getElementById("conCandidatura").style.display = "block";
      document.getElementById("titulo").textContent = c.titulo_obra;
      document.getElementById("estado").textContent = c.estado;
      document.getElementById("sinopsis").textContent = c.sinopsis;
  
    } catch (err) {
      console.error("Error cargando candidatura:", err);
      document.getElementById("sinCandidatura").style.display = "block";
    }
  
  });
  