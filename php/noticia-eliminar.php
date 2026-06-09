<?php
/**
 * Elimino una noticia y su imagen del servidor; uso ruta absoluta para el unlink. Solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$id = intval($_POST["id_noticia"] ?? 0);

if ($id <= 0) {
    echo json_encode(["ok" => false, "msg" => "ID no válido"]);
    exit;
}

// Obtengo la ruta de la imagen para borrarla del disco
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
$carpeta = __DIR__ . "/../uploads_noticias/";
if ($imagen && is_dir($carpeta) && file_exists($carpeta . $imagen)) {
    @unlink($carpeta . $imagen);
}

echo json_encode(["ok" => true, "msg" => "Noticia eliminada correctamente"]);