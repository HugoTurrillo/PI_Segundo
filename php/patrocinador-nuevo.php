<?php
require "config/conexion.php";
header("Content-Type: application/json");

// ============================
// DATOS
// ============================
$nombre      = trim($_POST["nombre"] ?? "");
$url_web     = trim($_POST["enlace"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$forzar      = isset($_GET["forzar"]);

if ($nombre === "" || $url_web === "") {
    echo json_encode(["ok" => false, "msg" => "Nombre y enlace obligatorios"]);
    exit;
}

// ============================
// COMPROBAR DUPLICADO
// ============================
if (!$forzar) {
    $stmt = $conexion->prepare("SELECT id_patrocinador FROM patrocinador WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode([
            "ok" => false,
            "confirmar" => true,
            "msg" => "Ya existe un patrocinador con ese nombre. ¿Deseas crearlo igualmente?"
        ]);
        exit;
    }
    $stmt->close();
}

// ============================
// LOGO
// ============================
if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
    echo json_encode(["ok" => false, "msg" => "Debes subir un logo"]);
    exit;
}

$logo = $_FILES["logo"];
$nombreArchivo = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $logo["name"]);

$ruta = "uploads/" . $nombreArchivo;
if (!is_dir("uploads")) mkdir("uploads", 0777, true);

if (!move_uploaded_file($logo["tmp_name"], $ruta)) {
    echo json_encode(["ok" => false, "msg" => "Error al subir el logo"]);
    exit;
}

// ============================
// INSERTAR
// ============================
$stmt = $conexion->prepare("
    INSERT INTO patrocinador (nombre, logo_ruta, url_web, descripcion)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("ssss", $nombre, $nombreArchivo, $url_web, $descripcion);

if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "msg" => "Error al guardar"]);
    exit;
}

$stmt->close();

echo json_encode(["ok" => true, "msg" => "Patrocinador creado"]);
exit;
