<?php
require "config/conexion.php";
header("Content-Type: application/json");

$id_categoria = intval($_GET["id_categoria"] ?? 0);

$stmt = $conexion->prepare(
    "SELECT * FROM candidatura WHERE id_categoria=?"
);
$stmt->bind_param("i", $id_categoria);
$stmt->execute();

echo json_encode(
    $stmt->get_result()->fetch_all(MYSQLI_ASSOC)
);
