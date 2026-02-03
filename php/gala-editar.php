<?php
// php/gala-editar.html
require "config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
    exit;
}

$id = intval($_POST["id"] ?? 0);
$titulo = trim($_POST["titulo"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$hora = trim($_POST["hora"] ?? "");
$lugar = trim($_POST["lugar"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id <= 0 || $titulo === "" || $fecha === "" || $hora === "" || $lugar === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit;
}

$hoy = date("Y-m-d");
if ($fecha < $hoy) {
    echo json_encode(["ok" => false, "msg" => "La fecha no puede ser anterior a hoy"]);
    exit;
}

$stmt = $conexion->prepare("SELECT imagen FROM gala WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$actual = $resultado->fetch_assoc();

$nombreArchivo = $actual["imagen"] ?? "";

if (isset($_FILES["imagen"]) && $_FILES["imagen"]["size"] > 0) {
    $img = $_FILES["imagen"];
    $nombreArchivo = time() . "_" . basename($img["name"]);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    move_uploaded_file($img["tmp_name"], $rutaDestino);

    if (!empty($actual["imagen"]) && file_exists("../uploads/" . $actual["imagen"])) {
        unlink("../uploads/" . $actual["imagen"]);
    }
}

$stmt = $conexion->prepare(
    "UPDATE gala
     SET titulo=?, fecha=?, hora=?, lugar=?, descripcion=?, imagen=?
     WHERE id=?"
);

$stmt->bind_param(
    "ssssssi",
    $titulo,
    $fecha,
    $hora,
    $lugar,
    $descripcion,
    $nombreArchivo,
    $id
);

$stmt->execute();

echo json_encode(["ok" => true, "msg" => "Evento actualizado"]);
