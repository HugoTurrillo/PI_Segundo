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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

 <footer class="footer">

  <div class="footer-main">

    
    <div class="footer-col footer-brand">
      <img src="../IMG/logo-ue-blanco.png" alt="Festival Universitario" class="footer-logo">
      <p class="footer-tagline">Talento universitario en acción</p>
    </div>

    
    <div class="footer-col footer-links">
      <a href="quienes-somos.html">Quiénes somos</a>
      <a href="contacto.html">Contacta con nosotros</a>
    </div>

    <!-- COLUMNA 3 · REDES -->
    <div class="footer-col footer-social">
      <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
      <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      <a href="#" aria-label="X"><i class="fab fa-x-twitter"></i></a>
    </div>

  </div>

  <!-- FRANJA ROJA -->
  <div class="footer-bottom">
    © 2026 Festival Universitario
  </div>

</footer>

  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../JS/home_noticias.js"></script>
  <script src="../JS/home-patrocinadores.js"></script>
<script src="../JS/home-premios.js"></script>
</body>

</html>