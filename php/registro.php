<?php
/**
 * Registro de usuario y envío de candidatura en un solo paso.
 * Valido datos, compruebo duplicados (email, DNI, expediente), creo el usuario y la candidatura con la categoría según perfil.
 */

require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

/* 1. Datos del usuario */

$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$rol_participante = trim($_POST["rol_participante"] ?? "");
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

if (!in_array($rol_participante, ["alumno", "alumni"])) {
    echo json_encode(["ok" => false, "mensaje" => "Perfil de participante no válido"]);
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

/* DNI DUPLICADO */
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE dni = ?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["ok" => false, "mensaje" => "No puede usar ese DNI"]);
    exit;
}

/* Nª EXPEDIENTE DUPLICADO */
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE numero_expediente = ?");
$stmt->bind_param("s", $numero_expediente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["ok" => false, "mensaje" => "Este número de expediente ya está en uso"]);
    exit;
}
$stmt->close();

/* ============================
   CREAR USUARIO
============================ */

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("
    INSERT INTO usuario 
    (nombre_completo, email, password_hash, rol, rol_participante, dni, numero_expediente)
    VALUES (?, ?, ?, 'participante', ?, ?, ?)
");

$stmt->bind_param(
    "ssssss",
    $nombre,
    $email,
    $password_hash,
    $rol_participante,
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
   2b. CATEGORÍA SEGÚN PERFIL (Alumno → Alumnos, Alumni → Alumni)
============================ */
$nombre_categoria = ($rol_participante === "alumno") ? "Alumnos" : "Alumni";
$stmt_cat = $conexion->prepare("SELECT id FROM categorias WHERE nombre = ? LIMIT 1");
$stmt_cat->bind_param("s", $nombre_categoria);
$stmt_cat->execute();
$res_cat = $stmt_cat->get_result();
$id_categoria = null;
if ($res_cat->num_rows > 0) {
    $id_categoria = (int) $res_cat->fetch_assoc()["id"];
}
$stmt_cat->close();

/* ============================
   3. DATOS DE CANDIDATURA
============================ */

$titulo_obra = trim($_POST["titulo_obra"] ?? "");
$sinopsis = trim($_POST["sinopsis"] ?? "");
$dni_candidatura = trim($_POST["dni"] ?? "");

if ($titulo_obra === "" || $sinopsis === "" || $dni_candidatura === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Todos los campos de la candidatura son obligatorios"
    ]);
    exit;
}

/* ============================
   4. SUBIR ARCHIVOS
============================ */

if (!isset($_FILES["video"]) || !isset($_FILES["portada"])) {
    echo json_encode(["ok" => false, "mensaje" => "Debes adjuntar vídeo y portada"]);
    exit;
}

/* RUTAS FÍSICAS (fuera de /php) */
$carpeta_fisica_videos   = __DIR__ . "/../videos/";
$carpeta_fisica_portadas = __DIR__ . "/../portadas/";

/* RUTAS PÚBLICAS PARA BD */
$carpeta_bd_videos   = "videos/";
$carpeta_bd_portadas = "portadas/";

/* Crear carpetas si no existen */
if (!is_dir($carpeta_fisica_videos)) {
    mkdir($carpeta_fisica_videos, 0777, true);
}

if (!is_dir($carpeta_fisica_portadas)) {
    mkdir($carpeta_fisica_portadas, 0777, true);
}

/* VIDEO */
$video_ext = pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION);
$video_nombre = time() . "_video." . strtolower($video_ext);

if (!move_uploaded_file(
    $_FILES["video"]["tmp_name"],
    $carpeta_fisica_videos . $video_nombre
)) {
    echo json_encode(["ok" => false, "mensaje" => "Error al subir el vídeo"]);
    exit;
}

/* PORTADA */
$portada_ext = pathinfo($_FILES["portada"]["name"], PATHINFO_EXTENSION);
$portada_nombre = time() . "_portada." . strtolower($portada_ext);

if (!move_uploaded_file(
    $_FILES["portada"]["tmp_name"],
    $carpeta_fisica_portadas . $portada_nombre
)) {
    echo json_encode(["ok" => false, "mensaje" => "Error al subir la portada"]);
    exit;
}

/* RUTAS QUE SE GUARDAN EN BD */
$video_ruta_bd   = $carpeta_bd_videos . $video_nombre;
$portada_ruta_bd = $carpeta_bd_portadas . $portada_nombre;

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
    $video_ruta_bd,
    $portada_ruta_bd
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true, "mensaje" => "Registro y candidatura completados"]);
exit;
