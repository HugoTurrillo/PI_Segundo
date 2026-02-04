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

$id_usuario = $_SESSION["id_usuario"];

/* ===========================
   BLOQUEAR SEGUNDA CANDIDATURA
=========================== */
$check = $conexion->prepare("
    SELECT id_candidatura, estado
    FROM candidatura
    WHERE id_usuario = ?
    LIMIT 1
");
$check->bind_param("i", $id_usuario);
$check->execute();
$resCheck = $check->get_result();

if ($resCheck->num_rows > 0) {
    $existe = $resCheck->fetch_assoc();

    // Si NO está rechazada → bloquear
    if ($existe["estado"] !== "rechazada") {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Ya has enviado una candidatura. No puedes enviar otra."
        ]);
        exit;
    }
}
$check->close();

/* ===========================
   DATOS FORMULARIO
=========================== */
$titulo   = trim($_POST["titulo_obra"] ?? "");
$sinopsis = trim($_POST["sinopsis"] ?? "");
$dni      = strtoupper(trim($_POST["dni"] ?? ""));

$video   = $_FILES["video"] ?? null;
$portada = $_FILES["portada"] ?? null;

if ($titulo === "" || $sinopsis === "" || $dni === "") {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios"]);
    exit;
}

if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) {
    echo json_encode(["ok" => false, "mensaje" => "DNI inválido"]);
    exit;
}

if (!$video || $video["error"] !== 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir un vídeo"]);
    exit;
}

if (!$portada || $portada["error"] !== 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir una portada"]);
    exit;
}

/* ===========================
   DATOS USUARIO
=========================== */
$stmt = $conexion->prepare("
    SELECT nombre_completo, email
    FROM usuario
    WHERE id_usuario = ?
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ===========================
   EDICIÓN ACTIVA
=========================== */
$res = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa=1 LIMIT 1");
$edicion = $res->fetch_assoc();

/* ===========================
   CARPETAS
=========================== */
@mkdir(__DIR__ . "/../uploads/videos", 0777, true);
@mkdir(__DIR__ . "/../uploads/portadas", 0777, true);

/* ===========================
   ARCHIVOS
=========================== */
$videoNombre   = uniqid("video_") . "_" . basename($video["name"]);
$portadaNombre = uniqid("portada_") . "_" . basename($portada["name"]);

$videoRutaBD   = "/uploads/videos/" . $videoNombre;
$portadaRutaBD = "/uploads/portadas/" . $portadaNombre;

move_uploaded_file($video["tmp_name"], __DIR__ . "/../" . $videoRutaBD);
move_uploaded_file($portada["tmp_name"], __DIR__ . "/../" . $portadaRutaBD);

/* ===========================
   INSERT
=========================== */
$stmt = $conexion->prepare("
    INSERT INTO candidatura
    (id_usuario, id_edicion, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni, video_ruta, portada_ruta)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisssssss",
    $id_usuario,
    $edicion["id_edicion"],
    $titulo,
    $sinopsis,
    $user["nombre_completo"],
    $user["email"],
    $dni,
    $videoRutaBD,
    $portadaRutaBD
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true]);
exit;
