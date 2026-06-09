<?php
/**
 * Devuelvo una noticia por ID para el popup del home; lo dejé público para que cualquier visitante pueda ver el detalle.
 */

require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$id = intval($_POST["id_noticia"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM noticia WHERE id_noticia = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    echo json_encode([
        "ok" => true,
        "noticia" => $res->fetch_assoc()
    ]);
    exit;
}

echo json_encode(["ok" => false, "msg" => "Noticia no encontrada"]);