<?php
include("conexion.php");
header("Content-Type: application/json");

$id_categoria = $_POST["id_categoria"] ?? null;
$numero_premio = $_POST["numero_premio"] ?? null;
$id_candidatura = $_POST["id_candidatura"] ?? null;

if (!$id_categoria || !$numero_premio || !$id_candidatura) {
    echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
    exit();
}

// Evitar duplicados
$stmt = $pdo->prepare("
    SELECT * FROM ganadores 
    WHERE id_categoria = ? AND numero_premio = ?
");
$stmt->execute([$id_categoria, $numero_premio]);

if ($stmt->fetch()) {
    echo json_encode(["ok" => false, "error" => "Ese premio ya tiene ganador"]);
    exit();
}

// Insertar ganador
$stmt = $pdo->prepare("
    INSERT INTO ganadores (id_categoria, numero_premio, id_candidatura)
    VALUES (?, ?, ?)
");
$stmt->execute([$id_categoria, $numero_premio, $id_candidatura]);

echo json_encode(["ok" => true]);
