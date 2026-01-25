<?php
session_start();
require __DIR__ . "/config/conexion.php";

header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No autenticado"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data["nombre"] ?? "");
$password = $data["password"] ?? "";

if ($nombre === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "El nombre es obligatorio"
    ]);
    exit;
}

$id = $_SESSION["id_usuario"];

if ($password !== "") {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("
        UPDATE usuario
        SET nombre_completo = ?, password_hash = ?
        WHERE id_usuario = ?
    ");
    $stmt->bind_param("ssi", $nombre, $hash, $id);
} else {
    $stmt = $conexion->prepare("
        UPDATE usuario
        SET nombre_completo = ?
        WHERE id_usuario = ?
    ");
    $stmt->bind_param("si", $nombre, $id);
}

$stmt->execute();

echo json_encode([
    "ok" => true
]);
exit;
