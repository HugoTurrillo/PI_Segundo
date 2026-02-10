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

      

          <section class="home-hero-art">
        <div class="hero-art-inner">
          <span class="hero-accent"></span>

          <h1 class="hero-title">
            TALENTO<br>
            <span>UNIVERSITARIO</span>
          </h1>

          <p class="hero-subtitle">
            Festival de Cortometrajes<br>
            Universidad Europea
          </p>
        <p class="hero-word">
        <span class="word active">Cine</span>
        <span class="word">Creación</span>
        <span class="word">Talento</span>
        <span class="word">Pasión</span>
      </p>

        </div>
      </section>


    </main>

    <section class="home-news">
      <div class="home-section-inner">
        <p class="section-intro">
        <h2>Últimas noticias</h2>
        </p>
        <div id="contenedorNoticias"></div>
      </div>
    </section>


   <section class="home-premios">
  <div class="home-section-inner">

    <h2>Categorías del festival</h2>

    <div class="panel-grid">
      <a href="#" class="panel-card panel-card-categoria">
        <h3>Alumnos</h3>
        <p>Premios: 3</p>
        <p>Premio físico: 1</p>
      </a>

      <a href="#" class="panel-card panel-card-categoria">
        <h3>Alumni</h3>
        <p>Premios: 3</p>
        <p>Premio físico: 0</p>
      </a>
    </div>

  </div>
</section>


    <section class="patrocinadores-home">
      <div class="home-section-inner">
        <p class="section-intro">
      <h2>Nuestros patrocinadores</h2>
</p>
      <div id="patrocinadores-home" class="patro-grid"></div>
      </div>
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
         <a href="../HTML/sobre_nosotros.html" class="footer-contact-link">Sobre nosotros: ¿Quiénes Somos?</a><br>
                   <a href="../HTML/contacto.html" class="footer-contact-link">Contáctanos</a>

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
  <script src="../JS/home_noticias.js"></script>
  <script src="../JS/home-patrocinadores.js"></script>
<script src="../JS/home-premios.js"></script>
</body>

</html>