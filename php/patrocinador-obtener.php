<?php
require "config/conexion.php";
header("Content-Type: application/json");

// ============================
// VALIDAR ID
// ============================
if (!isset($_GET["id"])) {
    echo json_encode(["error" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id"]);
if ($id <= 0) {
    echo json_encode(["error" => "ID inválido"]);
    exit();
}

// ============================
// CONSULTA PREPARADA
// ============================
$stmt = $conexion->prepare("SELECT * FROM patrocinador WHERE id_patrocinador = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$patro = $resultado->fetch_assoc();

$stmt->close();

// ============================
// RESPUESTA
// ============================
echo json_encode($patro ?: []);
?>
