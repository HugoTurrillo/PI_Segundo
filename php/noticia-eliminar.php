<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$id = intval($_POST["id_noticia"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

// ============================
// OBTENER IMAGEN ACTUAL
// ============================
$stmt = $conexion->prepare("SELECT imagen_ruta FROM noticia WHERE id_noticia = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "Noticia no encontrada"]);
    exit;
}

$noticia = $res->fetch_assoc();
$imagen = $noticia["imagen_ruta"];
$stmt->close();

// ============================
// ELIMINAR NOTICIA
// ============================
$stmt = $conexion->prepare("DELETE FROM noticia WHERE id_noticia = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// ============================
// ELIMINAR IMAGEN DEL SERVIDOR
// ============================
$carpeta = "uploads_noticias/";

if ($imagen && file_exists($carpeta . $imagen)) {
    unlink($carpeta . $imagen);
}

echo json_encode(["ok" => true, "msg" => "Noticia eliminada correctamente"]);