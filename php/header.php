<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="navbar">
  <div class="logo">
    <img src="../IMG/LOGOENTERO.png" alt="Logo Universidad Europea">
  </div>

  <div class="search-bar">
    <input type="text" placeholder="Buscar...">
  </div>

  <nav>
    <ul class="nav-links">

      <?php if (!isset($_SESSION["id_usuario"])): ?>

        <!-- USUARIO NO LOGUEADO -->
        <li><a href="../php/home.php">Inicio</a></li>
        <li><a href="../HTML/calendario.html">Calendario</a></li>

      <?php elseif ($_SESSION["rol"] === "participante"): ?>

        <!-- PARTICIPANTE -->
        <li><a href="../HTML/calendario.html">Calendario</a></li>
        <li><a href="../HTML/participante.html">Panel</a></li>
        <li><a href="../HTML/participante_candidatura.html">Mi candidatura</a></li>
        <li><a href="../HTML/mis_datos.html">Mis datos</a></li>

      <?php elseif ($_SESSION["rol"] === "organizador"): ?>

        <!-- ORGANIZADOR -->
        <li><a href="../HTML/calendario.html">Calendario</a></li>
        <li><a href="../HTML/organizador.html">Panel</a></li>
        <li><a href="../HTML/candidaturas.html">Candidaturas</a></li>
        <li><a href="../HTML/patrocinadores.html">Patrocinadores</a></li>

      <?php endif; ?>

    </ul>
  </nav>

  <div class="nav-buttons">
    <?php if (!isset($_SESSION["id_usuario"])): ?>
      <a href="../HTML/login.html" class="btn nav-btn">Acceso</a>
      <a href="../HTML/registro.html" class="btn nav-btn">Unirse</a>
    <?php else: ?>
      <a href="../php/logout.php" class="btn nav-btn">Salir</a>
    <?php endif; ?>
  </div>
</header>
