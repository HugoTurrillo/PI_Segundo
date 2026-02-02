<?php
require "config/conexion.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$titulo = trim($_POST["titulo"] ?? "");
$contenido = trim($_POST["contenido"] ?? "");

if ($titulo === "" || $contenido === "") {
    echo json_encode(["ok" => false, "msg" => "Todos los campos son obligatorios"]);
    exit;
}

// ============================
// SUBIR IMAGEN
// ============================
$imagen_ruta = null;

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

    $imagen_ruta = $nombreFinal;
}

// ============================
// INSERTAR EN BD
// ============================
$stmt = $conexion->prepare(
    "INSERT INTO noticia (titulo, contenido, imagen_ruta, fecha_publicacion)
     VALUES (?, ?, ?, NOW())"
);
$stmt->bind_param("sss", $titulo, $contenido, $imagen_ruta);
$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Noticia creada correctamente"]);