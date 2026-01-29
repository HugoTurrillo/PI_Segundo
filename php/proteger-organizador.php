<?php
session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "organizador") {
    header("Location: home.php");
    exit();
}
