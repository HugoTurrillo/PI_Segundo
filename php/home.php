<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio - Universidad Europea</title>

  <link rel="stylesheet" href="../CSS/estilos.css">
  <link rel="icon" href="../IMG/favicon.png">
</head>

<body>

<?php include __DIR__ . "/header.php"; ?>

<main class="home-main">

  <!-- IZQUIERDA -->
  <section class="home-left">
    <h1>El talento universitario empieza aquí</h1>
    <p>Sube tu corto,<br>entra a formar parte del festival</p>

    <?php if (!isset($_SESSION["id_usuario"])): ?>
      <a href="../HTML/registro.html" class="btn home-btn">
        Crea tu cuenta gratis
      </a>
    <?php endif; ?>
  </section>

  <!-- DERECHA -->
  <section class="home-carousel">
    <div class="carousel-container">

      <div class="carousel-track">
        <div class="carousel-slide"><img src="../IMG/carruselh1.jpg"></div>
        <div class="carousel-slide"><img src="../IMG/carruselh2.jpg"></div>
        <div class="carousel-slide"><img src="../IMG/carruselh3.jpg"></div>
        <div class="carousel-slide"><img src="../IMG/carruselh4.jpg"></div>
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

<!-- FOOTER -->
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
