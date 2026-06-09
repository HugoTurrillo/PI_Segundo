<?php
/**
 * Barra de navegación para páginas PHP (p. ej. home.php).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . "/nav.php";
renderSiteHeader(false, basename($_SERVER["SCRIPT_NAME"] ?? ""));
