<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

/* ======================================================
   DATOS DEL USUARIO (AUTORELLENADOS)
====================================================== */
$nombre_contacto = trim($_POST["nombre_contacto"] ?? "");
$email_contacto  = trim($_POST["email_contacto"] ?? "");
$dni             = trim($_POST["dni"] ?? "");
$titulo_obra     = trim($_POST["titulo_obra"] ?? "");
$sinopsis        = trim($_POST["sinopsis"] ?? "");

if ($nombre_contacto === "" || $email_contacto === "" || $dni === "" ||
    $titulo_obra === "" || $sinopsis === "") {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

/* ======================================================
   OBTENER ID_USUARIO
====================================================== */
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->bind_param("s", $email_contacto);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
    exit;
}

$id_usuario = $res->fetch_assoc()["id_usuario"];
$stmt->close();

/* ======================================================
   OBTENER EDICIÓN ACTIVA
====================================================== */
$ed = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");

if ($ed->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No hay edición activa"]);
    exit;
}

$id_edicion = $ed->fetch_assoc()["id_edicion"];

/* ======================================================
   SUBIR PORTADA
====================================================== */
if (!isset($_FILES["portada"])) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir una portada"]);
    exit;
}

$carpeta = "../uploads/candidaturas/";
if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

$portada_nombre = time() . "_portada_" . basename($_FILES["portada"]["name"]);
$portada_ruta = $carpeta . $portada_nombre;

if (!move_uploaded_file($_FILES["portada"]["tmp_name"], $portada_ruta)) {
    echo json_encode(["ok" => false, "mensaje" => "Error al subir la portada"]);
    exit;
}

/* ======================================================
   INSERTAR CANDIDATURA
====================================================== */
$stmt = $conexion->prepare("
    INSERT INTO candidatura 
    (id_usuario, id_edicion, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni, portada_ruta)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissssss",
    $id_usuario,
    $id_edicion,
    $titulo_obra,
    $sinopsis,
    $nombre_contacto,
    $email_contacto,
    $dni,
    $portada_ruta
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true, "mensaje" => "Candidatura enviada correctamente"]);
exit;
