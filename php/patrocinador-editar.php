<?php
require "config/conexion.php";
header("Content-Type: application/json; charset=utf-8");

// ============================
// VALIDAR DATOS DE ENTRADA
// ============================
$id = intval($_POST["id"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$url_web = trim($_POST["enlace"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id <= 0 || $nombre === "" || $url_web === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

// ============================
// OBTENER DATOS ACTUALES
// ============================
$stmt = $conexion->prepare("SELECT logo_ruta FROM patrocinador WHERE id_patrocinador = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "Patrocinador no encontrado"]);
    exit();
}

$actual = $resultado->fetch_assoc();
$stmt->close();

$nombreArchivo = $actual["logo_ruta"];

// ============================
// PROCESAR NUEVO LOGO (SI EXISTE)
// ============================
if (isset($_FILES["logo"]) && $_FILES["logo"]["size"] > 0) {

    $logo = $_FILES["logo"];

    // Normalizar nombre del archivo
    $nombreLimpio = preg_replace("/[^a-zA-Z0-9_\.-]/", "", basename($logo["name"]));
    $nombreArchivo = time() . "_" . $nombreLimpio;

    $rutaDestino = "../uploads/" . $nombreArchivo;

    if (!move_uploaded_file($logo["tmp_name"], $rutaDestino)) {
        echo json_encode(["ok" => false, "msg" => "Error al subir el logo"]);
        exit();
    }

    // Eliminar logo anterior si existe
    $rutaAnterior = "../uploads/" . $actual["logo_ruta"];
    if ($actual["logo_ruta"] && file_exists($rutaAnterior)) {
        @unlink($rutaAnterior);
    }
}

// ============================
// ACTUALIZAR REGISTRO
// ============================
$stmt = $conexion->prepare("
    UPDATE patrocinador 
    SET nombre = ?, logo_ruta = ?, url_web = ?, descripcion = ?
    WHERE id_patrocinador = ?
");

$stmt->bind_param("ssssi", $nombre, $nombreArchivo, $url_web, $descripcion, $id);

if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "msg" => "Error al actualizar en la base de datos"]);
    exit();
}

$stmt->close();

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode(["ok" => true, "msg" => "Patrocinador actualizado correctamente"]);
?>
