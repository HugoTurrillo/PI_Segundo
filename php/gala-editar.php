<?php
include("conexion.php");
header("Content-Type: application/json");

$id = intval($_POST["id"] ?? 0);
$titulo = trim($_POST["titulo"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$hora = trim($_POST["hora"] ?? "");
$lugar = trim($_POST["lugar"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id <= 0 || $titulo === "" || $fecha === "" || $hora === "" || $lugar === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

$stmt = $pdo->prepare("SELECT imagen FROM gala WHERE id = ?");
$stmt->execute([$id]);
$actual = $stmt->fetch(PDO::FETCH_ASSOC);

$nombreArchivo = $actual["imagen"];

if (isset($_FILES["imagen"]) && $_FILES["imagen"]["size"] > 0) {
    $img = $_FILES["imagen"];
    $nombreArchivo = time() . "_" . basename($img["name"]);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    move_uploaded_file($img["tmp_name"], $rutaDestino);

    if (file_exists("../uploads/" . $actual["imagen"])) {
        unlink("../uploads/" . $actual["imagen"]);
    }
}

$stmt = $pdo->prepare("UPDATE gala SET titulo=?, fecha=?, hora=?, lugar=?, descripcion=?, imagen=? WHERE id=?");
$stmt->execute([$titulo, $fecha, $hora, $lugar, $descripcion, $nombreArchivo, $id]);

echo json_encode(["ok" => true, "msg" => "Evento actualizado"]);
?>