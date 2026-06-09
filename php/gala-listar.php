<?php
/**
 * Devuelvo la gala actual (solo hay una); la uso en el home y en el panel de gala.
 */

require __DIR__ . "/config/conexion.php";

header("Content-Type: application/json; charset=utf-8");

$sql = "SELECT id, titulo, fecha, lugar, descripcion FROM gala LIMIT 1";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $gala = $resultado->fetch_assoc();
    echo json_encode([
        "ok" => true,
        "data" => $gala
    ]);
} else {
    echo json_encode([
        "ok" => false,
        "msg" => "No existe ninguna gala"
    ]);
}
