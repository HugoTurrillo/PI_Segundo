<?php
// php/proteger_organizador.php
session_start();

// Si no hay sesión → fuera
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../HTML/home.html");
    exit;
}

// Si NO es organizador → fuera
if ($_SESSION["rol"] !== "organizador") {
    header("Location: ../HTML/home.html");
    exit;
}
?>
