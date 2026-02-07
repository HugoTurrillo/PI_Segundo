<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

/* ============================
   1. DATOS DEL USUARIO
============================ */

$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$rol_participante = "participante";
$dni = trim($_POST["dni"] ?? "");
$numero_expediente = trim($_POST["numero_expediente"] ?? "");

if (
    $nombre === "" ||
    $email === "" ||
    $password === "" ||
    $rol_participante === "" ||
    $dni === "" ||
    $numero_expediente === ""
) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos del usuario son obligatorios"]);
    exit;
}

/* EMAIL DUPLICADO */
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["ok" => false, "mensaje" => "El email ya está registrado"]);
    exit;
}
$stmt->close();

/* CREAR USUARIO */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("
    INSERT INTO usuario 
    (nombre_completo, email, password_hash, rol, rol_participante, dni, numero_expediente)
    VALUES (?, ?, ?, 'participante', 'participante', ?, ?)
");

$stmt->bind_param(
    "sssss",
    $nombre,
    $email,
    $password_hash,
    $dni,
    $numero_expediente
);


$stmt->execute();
$id_usuario = $stmt->insert_id;
$stmt->close();

/* ============================
   2. OBTENER EDICIÓN ACTIVA
============================ */

$ed = $conexion->query("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");

if ($ed->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No hay edición activa"]);
    exit;
}

$id_edicion = $ed->fetch_assoc()["id_edicion"];

/* ============================
   3. DATOS DE CANDIDATURA
============================ */

$titulo_obra = trim($_POST["titulo_obra"] ?? "");
$sinopsis = trim($_POST["sinopsis"] ?? "");
$dni_candidatura = trim($_POST["dni"] ?? ""); // puede ser el mismo DNI del usuario
$id_categoria = intval($_POST["id_categoria"] ?? 0);

if ($titulo_obra === "" || $sinopsis === "" || $dni_candidatura === "" || $id_categoria === 0) {
    echo json_encode(["ok" => false, "mensaje" => "Todos los campos de la candidatura son obligatorios"]);
    exit;
}

/* ============================
   4. SUBIR ARCHIVOS
============================ */

if (!isset($_FILES["video"]) || !isset($_FILES["portada"])) {
    echo json_encode(["ok" => false, "mensaje" => "Debes adjuntar vídeo y portada"]);
    exit;
}

$carpeta = "../uploads/candidaturas/";
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

/* VIDEO */
$video_nombre = time() . "_video_" . basename($_FILES["video"]["name"]);
$video_ruta = $carpeta . $video_nombre;

if (!move_uploaded_file($_FILES["video"]["tmp_name"], $video_ruta)) {
    echo json_encode(["ok" => false, "mensaje" => "Error al subir el vídeo"]);
    exit;
}

/* PORTADA */
$portada_nombre = time() . "_portada_" . basename($_FILES["portada"]["name"]);
$portada_ruta = $carpeta . $portada_nombre;

if (!move_uploaded_file($_FILES["portada"]["tmp_name"], $portada_ruta)) {
    echo json_encode(["ok" => false, "mensaje" => "Error al subir la portada"]);
    exit;
}

/* ============================
   5. INSERTAR CANDIDATURA
============================ */

$stmt = $conexion->prepare("
    INSERT INTO candidatura 
    (id_usuario, id_edicion, id_categoria, titulo_obra, sinopsis, nombre_contacto, email_contacto, dni, video_ruta, portada_ruta)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iiisssssss",
    $id_usuario,
    $id_edicion,
    $id_categoria,
    $titulo_obra,
    $sinopsis,
    $nombre,
    $email,
    $dni_candidatura,
    $video_ruta,
    $portada_ruta
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true, "mensaje" => "Registro y candidatura completados"]);
exit;