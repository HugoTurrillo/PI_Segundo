<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: home.php");
    exit();
}
