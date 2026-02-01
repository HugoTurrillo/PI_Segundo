<?php
session_start();
require __DIR__ . "/config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
    exit;
}

// Validar campos obligatorios
$campos = [
    "nombre_completo", "email", "telefono", "password",
    "titulo_obra", "sinopsis", "nombre_contacto", "email_contacto", "dni",
    "id_categoria", "id_edicion"
];

foreach ($campos as $campo) {
    if (empty($_POST[$campo])) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Falta el campo obligatorio: $campo"
        ]);
        exit;
    }
}

// Sanitizar entradas
$nombre_completo  = trim($_POST["nombre_completo"]);
$email            = trim($_POST["email"]);
$telefono         = trim($_POST["telefono"]);
$password_hash    = password_hash($_POST["password"], PASSWORD_DEFAULT);
$titulo_obra      = trim($_POST["titulo_obra"]);
$sinopsis         = trim($_POST["sinopsis"]);
$nombre_contacto  = trim($_POST["nombre_contacto"]);
$email_contacto   = trim($_POST["email_contacto"]);
$dni              = trim($_POST["dni"]);
$id_categoria     = intval($_POST["id_categoria"]);
$id_edicion       = intval($_POST["id_edicion"]);

// Validar archivo
if (!isset($_FILES["video"]) || $_FILES["video"]["error"] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No se ha subido el vídeo correctamente."
    ]);
    exit;
}

$video = $_FILES["video"];

if ($video["type"] !== "video/mp4") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "El vídeo debe ser un archivo MP4."
    ]);
    exit;
}

if ($video["size"] > 500 * 1024 * 1024) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "El archivo supera el tamaño máximo permitido (500MB)."
    ]);
    exit;
}

$videoName = uniqid("video_", true) . ".mp4";
$videoPath = __DIR__ . "/../videos/" . $videoName;

if (!move_uploaded_file($video["tmp_name"], $videoPath)) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "No se pudo guardar el archivo de vídeo."
    ]);
    exit;
}

try {
    $conexion->begin_transaction();

    // Verificar email duplicado
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Ya existe un usuario con este email."
        ]);
        exit;
    }

    // Insertar usuario
    $stmt = $conexion->prepare("
        INSERT INTO usuario (nombre_completo, email, password_hash, rol, activo)
        VALUES (?, ?, ?, 'participante', 1)
    ");
    $stmt->bind_param("sss", $nombre_completo, $email, $password_hash);
    $stmt->execute();
    $id_usuario = $conexion->insert_id;

    // Insertar candidatura
    $stmt = $conexion->prepare("
        INSERT INTO candidatura (
            id_usuario, id_edicion, id_categoria,
            titulo_obra, sinopsis, nombre_contacto,
            email_contacto, dni, estado, motivo_rechazo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDIENTE', NULL)
    ");
    $stmt->bind_param(
        "iiisssss",
        $id_usuario, $id_edicion, $id_categoria,
        $titulo_obra, $sinopsis, $nombre_contacto,
        $email_contacto, $dni
    );
    $stmt->execute();

    $conexion->commit();

    $_SESSION["id_usuario"] = $id_usuario;
    $_SESSION["nombre"] = $nombre_completo;
    $_SESSION["rol"] = "participante";

   header("Location: ../HTML/participante.php");
    exit;


} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode([
        "ok" => false,
        "mensaje" => "Error en el registro: " . $e->getMessage()
    ]);
    exit;
}
