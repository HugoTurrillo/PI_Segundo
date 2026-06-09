<?php
/**
 * Creo una nueva categoría con nombre y premios; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

// Leer JSON enviado desde fetch()
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["ok" => false, "msg" => "No se recibieron datos"]);
    exit;
}

// Extraer datos
$nombre = trim($data["nombre"] ?? "");
$premios = trim($data["premios"] ?? "");
$premio_fisico = trim($data["premio_fisico"] ?? "");

// Validación
if ($nombre === "" || $premios === "" || $premio_fisico === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios"]);
    exit;
}

// CONSULTA CORRECTA PARA TABLA "categorias"
$sql = "INSERT INTO categorias (nombre, premios, premio_fisico) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);

// Si falla el prepare, mostrar error SQL
if (!$stmt) {
    echo json_encode([
        "ok" => false,
        "msg" => "Error en prepare()",
        "sql_error" => $conexion->error,
        "sql" => $sql
    ]);
    exit;
}

$stmt->bind_param("sss", $nombre, $premios, $premio_fisico);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "msg" => "Categoría creada correctamente"]);
} else {
    echo json_encode([
        "ok" => false,
        "msg" => "Error al insertar",
        "sql_error" => $stmt->error
    ]);
}

$stmt->close();
exit;
