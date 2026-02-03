<?php
require_once "../php/config/auth.php";
requireRole("organizador");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gala - Panel Organizador</title>
  <link rel="stylesheet" href="../CSS/estilos.css">
  <link rel="icon" href="../IMG/favicon.png" type="image/x-icon">
</head>

<body>

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
        <li><a href="calendario.html">Calendario</a></li>
        <li><a href="#">Sobre nosotros</a></li>
      </ul>
    </nav>

    <div class="nav-buttons">
      <a href="login.html" class="btn nav-btn">Salir</a>
    </div>
  </header>

  <main class="login-container">

    <h2>Gestión de la Gala</h2>

    <section style="margin-top: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <h3 style="color: #d32f2f; font-size: 1.4rem;">Eventos de la Gala</h3>
        <a href="gala-nueva.php" class="btn login-btn" style="padding: 0.6rem 1.2rem;">Añadir evento</a>
      </div>
    </section>

    <!-- CONTENEDOR VACÍO PARA QUE EL JS LO RELLENE -->
    <div class="panel-grid"></div>

  </main>

  <!-- CARGA DEL SCRIPT -->
  <script src="../JS/gala.js" defer></script>

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
  <script src="../js/gala.js"></script>


</body>

</html>