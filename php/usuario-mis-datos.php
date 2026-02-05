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

$id = $_SESSION["id_usuario"];

$stmt = $conexion->prepare("
    SELECT nombre_completo, email, rol_participante
    FROM usuario
    WHERE id_usuario = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Usuario no encontrado"
    ]);
    exit;
}

echo json_encode([
    "ok" => true,
    "usuario" => $res->fetch_assoc()
]);
exit;
