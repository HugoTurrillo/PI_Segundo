<?php
require "conexion.php";

header("Content-Type: application/json");

$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

$id = $datos["id"] ?? null;
$motivo = $datos["motivo"] ?? "";

if ($id && $motivo !== "") {
    $stmt = $pdo->prepare("
        UPDATE candidatura 
        SET estado='rechazada', motivo_rechazo=? 
        WHERE id_candidatura=?
    ");
    $stmt->execute([$motivo, $id]);

    echo json_encode(["ok" => true]);
    exit;
}

echo json_encode(["ok" => false]);
exit;
