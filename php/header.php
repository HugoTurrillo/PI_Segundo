<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<header class="navbar">
  <div class="logo">
    <img src="../IMG/LOGOENTERO.png" alt="Logo Universidad Europea">
  </div>



   
    <div class="nav-right">

      <?php if (!isset($_SESSION["id_usuario"])): ?>

        <!-- 🔹 NO LOGUEADO -->
        <a href="../HTML/calendario.html">Calendario</a>
       
      <?php elseif ($_SESSION["rol"] === "participante"): ?>

        <!-- 🔹 PARTICIPANTE -->
        <a href="../HTML/participante.html"  style="text-decoration: none; color: rgb(30, 30, 30);">Panel</a>
        <a href="../HTML/calendario.html"  style="text-decoration: none; color: rgb(30, 30, 30);">Calendario</a>
       

      <?php elseif ($_SESSION["rol"] === "organizador"): ?>

        <!-- 🔹 ORGANIZADOR -->
        <a href="../HTML/organizador.html" style="text-decoration: none; color: rgb(30, 30, 30);">Panel</a>
      

      <?php endif; ?>

  </div>
   

  <div class="nav-buttons">
    <?php if (!isset($_SESSION["id_usuario"])): ?>
      <a href="../HTML/login.html" class="btn nav-btn">Acceso</a>
      <a href="../HTML/registro.html" class="btn nav-btn">Unirse</a>
    <?php else: ?>
      <a href="../php/logout.php" class="btn nav-btn">Salir</a>
    <?php endif; ?>
  </div>
</header>