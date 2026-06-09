<?php
/**
 * Elimino una categoría por ID si no es base ni tiene ganadores; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

/* PROTECCIÓN: comprobar si es categoría base */
$stmt = $conexion->prepare("SELECT es_base FROM categorias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "Categoría no encontrada"]);
    exit;
}

$cat = $res->fetch_assoc();

if ($cat["es_base"] == 1) {
    echo json_encode([
        "ok" => false,
        "msg" => "Esta categoría no se puede eliminar"
    ]);
    exit;
}

/* ELIMINAR */
$stmt = $conexion->prepare("DELETE FROM categorias WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode([
    "ok" => true,
    "msg" => "Categoría eliminada correctamente"
]);
