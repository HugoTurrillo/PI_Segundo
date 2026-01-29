<?php
session_start();
$logueado = isset($_SESSION["id_usuario"]);
$rol = $_SESSION["rol"] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio - Universidad Europea</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../CSS/estilos.css">
  <link rel="icon" href="../IMG/favicon.png">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
  <div class="logo">
    <img src="../IMG/LOGOENTERO.png" alt="Logo Universidad Europea">
  </div>

  <div class="search-bar">
    <input type="text" placeholder="Buscar...">
  </div>

  <nav>
    <ul class="nav-links">
      <li><a href="home.php">Inicio</a></li>
      <li><a href="../HTML/calendario.html">Calendario</a></li>

      <?php if ($logueado && $rol === "participante"): ?>
        <li><a href="../HTML/participante.html">Panel</a></li>
        <li><a href="../HTML/participante_candidatura.html">Mi candidatura</a></li>
        <li><a href="../HTML/mis_datos.html">Mis datos</a></li>
      <?php elseif ($logueado && $rol === "organizador"): ?>
        <li><a href="../HTML/organizador.html">Panel</a></li>
        <li><a href="../HTML/candidaturas.html">Candidaturas</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <div class="nav-buttons">
    <?php if (!$logueado): ?>
      <a href="../HTML/login.html" class="btn nav-btn">Acceso</a>
      <a href="../HTML/registro.html" class="btn nav-btn">Unirse</a>
    <?php else: ?>
      <a href="../php/logout.php" class="btn nav-btn">Salir</a>
    <?php endif; ?>
  </div>
</header>

<!-- ========= CONTENEDOR QUE EMPUJA EL FOOTER ========= -->
<div class="home-wrapper">

  <!-- ================= CONTENIDO PRINCIPAL ================= -->
  <main class="home-main">

    <!-- IZQUIERDA -->
    <section class="home-left">
      <h1>El talento universitario empieza aquí</h1>
      <p>
        Sube tu corto,<br>
        entra a formar parte del festival
      </p>

      <?php if (!$logueado): ?>
        <a href="../HTML/registro.html" class="btn home-btn">Crea tu cuenta gratis</a>
      <?php elseif ($rol === "participante"): ?>
        <a href="../HTML/participante.html" class="btn home-btn">Ir a mi panel</a>
      <?php elseif ($rol === "organizador"): ?>
        <a href="../HTML/organizador.html" class="btn home-btn">Ir al panel organizador</a>
      <?php endif; ?>
    </section>

    <!-- DERECHA: CARRUSEL -->
    <section class="home-carousel">
      <div class="carousel-container">

        <div class="carousel-track">
          <div class="carousel-slide"><img src="../IMG/carruselh1.jpg" alt=""></div>
          <div class="carousel-slide"><img src="../IMG/carruselh2.jpg" alt=""></div>
          <div class="carousel-slide"><img src="../IMG/carruselh3.jpg" alt=""></div>
          <div class="carousel-slide"><img src="../IMG/carruselh4.jpg" alt=""></div>
        </div>

        <button class="carousel-btn prev">&#10094;</button>
        <button class="carousel-btn next">&#10095;</button>

        <div class="carousel-dots">
          <span class="dot active"></span>
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>

      </div>
    </section>

  </main>

  

</div>

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

<script src="../JS/carrusel.js"></script>
</body>
</html>
