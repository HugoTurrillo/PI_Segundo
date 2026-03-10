<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json; charset=utf-8");

$sql = "
    SELECT 
        g.id_ganador,
        g.id_categoria,
        g.numero_premio,
        c.nombre AS categoria,
        cand.id_candidatura,
        cand.titulo_obra,
        cand.nombre_contacto
    FROM ganadores g
    INNER JOIN categorias c ON c.id = g.id_categoria
    INNER JOIN candidatura cand ON cand.id_candidatura = g.id_candidatura
    ORDER BY g.id_categoria, g.numero_premio
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "ok" => false,
        "error" => "Error en prepare(): " . $conexion->error
    ]);
    exit;
}

$stmt->execute();
$res = $stmt->get_result();

echo json_encode([
    "ok" => true,
    "data" => $res->fetch_all(MYSQLI_ASSOC)
]);
