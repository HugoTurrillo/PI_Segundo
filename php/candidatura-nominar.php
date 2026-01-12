<?php
require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_candidatura = $data["id_candidatura"];
$id_categoria = $data["id_categoria"];

$stmt = $pdo->prepare("UPDATE candidatura SET id_categoria=? WHERE id_candidatura=?");
$stmt->execute([$id_categoria, $id_candidatura]);

echo json_encode(["ok" => true]);
