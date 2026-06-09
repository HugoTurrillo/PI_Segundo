<?php
/**
 * Actualizo los datos de la gala (título, fecha, lugar, descripción); solo organizador.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$titulo = $_POST["titulo"] ?? "";
$fecha = $_POST["fecha"] ?? "";
$hora = $_POST["hora"] ?? "";
$lugar = $_POST["lugar"] ?? "";
$descripcion = $_POST["descripcion"] ?? "";

if (!$titulo || !$fecha || !$hora || !$lugar) {
    echo json_encode(["ok" => false, "msg" => "Faltan datos"]);
    exit;
}

// Obtener la gala existente
$res = $conexion->query("SELECT * FROM gala LIMIT 1");
$gala = $res->fetch_assoc();
$id = $gala["id"];

// Imagen
$imagen = $gala["imagen"];

if (!empty($_FILES["imagen"]["name"])) {
    $nombreArchivo = time() . "_" . basename($_FILES["imagen"]["name"]);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
        $imagen = $nombreArchivo;
    }
}

$stmt = $conexion->prepare("UPDATE gala SET titulo=?, fecha=?, hora=?, lugar=?, descripcion=?, imagen=? WHERE id=?");
$stmt->bind_param("ssssssi", $titulo, $fecha, $hora, $lugar, $descripcion, $imagen, $id);

echo json_encode([
    "ok" => $stmt->execute(),
    "msg" => $stmt->execute() ? "Gala actualizada" : "Error al actualizar"
]);
