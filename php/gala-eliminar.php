<?php
/**
 * Elimino la gala y sus secciones por ID; solo organizador. Acepto POST (JSON id) o GET (id).
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$method = $_SERVER["REQUEST_METHOD"];

$id = 0;

if ($method === "POST") {
    $entrada = file_get_contents("php://input");
    $datos = json_decode($entrada, true);
    $id = intval($datos["id"] ?? 0);
} elseif ($method === "GET") {
    $id = intval($_GET["id"] ?? 0);
} else {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

// Obtener imagen
$stmt = $conexion->prepare("SELECT imagen FROM gala WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$evento = $resultado->fetch_assoc();

// Borrar imagen
if ($evento && !empty($evento["imagen"])) {
    $ruta = "../uploads/" . $evento["imagen"];
    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

// Borrar evento
$stmt = $conexion->prepare("DELETE FROM gala WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode([
    "ok" => true,
    "msg" => "Evento eliminado correctamente"
]);
