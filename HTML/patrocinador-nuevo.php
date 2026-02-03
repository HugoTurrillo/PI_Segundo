<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Añadir patrocinador</title>
  <link rel="stylesheet" href="../CSS/estilos.css">
  <script defer src="../JS/patrocinadores.js"></script>
  <link rel="icon" href="../IMG/favicon.png" type="image/x-icon">
</head>

<body data-rol="organizador">

  <header class="navbar">
    <div class="logo">
      <img src="../IMG/LOGOENTERO.png" alt="Logo Universidad Europea">
    </div>

    <div class="search-bar">
      <input type="text" placeholder="Buscar...">
    </div>

    <nav>
      <ul class="nav-links">
        <li><a href="organizador.php">Panel</a></li>
        <li><a href="patrocinadores.php">Patrocinadores</a></li>
      </ul>
    </nav>

    <div class="nav-buttons">
      <a href="login.html" class="btn nav-btn">Salir</a>
    </div>
  </header>

  <main class="login-container">
    <h2>Añadir patrocinador</h2>

    <form id="form-patrocinador" class="login-form" method="POST" enctype="multipart/form-data">


      <label for="nombre">Nombre del patrocinador</label>
      <input type="text" id="nombre" name="nombre" required>
      <span class="error-campo" id="error-nombre"></span>

      <label for="logo">Logo (imagen)</label>
      <input type="file" id="logo" name="logo" accept="image/*" required>
      <span class="error-campo" id="error-logo"></span>

      <label for="enlace">Enlace web</label>
      <input type="text" id="enlace" name="enlace" placeholder="https://www.ejemplo.com" required>
      <span class="error-campo" id="error-enlace"></span>

      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="4" style="width:100%; padding:0.6rem; border:1px solid #ccc; border-radius:4px;"></textarea>
      <span class="error-campo" id="error-descripcion"></span>

      <div class="error-global" id="error-global"></div>

      <button type="submit" class="btn login-btn">Guardar patrocinador</button>
    </form>
  </main>

  <footer class="footer">
    <div class="footer-main">
      <div class="footer-left">
        <div class="footer-social-row">
          <img src="../IMG/instagram.png" alt="Instagram">
          <span>@cortosuem</span>
        </div>
        <div class="footer-social-row">
          <img src="../IMG/facebook.png" alt="Facebook">
          <span>@cortosuem</span>
        </div>
        <div class="footer-social-row">
          <img src="../IMG/twitter.png" alt="Twitter">
          <span>@cortosuem</span>
        </div>
      </div>

      <div class="footer-center">
        <a href="../HTML/contacto.html" class="footer-contact-link">Contacta con nosotros</a>
      </div>

      <div class="footer-right">
        <div class="footer-logo-circle">
          <img src="../IMG/logosimple.jpg" alt="Logo">
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      Universidad Europea © 2025. Todos los Derechos Reservados
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../JS/auth.js"></script>



</body>

</html>