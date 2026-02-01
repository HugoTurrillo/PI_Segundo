<?php
require_once "../php/config/auth.php";
requireRole("organizador");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nominar candidatura</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../CSS/estilos.css">
  <link rel="icon" href="../IMG/favicon.png">
</head>

<body>

<header class="navbar">
  <div class="logo">
    <img src="../IMG/LOGOENTERO.png" alt="Logo">
  </div>

  <div class="search-bar">
    <input type="text" placeholder="Buscar...">
  </div>

  <nav>
    <ul class="nav-links">
      <li><a href="organizador.php">Panel</a></li>
      <li><a href="candidaturas.php">Candidaturas</a></li>
      <li><a href="calendario.html">Calendario</a></li>
    </ul>
  </nav>

  <div class="nav-buttons">
    <a href="../php/logout.php" class="btn nav-btn">Salir</a>
  </div>
</header>

<main class="login-container">

  <h2>Nominar candidatura a categoría</h2>

  <div id="info-candidatura" class="panel-card" style="margin-bottom:2rem;">
    Cargando candidatura…
  </div>

  <label>Categoría</label>
  <select id="categoria"></select>

  <button id="btn-nominar" class="btn login-btn" style="margin-top:1.5rem;">
    Nominar / Editar nominación
  </button>

  <p class="error-global" id="error-global"></p>

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

<script src="../JS/nominar-categoria.js"></script>

</body>
</html>
