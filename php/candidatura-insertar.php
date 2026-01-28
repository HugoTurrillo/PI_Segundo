<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

// Validar método
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

// Validar sesión
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "participante") {
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

// Recoger campos de texto
$titulo = trim($_POST["titulo_obra"] ?? "");
$sinopsis = trim($_POST["sinopsis"] ?? "");
$nombre = trim($_POST["nombre_contacto"] ?? "");
$email = trim($_POST["email_contacto"] ?? "");
$dni = strtoupper(trim($_POST["dni"] ?? ""));
$expediente = trim($_POST["expediente"] ?? "");

// Validaciones básicas
if ($titulo === "" || $dni === "" || $nombre === "" || $email === "") {
    echo json_encode(["ok" => false, "mensaje" => "Faltan campos obligatorios"]);
    exit;
}

// Validar DNI
if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) {
    echo json_encode(["ok" => false, "mensaje" => "El DNI no tiene un formato válido"]);
    exit;
}

// Comprobar DNI duplicado
$stmt = $conexion->prepare("SELECT id_candidatura FROM candidatura WHERE dni = ? LIMIT 1");
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["ok" => false, "mensaje" => "Ya existe una candidatura registrada con este DNI"]);
    exit;
}
$stmt->close();

// Obtener edición activa
$res = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");
$ed = $res->fetch_assoc();

if (!$ed) {
    echo json_encode(["ok" => false, "mensaje" => "No hay una edición activa"]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];
$id_edicion = $ed["id_edicion"];

// ============================
// SUBIR ARCHIVOS
// ============================

$carpeta = "../uploads/candidaturas/";
if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

// CARTEL
$cartel_nombre = time() . "_cartel_" . basename($_FILES["cartel"]["name"]);
move_uploaded_file($_FILES["cartel"]["tmp_name"], $carpeta . $cartel_nombre);

// VIDEO
$video_nombre = time() . "_video_" . basename($_FILES["video"]["name"]);
move_uploaded_file($_FILES["video"]["tmp_name"], $carpeta . $video_nombre);

// ============================
// INSERTAR EN BD
// ============================

$stmt = $conexion->prepare("
    INSERT INTO candidatura
    (id_usuario, id_edicion, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni, expediente, cartel, video, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_proceso')
");

$stmt->bind_param(
    "iissssssss",
    $id_usuario,
    $id_edicion,
    $titulo,
    $sinopsis,
    $nombre,
    $email,
    $dni,
    $expediente,
    $cartel_nombre,
    $video_nombre
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true]);
exit;