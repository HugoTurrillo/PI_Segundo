<?php
include("conexion.php");
header("Content-Type: application/json; charset=utf-8");

// ============================
// VALIDAR DATOS DE ENTRADA
// ============================
$id = intval($_POST["id"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$url_web = trim($_POST["enlace"] ?? ""); // sigue viniendo como "enlace" desde el formulario
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id <= 0 || $nombre === "" || $url_web === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

// ============================
// OBTENER DATOS ACTUALES
// ============================
$stmt = $pdo->prepare("SELECT logo_ruta FROM patrocinador WHERE id_patrocinador = ?");
$stmt->execute([$id]);
$actual = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actual) {
    echo json_encode(["ok" => false, "msg" => "Patrocinador no encontrado"]);
    exit();
}

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

    // Subir archivo
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
$stmt = $pdo->prepare("
    UPDATE patrocinador 
    SET nombre = ?, logo_ruta = ?, url_web = ?, descripcion = ?
    WHERE id_patrocinador = ?
");

$ok = $stmt->execute([$nombre, $nombreArchivo, $url_web, $descripcion, $id]);

if (!$ok) {
    echo json_encode(["ok" => false, "msg" => "Error al actualizar en la base de datos"]);
    exit();
}

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode(["ok" => true, "msg" => "Patrocinador actualizado correctamente"]);
?>