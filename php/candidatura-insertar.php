<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

// VALIDAR MÉTODO
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido"
    ]);
    exit;
}

// VALIDAR SESIÓN
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "participante") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No autorizado"
    ]);
    exit;
}

// LEER JSON
$data = json_decode(file_get_contents("php://input"), true);

$titulo   = trim($data["titulo_obra"] ?? "");
$sinopsis = trim($data["sinopsis"] ?? "");
$nombre   = trim($data["nombre_contacto"] ?? "");
$email    = trim($data["email_contacto"] ?? "");
$dni      = strtoupper(trim($data["dni"] ?? ""));

// VALIDAR CAMPOS
if ($titulo === "" || $sinopsis === "" || $dni === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Faltan datos obligatorios"
    ]);
    exit;
}

// VALIDAR DNI
if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Formato de DNI inválido"
    ]);
    exit;
}

// COMPROBAR DNI DUPLICADO
$stmt = $conexion->prepare(
    "SELECT id_candidatura FROM candidatura WHERE dni = ? LIMIT 1"
);
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Ya existe una candidatura con este DNI"
    ]);
    exit;
}
$stmt->close();

// OBTENER EDICIÓN ACTIVA
$res = $conexion->query(
    "SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1"
);
$ed = $res->fetch_assoc();

if (!$ed) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No hay edición activa"
    ]);
    exit;
}

// INSERTAR CANDIDATURA
$stmt = $conexion->prepare("
  INSERT INTO candidatura
  (id_usuario, id_edicion, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni)
  VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisssss",
    $_SESSION["id_usuario"],
    $ed["id_edicion"],
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
