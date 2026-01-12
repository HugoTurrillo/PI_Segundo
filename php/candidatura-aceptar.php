<?php
require "conexion.php";

$id = $_GET["id"];

$stmt = $pdo->prepare("UPDATE candidatura SET estado='aceptada' WHERE id_candidatura=?");
$stmt->execute([$id]);

echo json_encode(["ok" => true]);
