<?php
// php/evento-crear.php

session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Leer JSON del cuerpo de la petición
    $entrada = file_get_contents("php://input");
    $datos = json_decode($entrada, true);

    if (!$datos) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Datos no válidos."
        ]);
        exit;
    }

    // Recoger datos
    $titulo      = trim($datos["titulo"] ?? "");
    $fecha       = trim($datos["fecha"] ?? "");
    $descripcion = trim($datos["descripcion"] ?? "");

    // Validación
    if ($titulo === "" || $fecha === "" || $descripcion === "") {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Todos los campos son obligatorios."
        ]);
        exit;
    }

    // Preparar consulta
    $stmt = $conexion->prepare("
        INSERT INTO evento (titulo, fecha, descripcion)
        VALUES (?, ?, ?)
    ");

    if (!$stmt) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error en prepare(): " . $conexion->error
        ]);
        exit;
    }

    // Enlazar parámetros
    $stmt->bind_param("sss", $titulo, $fecha, $descripcion);

    // Ejecutar
    if (!$stmt->execute()) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error al crear el evento: " . $stmt->error
        ]);
        $stmt->close();
        exit;
    }

    $stmt->close();

    // Respuesta final
    echo json_encode([
        "ok" => true,
        "mensaje" => "Evento creado correctamente."
    ]);
    exit;

} else {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
    exit;
}
