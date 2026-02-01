<?php
// php/config/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Exige que el usuario esté logueado
 */
function requireLogin()
{
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: ../php/home.php");
        exit;
    }
}

/**
 * Exige un rol concreto
 * @param string $rolRequerido
 */
function requireRole(string $rolRequerido)
{
    requireLogin();

    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== $rolRequerido) {
        header("Location: ../php/home.php");
        exit;
    }
}
