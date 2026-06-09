<?php
/**
 * Header HTML para páginas estáticas en HTML/ (cargado por navbar.js).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . "/nav.php";

header("Content-Type: text/html; charset=utf-8");

$pagina = isset($_GET["pagina"]) ? basename($_GET["pagina"]) : null;
renderSiteHeader(true, $pagina);
