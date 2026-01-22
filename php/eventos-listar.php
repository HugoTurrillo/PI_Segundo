<?php
// php/evento-listar.php

session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Preparar consulta
    $stmt = $conexion->prepare("SELECT * FROM evento ORDER BY fecha ASC");

    if (!$stmt) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error en prepare(): " . $conexion->error
        ]);
        exit;
    }

    // Ejecutar
    if (!$stmt->execute()) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error al obtener los eventos: " . $stmt->error
        ]);
        $stmt->close();
        exit;
    }

    // Obtener resultados
    $resultado = $stmt->get_result();
    $eventos = $resultado->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    echo json_encode([
        "ok" => true,
        "eventos" => $eventos
    ]);
    exit;

} else {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
    exit;
}
