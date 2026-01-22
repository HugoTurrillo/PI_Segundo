<?php
require "config/conexion.php";
header("Content-Type: application/json");

$stmt = $conexion->prepare(
    "SELECT * FROM noticia ORDER BY id_noticia DESC"
);
$stmt->execute();

$res = $stmt->get_result();
echo json_encode($res->fetch_all(MYSQLI_ASSOC));
