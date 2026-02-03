<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

// Solo POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

// Solo participantes
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "participante") {
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

// Recibir datos del formulario (FormData)
$titulo   = trim($_POST["titulo_obra"] ?? "");
$sinopsis = trim($_POST["sinopsis"] ?? "");
$nombre   = trim($_POST["nombre_contacto"] ?? "");
$email    = trim($_POST["email_contacto"] ?? "");
$dni      = strtoupper(trim($_POST["dni"] ?? ""));

$video   = $_FILES["video"] ?? null;
$portada = $_FILES["portada"] ?? null;

// Validación básica
if ($titulo === "" || $sinopsis === "" || $dni === "") {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios"]);
    exit;
}

// Validar DNI
if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) {
    echo json_encode(["ok" => false, "mensaje" => "Formato de DNI inválido"]);
    exit;
}

// Validar archivos
if (!$video || $video["error"] !== 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir un vídeo"]);
    exit;
}

if (!$portada || $portada["error"] !== 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir una imagen de portada"]);
    exit;
}

// Comprobar DNI duplicado
$stmt = $conexion->prepare("SELECT id_candidatura FROM candidatura WHERE dni=?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["ok" => false, "mensaje" => "Ya existe una candidatura con este DNI"]);
    exit;
}
$stmt->close();

// Comprobar email duplicado
$stmt = $conexion->prepare("SELECT id_candidatura FROM candidatura WHERE email_contacto=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["ok" => false, "mensaje" => "Ya existe una candidatura con este email"]);
    exit;
}
$stmt->close();

// Obtener edición activa
$res = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa=1 LIMIT 1");
$edicion = $res->fetch_assoc();

// Crear carpetas si no existen
if (!is_dir("../uploads/videos")) mkdir("../uploads/videos", 0777, true);
if (!is_dir("../uploads/portadas")) mkdir("../uploads/portadas", 0777, true);

// Guardar archivos
$videoNombre = uniqid("video_") . "_" . basename($video["name"]);
$portadaNombre = uniqid("portada_") . "_" . basename($portada["name"]);

$videoRuta = "../uploads/videos/" . $videoNombre;
$portadaRuta = "../uploads/portadas/" . $portadaNombre;

move_uploaded_file($video["tmp_name"], $videoRuta);
move_uploaded_file($portada["tmp_name"], $portadaRuta);

// Insertar candidatura
$stmt = $conexion->prepare("
  INSERT INTO candidatura
  (id_usuario, id_edicion, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni, video_ruta, portada_ruta)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
  "iisssssss",
  $_SESSION["id_usuario"],
  $edicion["id_edicion"],
  $titulo,
  $sinopsis,
  $nombre,
  $email,
  $dni,
  $videoRuta,
  $portadaRuta
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true]);
exit;