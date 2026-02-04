<?php
require "config/conexion.php";
header("Content-Type: application/json");

$sql = "SELECT * FROM gala LIMIT 1";
$res = $conexion->query($sql);

if ($res && $res->num_rows > 0) {
    echo json_encode([
        "ok" => true,
        "data" => $res->fetch_assoc()
    ]);
} else {
    echo json_encode([
        "ok" => false,
        "msg" => "No existe ninguna gala"
    ]);
}
