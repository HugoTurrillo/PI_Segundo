<?php
require_once "../php/config/auth.php";
requireRole("organizador");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ganador Carrera Profesional</title>
  <link rel="stylesheet" href="../CSS/estilos.css">
  <script defer src="../JS/premios.js"></script>
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
      <li><a href="premios.php">Premios</a></li>
   </ul>
  </nav>

  <div class="nav-buttons">
      <a href="login.html" class="btn nav-btn">Salir</a>
  </div>
</header>

<main class="login-container">
  <h2>Ganador Carrera Profesional</h2>

  <form id="form-carrera" class="login-form">

    <label for="nombre">Nombre y apellidos</label>
    <input type="text" id="nombre" name="nombre" required>
    <span class="error-campo" id="error-nombre"></span>

    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email" required>
    <span class="error-campo" id="error-email"></span>

    <label for="telefono">Teléfono de contacto</label>
    <input type="tel" id="telefono" name="telefono" required>
    <span class="error-campo" id="error-telefono"></span>

    <label for="video">Vídeo del recorrido profesional</label>
    <input type="file" id="video" name="video" accept="video/*" required>
    <span class="error-campo" id="error-video"></span>

    <div class="error-global" id="error-global"></div>

    <button type="submit" class="btn login-btn">Guardar ganador</button>
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
      <p>Contacta con nosotros</p>
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


</body>
</html>