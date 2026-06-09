<?php
/**
 * Elimino un evento por ID; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $entrada = file_get_contents("php://input");
    $datos = json_decode($entrada, true);

    if (!$datos) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Datos no válidos."
        ]);
        exit;
    }

    $id = intval($datos["id"] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "ID no recibido o no válido."
        ]);
        exit;
    }

    $stmt = $conexion->prepare("DELETE FROM evento WHERE id = ?");

    if (!$stmt) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error en prepare(): " . $conexion->error
        ]);
        exit;
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Error al eliminar el evento: " . $stmt->error
        ]);
        $stmt->close();
        exit;
    }

    $stmt->close();

    echo json_encode([
        "ok" => true,
        "mensaje" => "Evento eliminado correctamente."
    ]);
    exit;

} else {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
    exit;
}
