<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

$nombre_contacto = trim($_POST["nombre_contacto"] ?? "");
$email_contacto  = trim($_POST["email_contacto"] ?? "");
$titulo_obra     = trim($_POST["titulo_obra"] ?? "");
$sinopsis        = trim($_POST["sinopsis"] ?? "");
$dni             = trim($_POST["dni"] ?? "");

if ($nombre_contacto === "" || $email_contacto === "" ||
    $titulo_obra === "" || $sinopsis === "" || $dni === "") {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

$stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->bind_param("s", $email_contacto);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
    exit;
}

$id_usuario = $res->fetch_assoc()["id_usuario"];

$ed = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");
$id_edicion = $ed->fetch_assoc()["id_edicion"];

if (!isset($_FILES["video"])) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir un vídeo"]);
    exit;
}

$carpeta = "../uploads/candidaturas/";
if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

$video_nombre = time() . "_video_" . basename($_FILES["video"]["name"]);
$video_ruta = $carpeta . $video_nombre;
move_uploaded_file($_FILES["video"]["tmp_name"], $video_ruta);

$portada_nombre = time() . "_portada_" . basename($_FILES["portada"]["name"]);
$portada_ruta = $carpeta . $portada_nombre;
move_uploaded_file($_FILES["portada"]["tmp_name"], $portada_ruta);

$stmt = $conexion->prepare("
    INSERT INTO candidatura 
    (id_usuario, id_edicion, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni, video_ruta, portada_ruta)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisssssss",
    $id_usuario,
    $id_edicion,
    $titulo_obra,
    $sinopsis,
    $nombre_contacto,
    $email_contacto,
    $dni,
    $video_ruta,
    $portada_ruta
);

$stmt->execute();

echo json_encode(["ok" => true, "mensaje" => "Candidatura enviada correctamente"]);
