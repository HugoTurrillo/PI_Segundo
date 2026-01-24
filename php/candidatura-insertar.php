<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

// ============================
// VALIDAR MÉTODO
// ============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido"
    ]);
    exit;
}

// ============================
// VALIDAR SESIÓN PARTICIPANTE
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
$dni = strtoupper(trim($data["dni"] ?? ""));

// ============================
// VALIDAR CAMPOS
// ============================
if ($titulo === "" || $dni === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "El título y el DNI son obligatorios"
    ]);
    exit;
}

// ============================
// VALIDAR FORMATO DNI (8 números + letra)
// ============================
if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "El DNI no tiene un formato válido"
    ]);
    exit;
}

// ============================
// COMPROBAR DNI DUPLICADO
// ============================
$stmt = $conexion->prepare(
    "SELECT id_candidatura FROM candidatura WHERE dni = ? LIMIT 1"
);
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Ya existe una candidatura registrada con este DNI"
    ]);
    exit;
}
$stmt->close();

// ============================
// OBTENER EDICIÓN ACTIVA
// ============================
$res = $conexion->query(
    "SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1"
);
$ed = $res->fetch_assoc();

if (!$ed) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No hay una edición activa"
    ]);
    exit;
}

// ============================
// INSERTAR CANDIDATURA
// ============================
$id_usuario = $_SESSION["id_usuario"];
$id_edicion = $ed["id_edicion"];

$stmt = $conexion->prepare(
    "INSERT INTO candidatura
     (id_usuario, id_edicion, titulo_obra, sinopsis, dni)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "iisss",
    $id_usuario,
    $id_edicion,
    $titulo,
    $sinopsis,
    $dni
);

$stmt->execute();
$stmt->close();

echo json_encode([
    "ok" => true
]);
exit;
