<?php
require "config/conexion.php";
header("Content-Type: application/json");

session_start();
$id_usuario = $_SESSION["id_usuario"] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "No autenticado"]);
    exit;
}

$titulo = trim($_POST["tituloEditado"] ?? "");
$sinopsis = trim($_POST["sinopsisEditada"] ?? "");
$mensaje = trim($_POST["mensajeSubsanacion"] ?? "");

if ($mensaje === "") {
    echo json_encode(["ok" => false, "mensaje" => "Debes escribir un mensaje de subsanación"]);
    exit;
}

$sql = "SELECT id_candidatura FROM candidatura WHERE id_usuario = ? ORDER BY id_candidatura DESC LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No tienes candidatura"]);
    exit;
}

$id_candidatura = $res->fetch_assoc()["id_candidatura"];

$portada_ruta = null;

if (isset($_FILES["portadaEditada"]) && $_FILES["portadaEditada"]["size"] > 0) {
    $carpeta = "../uploads/candidaturas/";
    if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

    $nombre = time() . "_portada_" . basename($_FILES["portadaEditada"]["name"]);
    $portada_ruta = $carpeta . $nombre;

    move_uploaded_file($_FILES["portadaEditada"]["tmp_name"], $portada_ruta);
}

$sql = "UPDATE candidatura 
        SET titulo_obra = IF(?='', titulo_obra, ?),
            sinopsis = IF(?='', sinopsis, ?),
            mensaje_subsanacion = ?,
            portada_ruta = IF(? IS NULL, portada_ruta, ?),
            estado = 'en_proceso'
        WHERE id_candidatura = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sssssssi",
    $titulo, $titulo,
    $sinopsis, $sinopsis,
    $mensaje,
    $portada_ruta, $portada_ruta,
    $id_candidatura
);

$stmt->execute();

echo json_encode(["ok" => true, "mensaje" => "Subsanación enviada"]);
