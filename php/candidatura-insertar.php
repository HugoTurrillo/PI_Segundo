<?php
require __DIR__ . "/config/conexion.php";





header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

session_start();

/* Método permitido */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

/* Sesión válida */
if (!isset($_SESSION["email"])) {
    echo json_encode(["ok" => false, "mensaje" => "Sesión no válida"]);
    exit;
}

/* Datos básicos del formulario */
$titulo_obra  = trim($_POST["titulo_obra"] ?? "");
$sinopsis     = trim($_POST["sinopsis"] ?? "");
$id_categoria = intval($_POST["categoria"] ?? 0);

if ($titulo_obra === "" || $sinopsis === "" || $id_categoria === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

/* Obtener usuario y DNI desde email en sesión */
$email_usuario = $_SESSION["email"];

$stmt = $conexion->prepare("SELECT id_usuario, dni FROM usuario WHERE email = ?");
if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error en prepare (usuario): " . $conexion->error]);
    exit;
}

$stmt->bind_param("s", $email_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
    exit;
}

$row        = $res->fetch_assoc();
$id_usuario = $row["id_usuario"];
$dni = $row["dni"];


/* Edición activa */
$ed = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");
if (!$ed || $ed->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No hay edición activa"]);
    exit;
}
$id_edicion = $ed->fetch_assoc()["id_edicion"];

/* Validar archivos */
if (!isset($_FILES["video"]) || $_FILES["video"]["size"] === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir un vídeo"]);
    exit;
}

if (!isset($_FILES["portada"]) || $_FILES["portada"]["size"] === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir una portada"]);
    exit;
}

/* Subir archivos */
$carpeta = "../uploads/candidaturas/";
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

$video_nombre  = time() . "_video_" . basename($_FILES["video"]["name"]);
$video_ruta    = $carpeta . $video_nombre;
move_uploaded_file($_FILES["video"]["tmp_name"], $video_ruta);

$portada_nombre = time() . "_portada_" . basename($_FILES["portada"]["name"]);
$portada_ruta   = $carpeta . $portada_nombre;
move_uploaded_file($_FILES["portada"]["tmp_name"], $portada_ruta);

/* Insertar candidatura */
$sql = "
    INSERT INTO candidatura  
    (id_usuario, id_edicion, id_categoria, titulo_obra, sinopsis, dni, video_ruta, portada_ruta)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conexion->prepare($sql);



$stmt->bind_param(
    "iiisssss",
    $id_usuario,
    $id_edicion,
    $id_categoria,
    $titulo_obra,
    $sinopsis,
    $dni,
    $video_ruta,
    $portada_ruta
);

$stmt->execute();

echo json_encode(["ok" => true, "mensaje" => "Candidatura enviada correctamente"]);
