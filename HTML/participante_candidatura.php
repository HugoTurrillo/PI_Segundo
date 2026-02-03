<?php
require_once "../php/config/auth.php";
requireRole("participante");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Mi candidatura</title>
  <link rel="stylesheet" href="../CSS/estilos.css">
</head>

<body>

  <header class="navbar">
    <div class="logo">
      <img src="../IMG/LOGOENTERO.png" alt="Logo">
    </div>

    <nav>
      <ul class="nav-links">
        <li><a href="participante.php">Panel</a></li>
      </ul>
    </nav>

    <div class="nav-buttons">
      <a href="../php/logout.php" class="btn nav-btn">Salir</a>
    </div>
  </header>

  <main class="login-container">

    <h2>Mi candidatura</h2>

    <div id="sinCandidatura" style="display:none;">
      <p>No has enviado ninguna candidatura todavía.</p>
      <a href="form_inscripcion.php" class="btn nav-btn">Enviar candidatura</a>
    </div>

    <div id="conCandidatura" style="display:none;">

      <h3 id="titulo"></h3>
      <p><strong>Estado:</strong> <span id="estado"></span></p>

      <p><strong>Descripción:</strong></p>
      <p id="sinopsis"></p>

      <div id="rechazoBox" style="display:none;">
        <p style="color:red;"><strong>Motivo del rechazo:</strong></p>
        <p id="motivoRechazo"></p>
      </div>

      <div id="subsanarBox" style="display:none; margin-top:1rem;">
        <h4>Subsanar candidatura</h4>

        <textarea id="mensajeSubsanacion"
          rows="4"
          placeholder="Explica qué has corregido..."
          style="width:100%;"></textarea>

        <button id="btnSubsanar" class="btn nav-btn" style="margin-top:0.5rem;">
          Enviar subsanación
        </button>

        <p id="subsanarError" style="color:red;"></p>
      </div>

    </div>

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
  <script src="../JS/auth.js"></script>
  <script src="../JS/participante-candidatura.js"></script>
</body>

</html>