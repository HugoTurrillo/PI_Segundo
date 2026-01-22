<?php
// php/evento-detalle.php

session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Validar ID recibido
    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "ID no recibido o no válido."
        ]);
        exit;
    }

    // Preparar consulta
    $stmt = $conexion->prepare("SELECT * FROM evento WHERE id = ?");

    if (!$stmt) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error en prepare(): " . $conexion->error
        ]);
        exit;
    }

    // Enlazar parámetro
    $stmt->bind_param("i", $id);

    // Ejecutar
    if (!$stmt->execute()) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error al obtener el evento: " . $stmt->error
        ]);
        $stmt->close();
        exit;
    }

    // Obtener resultado
    $resultado = $stmt->get_result();
    $evento = $resultado->fetch_assoc();

    $stmt->close();

    echo json_encode([
        "ok" => true,
        "evento" => $evento ?: null
    ]);
    exit;

} else {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
    exit;
}
