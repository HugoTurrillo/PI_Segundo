<?php
// php/proteger.php

session_start();

// Si NO hay sesión iniciada, redirigir al home
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // o home.php si lo tienes
    exit();
}
?>