<?php
require __DIR__ . "/config/conexion.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

session_start();

/* ============================
   MÉTODO
============================ */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

/* ============================
   SESIÓN
============================ */
if (!isset($_SESSION["email"])) {
    echo json_encode(["ok" => false, "mensaje" => "Sesión no válida"]);
    exit;
}

/* ============================
   DATOS FORMULARIO
============================ */
$titulo_obra  = trim($_POST["titulo_obra"] ?? "");
$sinopsis     = trim($_POST["sinopsis"] ?? "");
$id_categoria = intval($_POST["categoria"] ?? 0);

if ($titulo_obra === "" || $sinopsis === "" || $id_categoria === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

/* ============================
   OBTENER USUARIO COMPLETO
============================ */
$email_usuario = $_SESSION["email"];

$stmt = $conexion->prepare("
    SELECT 
        id_usuario,
        dni,
        nombre_completo,
        email,
        rol_participante
    FROM usuario
    WHERE email = ?
");
$stmt->bind_param("s", $email_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Usuario no encontrado"]);
    exit;
}

$row = $res->fetch_assoc();

$id_usuario       = $row["id_usuario"];
$dni              = $row["dni"];
$nombre_contacto  = $row["nombre_completo"];
$email_contacto   = $row["email"];
$rol_participante = $row["rol_participante"];

/* ============================
   EDICIÓN ACTIVA
============================ */
$ed = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");
if (!$ed || $ed->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No hay edición activa"]);
    exit;
}
$id_edicion = $ed->fetch_assoc()["id_edicion"];

/* ============================
   VALIDAR ARCHIVOS
============================ */
if (!isset($_FILES["video"]) || $_FILES["video"]["size"] === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir un vídeo"]);
    exit;
}

if (!isset($_FILES["portada"]) || $_FILES["portada"]["size"] === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Debes subir una portada"]);
    exit;
}

/* ============================
   SUBIR ARCHIVOS
============================ */

/* RUTA REAL EN DISCO */
$carpeta_fisica = __DIR__ . "/uploads/candidaturas/";

/* RUTA WEB (la que va a la BBDD) */
$carpeta_bd = "../php/uploads/candidaturas/";

if (!is_dir($carpeta_fisica)) {
    mkdir($carpeta_fisica, 0777, true);
}

/* VIDEO */
$video_ext = pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION);
$video_nombre = time() . "_video." . strtolower($video_ext);
move_uploaded_file(
    $_FILES["video"]["tmp_name"],
    $carpeta_fisica . $video_nombre
);

/* PORTADA */
$portada_ext = pathinfo($_FILES["portada"]["name"], PATHINFO_EXTENSION);
$portada_nombre = time() . "_portada." . strtolower($portada_ext);
move_uploaded_file(
    $_FILES["portada"]["tmp_name"],
    $carpeta_fisica . $portada_nombre
);

$video_ruta_bd   = $carpeta_bd . $video_nombre;
$portada_ruta_bd = $carpeta_bd . $portada_nombre;

/* ============================
   INSERTAR CANDIDATURA 
============================ */
$sql = "
    INSERT INTO candidatura
    (
        id_usuario,
        id_edicion,
        id_categoria,
        titulo_obra,
        sinopsis,
        nombre_contacto,
        email_contacto,
        dni,
        video_ruta,
        portada_ruta
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "iiisssssss",
    $id_usuario,
    $id_edicion,
    $id_categoria,
    $titulo_obra,
    $sinopsis,
    $nombre_contacto,
    $email_contacto,
    $dni,
    $video_ruta_bd,
    $portada_ruta_bd
);

$stmt->execute();

echo json_encode(["ok" => true, "mensaje" => "Candidatura enviada correctamente"]);
exit;