<?php
/**
 * Elimino un patrocinador por ID y su logo del disco; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

if (!isset($_GET["id"])) {
    echo json_encode(["ok" => false, "msg" => "ID no recibido"]);
    exit();
}

$id = intval($_GET["id"]);
if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID inválido"]);
    exit();
}

// ============================
// OBTENER LOGO ACTUAL
// ============================
$stmt = $conexion->prepare("SELECT logo_ruta FROM patrocinador WHERE id_patrocinador = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "Patrocinador no encontrado"]);
    exit();
}

$patro = $resultado->fetch_assoc();
$stmt->close();

// ============================
// ELIMINAR ARCHIVO SI EXISTE
// ============================
$ruta = "../uploads/" . $patro["logo_ruta"];
if ($patro["logo_ruta"] && file_exists($ruta)) {
    @unlink($ruta);
}

// ============================
// ELIMINAR REGISTRO
// ============================
$stmt = $conexion->prepare("DELETE FROM patrocinador WHERE id_patrocinador = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "msg" => "Error al eliminar el patrocinador"]);
    exit();
}

$stmt->close();

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode(["ok" => true, "msg" => "Patrocinador eliminado correctamente"]);
?>
