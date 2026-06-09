<?php
/**
 * Funciones de autenticación que he implementado para proteger páginas y APIs.
 * Uso la sesión para comprobar si el usuario está logueado y su rol.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Exijo que el usuario esté logueado; si no, redirijo al home.
 */
function requireLogin()
{
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: ../php/home.php");
        exit;
    }
}

/**
 * Exijo un rol concreto (ej. organizador o participante); si no coincide, redirijo al home.
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

/**
 * Para APIs en JSON: exigo que sea organizador. Si no lo es, devuelvo 401 y JSON (no redirijo).
 */
function requireApiOrganizer()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "organizador") {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(401);
        echo json_encode(["ok" => false, "msg" => "No autorizado"]);
        exit;
    }
}

/**
 * Para APIs en JSON: exigo que el usuario esté logueado (cualquier rol). Si no, devuelvo 401 y JSON.
 */
function requireApiLogin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["id_usuario"])) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(401);
        echo json_encode(["ok" => false, "msg" => "No autorizado"]);
        exit;
    }
}
