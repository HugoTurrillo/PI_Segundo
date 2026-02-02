<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

$id_categoria = intval($_POST["id_categoria"] ?? 0);
$numero_premio = intval($_POST["numero_premio"] ?? 0);
$id_candidatura = intval($_POST["id_candidatura"] ?? 0);

if ($id_categoria <= 0 || $numero_premio <= 0 || $id_candidatura <= 0) {
    echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
    exit;
}

// Comprobar duplicado
$stmt = $conexion->prepare("
    SELECT id_ganador 
    FROM ganadores 
    WHERE id_categoria = ? AND numero_premio = ?
");
$stmt->bind_param("ii", $id_categoria, $numero_premio);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["ok" => false, "error" => "Ese premio ya tiene ganador"]);
    exit;
}

// Insertar ganador
$stmt = $conexion->prepare("
    INSERT INTO ganadores (id_categoria, numero_premio, id_candidatura)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iii", $id_categoria, $numero_premio, $id_candidatura);
$stmt->execute();

echo json_encode(["ok" => true]);

