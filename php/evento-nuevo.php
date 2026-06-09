<?php
/**
 * Creo un nuevo evento con título, fecha, hora y descripción; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

if (!$datos) {
    echo json_encode(["ok" => false, "mensaje" => "Datos no válidos"]);
    exit;
}

$titulo = trim($datos["titulo"] ?? "");
$fecha = trim($datos["fecha"] ?? "");
$hora = trim($datos["hora"] ?? "");
$descripcion = trim($datos["descripcion"] ?? "");

if ($titulo === "" || $fecha === "" || $hora === "" || $descripcion === "") {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

/* COMPROBAR DUPLICADO */
$stmt = $conexion->prepare(
    "SELECT id FROM evento WHERE fecha = ? AND hora = ?"
);
$stmt->bind_param("ss", $fecha, $hora);
$stmt->execute();
$stmt->store_result();

$hayDuplicado = $stmt->num_rows > 0;
$stmt->close();

/* SI HAY DUPLICADO Y NO VIENE forzar=1 → PEDIR CONFIRMACIÓN */
if ($hayDuplicado && !isset($_GET["forzar"])) {
    echo json_encode([
        "ok" => false,
        "confirmar" => true,
        "mensaje" => "Ya existe un evento ese día y a esa hora. ¿Quieres crearlo igualmente?"
    ]);
    exit;
}

/* INSERTAR (normal o forzado) */
$stmt = $conexion->prepare(
    "INSERT INTO evento (titulo, fecha, hora, descripcion)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $titulo, $fecha, $hora, $descripcion);

if (!$stmt->execute()) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Error al crear el evento"
    ]);
    exit;
}

$stmt->close();

echo json_encode(["ok" => true]);
exit;
