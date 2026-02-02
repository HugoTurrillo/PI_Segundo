<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$id = intval($_POST["id_noticia"] ?? 0);
$titulo = trim($_POST["titulo"] ?? "");
$contenido = trim($_POST["contenido"] ?? "");

if ($id <= 0 || $titulo === "" || $contenido === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit;
}

// ============================
// OBTENER NOTICIA ACTUAL
// ============================
$stmt = $conexion->prepare("SELECT imagen_ruta FROM noticia WHERE id_noticia = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "Noticia no encontrada"]);
    exit;
}

$noticiaActual = $res->fetch_assoc();
$imagenActual = $noticiaActual["imagen_ruta"];
$stmt->close();

// ============================
// PROCESAR NUEVA IMAGEN (si existe)
// ============================
$nuevaImagen = $imagenActual;

if (!empty($_FILES["imagen"]["name"])) {

    $carpeta = "uploads_noticias/";
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $nombreTemp = $_FILES["imagen"]["tmp_name"];
    $nombreFinal = time() . "_" . basename($_FILES["imagen"]["name"]);
    $rutaDestino = $carpeta . $nombreFinal;

    if (!move_uploaded_file($nombreTemp, $rutaDestino)) {
        echo json_encode(["ok" => false, "msg" => "Error al subir la imagen"]);
        exit;
    }

    // Eliminar imagen anterior si existía
    if ($imagenActual && file_exists($carpeta . $imagenActual)) {
        unlink($carpeta . $imagenActual);
    }

    $nuevaImagen = $nombreFinal;
}

// ============================
// ACTUALIZAR NOTICIA
// ============================
$stmt = $conexion->prepare(
    "UPDATE noticia 
     SET titulo = ?, contenido = ?, imagen_ruta = ?
     WHERE id_noticia = ?"
);
$stmt->bind_param("sssi", $titulo, $contenido, $nuevaImagen, $id);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Noticia actualizada correctamente"]);