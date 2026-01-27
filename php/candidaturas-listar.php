<?php
require "config/conexion.php";
header("Content-Type: application/json");

$stmt = $conexion->prepare("
    SELECT 
        c.id_candidatura,
        c.titulo_obra,
        c.ficha_tecnica,
        c.cartel,
        c.expediente,
        c.video,
        c.sinopsis,
        c.nombre_contacto,
        c.email_contacto,
        c.dni,
        c.estado,
        c.motivo_rechazo,
        c.id_categoria,
        cat.nombre AS categoria_nombre,
        c.fecha_creacion
    FROM candidatura c
    LEFT JOIN categorias cat ON cat.id = c.id_categoria
    ORDER BY c.fecha_creacion DESC
");

$stmt->execute();
$res = $stmt->get_result();

$candidaturas = $res->fetch_all(MYSQLI_ASSOC);

$stmt->close();

echo json_encode([
    "ok" => true,
    "candidaturas" => $candidaturas
]);
exit;