<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

// ============================
// VALIDAR SESIÓN
// ============================
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "participante") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No autorizado"
    ]);
    exit;
}

// ============================
// LEER JSON
// ============================
$data = json_decode(file_get_contents("php://input"), true);

$titulo = trim($data["titulo_obra"] ?? "");
$sinopsis = trim($data["sinopsis"] ?? "");
$nombre = trim($data["nombre_contacto"] ?? "");
$email = trim($data["email_contacto"] ?? "");
$dni = strtoupper(trim($data["dni"] ?? ""));

// ============================
// VALIDAR CAMPOS
// ============================
if ($titulo === "" || $dni === "" || $email === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Faltan datos obligatorios"
    ]);
    exit;
}

// ============================
// OBTENER EDICIÓN ACTIVA
// ============================
$res = $conexion->query(
    "SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1"
);
$edicion = $res->fetch_assoc();

if (!$edicion) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No hay edición activa"
    ]);
    exit;
}

// ============================
// INSERTAR CANDIDATURA
// ============================
$stmt = $conexion->prepare("
    INSERT INTO candidatura
    (id_usuario, id_edicion, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisssss",
    $_SESSION["id_usuario"],
    $edicion["id_edicion"],
    $titulo,
    $sinopsis,
    $nombre,
    $email,
    $dni
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true]);
exit;
