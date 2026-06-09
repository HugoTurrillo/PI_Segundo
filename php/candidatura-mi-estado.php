<?php
/**
 * Devuelvo las candidaturas del usuario logueado para que vea su estado en el panel de participante.
 */

require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

session_start();
$id_usuario = $_SESSION["id_usuario"] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "No autenticado"]);
    exit;
}

$sql = "SELECT 
            id_candidatura, 
            titulo_obra, 
            sinopsis, 
            estado, 
            motivo_rechazo, 
            video_ruta,
            portada_ruta
        FROM candidatura
        WHERE id_usuario = ?
        ORDER BY id_candidatura DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

echo json_encode([
    "ok" => true,
    "candidaturas" => $res->fetch_all(MYSQLI_ASSOC)
]);