<?php
include("conexion.php");
header("Content-Type: application/json; charset=utf-8");

// ============================
// VALIDAR DATOS DE ENTRADA
// ============================
$id = intval($_POST["id"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$enlace = trim($_POST["enlace"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id <= 0 || $nombre === "" || $enlace === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

// ============================
// OBTENER DATOS ACTUALES
// ============================
$stmt = $pdo->prepare("SELECT logo FROM patrocinadores WHERE id = ?");
$stmt->execute([$id]);
$actual = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actual) {
    echo json_encode(["ok" => false, "msg" => "Patrocinador no encontrado"]);
    exit();
}

$nombreArchivo = $actual["logo"];

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
    $rutaAnterior = "../uploads/" . $actual["logo"];
    if ($actual["logo"] && file_exists($rutaAnterior)) {
        @unlink($rutaAnterior);
    }
}

// ============================
// ACTUALIZAR REGISTRO
// ============================
$stmt = $pdo->prepare("
    UPDATE patrocinadores 
    SET nombre = ?, logo = ?, enlace = ?, descripcion = ?
    WHERE id = ?
");

$ok = $stmt->execute([$nombre, $nombreArchivo, $enlace, $descripcion, $id]);

if (!$ok) {
    echo json_encode(["ok" => false, "msg" => "Error al actualizar en la base de datos"]);
    exit();
}

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode(["ok" => true, "msg" => "Patrocinador actualizado correctamente"]);
?>
