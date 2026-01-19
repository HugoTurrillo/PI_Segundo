<?php
include("conexion.php");
header("Content-Type: application/json");

if (!isset($_GET["id"])) {
    echo json_encode(["ok" => false, "msg" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id"]);

// Obtener logo actual
$stmt = $pdo->prepare("SELECT logo_ruta FROM patrocinador WHERE id_patrocinador = ?");
$stmt->execute([$id]);
$patro = $stmt->fetch(PDO::FETCH_ASSOC);

// Eliminar archivo si existe
if ($patro && file_exists("../uploads/" . $patro["logo_ruta"])) {
    unlink("../uploads/" . $patro["logo_ruta"]);
}

// Eliminar registro
$stmt = $pdo->prepare("DELETE FROM patrocinador WHERE id_patrocinador = ?");
$stmt->execute([$id]);

echo json_encode(["ok" => true, "msg" => "Patrocinador eliminado"]);
?>