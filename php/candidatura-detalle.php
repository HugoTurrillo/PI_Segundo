<?php
require "config/conexion.php";
header("Content-Type: application/json");

$id = intval($_GET["id"] ?? 0);

$stmt = $conexion->prepare("
    SELECT 
        c.titulo_obra,
        c.nombre_contacto,
        c.email_contacto,
        u.rol_participante,
        c.sinopsis,
        c.video_ruta,
        c.portada_ruta
    FROM candidatura c
    INNER JOIN usuario u ON u.id_usuario = c.id_usuario
    WHERE c.id_candidatura = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "Candidatura no encontrada"]);
    exit;
}

echo json_encode([
    "ok" => true,
    "candidatura" => $res->fetch_assoc()
]);
exit;
