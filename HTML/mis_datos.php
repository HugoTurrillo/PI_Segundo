<?php
require_once "../php/config/auth.php";
requireRole("participante");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis datos - Festival Cortos UEM</title>
  <link rel="stylesheet" href="../CSS/estilos.css">
  <link rel="icon" href="../IMG/favicon.png">
</head>

<body>

  <header class="navbar">
    <div class="logo">
      <img src="../IMG/LOGOENTERO.png" alt="Logo Universidad Europea">
    </div>

    <nav>
      <ul class="nav-links">
        <li><a href="participante.php">Panel</a></li>
      </ul>
    </nav>

    <div class="nav-buttons">
      <a href="../php/logout.php" class="btn nav-btn">Salir</a>
    </div>
  </header>

  <main class="login-container">

    <h2>Mis datos personales</h2>
    <p>Revisa y actualiza tu información personal.</p>

    <form id="form-mis-datos" class="login-form">

      <label>Nombre completo</label>
      <input type="text" id="nombre">
      <span class="error-campo" id="error-nombre"></span>

      <label>Email</label>
      <input type="email" id="email" disabled>
      <small style="color:#666;">El email no se puede modificar</small>

      <label>Nueva contraseña</label>
      <input type="password" id="password">
      <span class="error-campo" id="error-password"></span>

      <button type="submit" class="btn login-btn" style="margin-top:1rem;">
        Guardar cambios
      </button>

      <p class="error-global" id="error-global"></p>

    </form>

  </main>

  <!-- ================= FOOTER ================= -->
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

  <script src="../JS/mis_datos.js"></script>
</body>

</html>