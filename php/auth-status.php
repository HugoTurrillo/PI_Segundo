<?php
/**
 * Devuelvo en JSON si hay sesión activa y el rol del usuario para que el front pueda proteger páginas.
 */

session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "ok" => false,
        "auth" => false
    ]);
    exit;
}

echo json_encode([
    "ok" => true,
    "auth" => true,
    "id_usuario" => $_SESSION["id_usuario"],
    "rol" => $_SESSION["rol"]
]);
