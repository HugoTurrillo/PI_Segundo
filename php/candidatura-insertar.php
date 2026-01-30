<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "participante") {
    echo json_encode(["ok" => false, "mensaje" => "No autorizado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$titulo   = trim($data["titulo_obra"] ?? "");
$sinopsis = trim($data["sinopsis"] ?? "");
$nombre   = trim($data["nombre_contacto"] ?? "");
$email    = trim($data["email_contacto"] ?? "");
$dni      = strtoupper(trim($data["dni"] ?? ""));

if ($titulo === "" || $sinopsis === "" || $dni === "") {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios"]);
    exit;
}

if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) {
    echo json_encode(["ok" => false, "mensaje" => "Formato de DNI inválido"]);
    exit;
}

$stmt = $conexion->prepare("SELECT id_candidatura FROM candidatura WHERE dni=?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["ok" => false, "mensaje" => "Ya existe una candidatura con este DNI"]);
    exit;
}
$stmt->close();

$res = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa=1 LIMIT 1");
$edicion = $res->fetch_assoc();

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
