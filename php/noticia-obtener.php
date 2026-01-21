<?php
require "config/conexion.php";
header("Content-Type: application/json");

if (!isset($_GET["id_noticia"])) {
    echo json_encode(["ok" => false, "msg" => "ID no recibido"]);
    exit;
}

$id = intval($_GET["id_noticia"]);

$stmt = $conexion->prepare("SELECT * FROM noticia WHERE id_noticia = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows >= 1) {
    $noticia = $resultado->fetch_assoc();
    echo json_encode([
        "ok" => true,
        "noticia" => $noticia
    ]);
    exit;
}

// Si no existe la noticia
echo json_encode([
    "ok" => false,
    "msg" => "Noticia no encontrada"
]);
exit;
?>