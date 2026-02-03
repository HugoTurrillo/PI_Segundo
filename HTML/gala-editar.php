<?php
require_once "../php/config/auth.php";
requireRole("organizador");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar evento de gala</title>
  <link rel="stylesheet" href="../CSS/estilos.css">
  <script defer src="../JS/gala.js"></script>
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
      <li><a href="gala.php">Gala</a></li>
   </ul>
  </nav>

  <div class="nav-buttons">
      <a href="login.html" class="btn nav-btn">Salir</a>
  </div>
</header>

<main class="login-container">
  <h2>Editar evento de gala</h2>

  <form id="form-gala" class="login-form">

    <label for="titulo">Título del evento</label>
    <input type="text" id="titulo" name="titulo" required>
    <span class="error-campo" id="error-titulo"></span>

    <label for="fecha">Fecha</label>
    <input type="date" id="fecha" name="fecha" required>
    <span class="error-campo" id="error-fecha"></span>

    <label for="hora">Hora</label>
    <input type="time" id="hora" name="hora" required>
    <span class="error-campo" id="error-hora"></span>

    <label for="lugar">Lugar</label>
    <input type="text" id="lugar" name="lugar" required>
    <span class="error-campo" id="error-lugar"></span>

    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="4" style="width:100%; padding:0.6rem; border:1px solid #ccc; border-radius:4px;"></textarea>
    <span class="error-campo" id="error-descripcion"></span>

    <label for="imagen">Imagen del evento (opcional)</label>
    <input type="file" id="imagen" name="imagen" accept="image/*">
    <span class="error-campo" id="error-imagen"></span>

    <div class="error-global" id="error-global"></div>

    <button type="submit" class="btn login-btn">Guardar cambios</button>
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
<script src="../js/gala.js"></script>


</body>
</html>