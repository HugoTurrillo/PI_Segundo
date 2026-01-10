<?php
include("conexion.php");
header("Content-Type: application/json");

$id = intval($_POST["id"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$enlace = trim($_POST["enlace"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id <= 0 || $nombre === "" || $enlace === "") {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit();
}

$stmt = $pdo->prepare("SELECT logo FROM patrocinadores WHERE id = ?");
$stmt->execute([$id]);
$actual = $stmt->fetch(PDO::FETCH_ASSOC);

$nombreArchivo = $actual["logo"];

if (isset($_FILES["logo"]) && $_FILES["logo"]["size"] > 0) {
    $logo = $_FILES["logo"];
    $nombreArchivo = time() . "_" . basename($logo["name"]);
    $rutaDestino = "../uploads/" . $nombreArchivo;

    move_uploaded_file($logo["tmp_name"], $rutaDestino);

    if (file_exists("../uploads/" . $actual["logo"])) {
        unlink("../uploads/" . $actual["logo"]);
    }
}

$stmt = $pdo->prepare("UPDATE patrocinadores SET nombre=?, logo=?, enlace=?, descripcion=? WHERE id=?");
$stmt->execute([$nombre, $nombreArchivo, $enlace, $descripcion, $id]);

echo json_encode(["ok" => true, "msg" => "Patrocinador actualizado"]);
?>