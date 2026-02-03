<?php
require_once "../php/config/auth.php";
requireRole("organizador");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asignar ganador</title>
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

  <h2>Asignar ganador</h2>

  <label>Categoría:</label>
  <select id="select_categoria" required style="margin-bottom:1rem;"></select>

  <div id="info-categoria" class="panel-card" style="padding:1.2rem; margin-bottom:1.5rem;">   Cargando categoría...
    Selecciona una categoría...  
    </div>

  <form id="form-ganador" class="panel-card" style="padding:1.5rem;">
    
    <label>Premio:</label>
    <select id="numero_premio" required></select>

    <label>Nominado ganador:</label>
    <select id="id_candidatura" required></select>

    <button class="btn login-btn" type="submit" style="margin-top:1rem;">
      Guardar ganador
    </button>
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

<script src="../JS/ganador-asignar.js"></script>

</body>
</html>
