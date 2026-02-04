<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["ok" => false, "msg" => "No autenticado"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$mensaje  = trim($_POST["mensaje"] ?? "");
$sinopsis = trim($_POST["sinopsis"] ?? "");

$video   = $_FILES["video"] ?? null;
$portada = $_FILES["portada"] ?? null;

if ($mensaje === "") {
    echo json_encode(["ok" => false, "msg" => "Mensaje obligatorio"]);
    exit;
}

/* Obtener candidatura rechazada */
$stmt = $conexion->prepare("
    SELECT id_candidatura
    FROM candidatura
    WHERE id_usuario = ?
      AND estado = 'rechazada'
    LIMIT 1
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "No hay candidatura rechazada"]);
    exit;
}

$candidatura = $res->fetch_assoc();
$id_candidatura = $candidatura["id_candidatura"];

/* Archivos nuevos (opcional) */
$updates = [];
$params  = [];
$types   = "";

/* Sinopsis */
if ($sinopsis !== "") {
    $updates[] = "sinopsis=?";
    $params[]  = $sinopsis;
    $types    .= "s";
}

/* Vídeo */
if ($video && $video["error"] === 0) {
    $videoNombre = uniqid("video_") . "_" . basename($video["name"]);
    $videoRutaBD = "/uploads/videos/" . $videoNombre;
    move_uploaded_file($video["tmp_name"], __DIR__ . "/../" . $videoRutaBD);

    $updates[] = "video_ruta=?";
    $params[]  = $videoRutaBD;
    $types    .= "s";
}

/* Portada */
if ($portada && $portada["error"] === 0) {
    $portadaNombre = uniqid("portada_") . "_" . basename($portada["name"]);
    $portadaRutaBD = "/uploads/portadas/" . $portadaNombre;
    move_uploaded_file($portada["tmp_name"], __DIR__ . "/../" . $portadaRutaBD);

    $updates[] = "portada_ruta=?";
    $params[]  = $portadaRutaBD;
    $types    .= "s";
}

/* Estado */
$updates[] = "mensaje_subsanacion=?";
$updates[] = "estado='en_proceso'";
$updates[] = "motivo_rechazo=NULL";

$params[] = $mensaje;
$types   .= "s";

/* Ejecutar UPDATE */
$sql = "
    UPDATE candidatura
    SET " . implode(", ", $updates) . "
    WHERE id_candidatura=?
";
$params[] = $id_candidatura;
$types   .= "i";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Subsanación enviada correctamente"]);
exit;
