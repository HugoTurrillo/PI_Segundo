<?php
/**
 * Subo una imagen a la galería del postevento y la registro en post_evento_imagen; solo organizador.
 */

require_once __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$response = ["ok" => false];

// Validar archivo
if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
    $response["msg"] = "No se ha recibido ninguna imagen válida.";
    echo json_encode($response);
    exit;
}

// Validar id_post_evento
if (!isset($_POST["id_post_evento"])) {
    $response["msg"] = "Falta el ID del post‑evento.";
    echo json_encode($response);
    exit;
}

$id_post_evento = intval($_POST["id_post_evento"]);

// Carpeta donde guardar imágenes
$carpeta = "../uploads/";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Procesar archivo
$nombreOriginal = $_FILES["imagen"]["name"];
$extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

// Nombre único
$nombreFinal = "post_" . $id_post_evento . "_" . time() . "." . $extension;

$rutaDestino = $carpeta . $nombreFinal;

// Mover archivo
if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
    $response["msg"] = "Error al guardar la imagen en el servidor.";
    echo json_encode($response);
    exit;
}

// Guardar en BD
$stmt = $conexion->prepare("
    INSERT INTO post_evento_imagen (id_post_evento, ruta_imagen)
    VALUES (?, ?)
");

$stmt->bind_param("is", $id_post_evento, $nombreFinal);

if ($stmt->execute()) {
    $response["ok"] = true;
    $response["msg"] = "Imagen subida correctamente.";
} else {
    $response["msg"] = "Error al guardar en la base de datos.";
}

echo json_encode($response);
