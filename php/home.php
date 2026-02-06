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
  <?php include "header.php"; ?>

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

    <section class="home-news">
      <h2>Últimas noticias</h2>
      <div id="contenedorNoticias"></div>
    </section>

    <section class="patrocinadores-home">
      <h2>Nuestros patrocinadores</h2>
      <div id="patrocinadores-home" class="patro-grid"></div>
    </section>

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
  <script src="../JS/carrusel.js"></script>
  <script src="../JS/home_noticias.js"></script>
  <script src="../JS/home-patrocinadores.js"></script>

</body>

</html>