<?php
require_once "../php/config/auth.php";
requireRole("organizador");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Premios - Panel Organizador</title>
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
      <li><a href="organizador.html">Panel</a></li>
     <li><a href="calendario.html">Calendario</a></li>
      <li><a href="#">Sobre nosotros</a></li>
   </ul>
  </nav>

  <div class="nav-buttons">
      <a href="login.html" class="btn nav-btn">Salir</a>
  </div>
</header>

<main class="login-container">
  
  <h2>Gestión de premios</h2>

  <!-- ============================
       CATEGORÍAS
  ============================= -->
  <section style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
      <h3 style="color: #d32f2f; font-size: 1.4rem;">Categorías y premios</h3>
      <a href="categoria-nueva.html" class="btn login-btn" style="padding: 0.6rem 1.2rem;">Añadir categoría</a>
    </div>
  </section>

  <div class="panel-grid">

    <div class="panel-card">
      <h3>Alumnos</h3>
      <p>Premios: 1º, 2º y 3º</p>
      <p>Premio físico: Cámara Canon (solo 1º premio)</p>
      <div style="margin-top: 1rem; display:flex; gap:1rem;">
        <a href="categoria-editar.html" class="btn login-btn" style="padding:0.5rem 1rem;">Editar</a>
        <button class="btn login-btn" style="padding:0.5rem 1rem; background:#555;">Eliminar</button>
      </div>
    </div>

    <div class="panel-card">
      <h3>Alumni</h3>
      <p>Premios: 1º y 2º</p>
      <p>Premio físico: No</p>
      <div style="margin-top: 1rem; display:flex; gap:1rem;">
        <a href="categoria-editar.html" class="btn login-btn" style="padding:0.5rem 1rem;">Editar</a>
        <button class="btn login-btn" style="padding:0.5rem 1rem; background:#555;">Eliminar</button>
      </div>
    </div>

    <div class="panel-card">
      <h3>Carrera Profesional</h3>
      <p>Premios: 1</p>
      <p>Premio físico: No</p>
      <div style="margin-top: 1rem; display:flex; gap:1rem;">
        <a href="categoria-editar.html" class="btn login-btn" style="padding:0.5rem 1rem;">Editar</a>
        <button class="btn login-btn" style="padding:0.5rem 1rem; background:#555;">Eliminar</button>
      </div>
    </div>

  </div>

  <hr class="home-separator">

  <!-- ============================
       GANADORES
  ============================= -->
  <section style="margin-top: 3rem;">
    <h3 style="color: #d32f2f; font-size: 1.4rem; margin-bottom: 1rem;">Ganadores</h3>

    <div class="panel-grid">

      <div class="panel-card">
        <h3>Ganadores Alumnos</h3>
        <p>Seleccionar ganadores desde la base de datos de participantes.</p>
        <br>
        <a href="ganador-carrera-profesional.html" class="btn login-btn" style="margin-top: 1rem;">Asignar ganadores</a>
      </div>

      <div class="panel-card">
        <h3>Ganadores Alumni</h3>
        <p>Seleccionar ganadores desde la base de datos de participantes.</p>
        <br>
        <a href="ganador-carrera-profesional.html" class="btn login-btn" style="margin-top: 1rem;">Asignar ganadores</a>
      </div>

      <div class="panel-card">
        <h3>Carrera Profesional</h3>
        <p>Asignar ganador mediante formulario personalizado.</p>
        <br>
        <a href="ganador-carrera-profesional.html" class="btn login-btn" style="margin-top: 1rem;">Asignar ganador</a>
      </div>

    </div>
  </section>

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


</body>
</html>