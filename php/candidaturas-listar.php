<?php
require "config/conexion.php";
header("Content-Type: application/json");

$stmt = $conexion->prepare(
    "SELECT id_candidatura, titulo_obra, nombre_contacto,
            email_contacto, estado, motivo_rechazo
     FROM candidatura
     ORDER BY fecha_creacion DESC"
);
$stmt->execute();
$res = $stmt->get_result();

echo json_encode($res->fetch_all(MYSQLI_ASSOC));
