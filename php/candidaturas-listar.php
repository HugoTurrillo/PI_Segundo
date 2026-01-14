<?php
require "conexion.php";

header("Content-Type: application/json");

$stmt = $pdo->query("
    SELECT id_candidatura, titulo_obra, nombre_contacto, email_contacto, estado, motivo_rechazo
    FROM candidatura
    ORDER BY fecha_creacion DESC
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
