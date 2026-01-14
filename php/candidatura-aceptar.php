<?php
require "conexion.php";

$id = $_GET["id"] ?? null;

if ($id) {
    $stmt = $pdo->prepare("UPDATE candidatura SET estado='aceptada' WHERE id_candidatura=?");
    $stmt->execute([$id]);
}

header("Location: ../HTML/participantes.html");
exit;
