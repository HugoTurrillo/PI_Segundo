<?php
require "config/conexion.php";
header("Content-Type: application/json");

$stmt = $conexion->prepare("
    SELECT 
        c.id_candidatura,
        c.titulo_obra,
        c.nombre_contacto,
        c.email_contacto,
        c.estado,
        c.motivo_rechazo,
        c.id_categoria,
        cat.nombre AS categoria_nombre
    FROM candidatura c
    LEFT JOIN categorias cat ON cat.id = c.id_categoria
    ORDER BY c.fecha_creacion DESC
");

$stmt->execute();
$res = $stmt->get_result();

echo json_encode($res->fetch_all(MYSQLI_ASSOC));
exit;
