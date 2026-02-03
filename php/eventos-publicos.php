<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

$sql = "
    SELECT id, titulo, fecha, hora, descripcion
    FROM evento
    ORDER BY fecha ASC, hora ASC
";

$result = $conexion->query($sql);

$eventosPorDia = [];

while ($row = $result->fetch_assoc()) {
    $fecha = $row["fecha"];

    if (!isset($eventosPorDia[$fecha])) {
        $eventosPorDia[$fecha] = [];
    }

    $eventosPorDia[$fecha][] = $row;
}

echo json_encode([
    "ok" => true,
    "eventos" => $eventosPorDia
]);
exit;
