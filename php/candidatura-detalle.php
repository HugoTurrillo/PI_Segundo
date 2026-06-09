<?php
/**
 * Devuelvo los datos de una candidatura (título, autor, sinopsis, vídeo, portada) para mostrarlos en el popup; solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

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
