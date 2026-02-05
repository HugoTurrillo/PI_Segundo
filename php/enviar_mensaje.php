<?php
require "config/conexion.php";
header("Content-Type: application/json");

// Leer JSON recibido
$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data["nombre"] ?? "");
$mensaje = trim($data["mensaje"] ?? "");

// Validación básica
if ($nombre === "" || $mensaje === "") {
    echo json_encode([
        "ok" => false,
        "msg" => "Debes completar todos los campos."
    ]);
    exit;
}

// Insertar en la BD
$stmt = $conexion->prepare("
    INSERT INTO mensajes (nombre, mensaje)
    VALUES (?, ?)
");
$stmt->bind_param("ss", $nombre, $mensaje);

if ($stmt->execute()) {
    echo json_encode([
        "ok" => true,
        "msg" => "Mensaje enviado correctamente."
    ]);
} else {
    echo json_encode([
        "ok" => false,
        "msg" => "Error al enviar el mensaje."
    ]);
}

exit;
