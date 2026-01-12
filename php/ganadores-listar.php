<?php
include("conexion.php");
header("Content-Type: application/json");

$sql = "
    SELECT 
        g.id_ganador,
        g.id_categoria,
        g.numero_premio,
        c.nombre AS categoria,
        cand.titulo_obra,
        cand.nombre_contacto
    FROM ganadores g
    INNER JOIN categorias c ON c.id = g.id_categoria
    INNER JOIN candidatura cand ON cand.id_candidatura = g.id_candidatura
    ORDER BY g.id_categoria, g.numero_premio
";

$stmt = $pdo->query($sql);
$ganadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "ok" => true,
    "data" => $ganadores
]);
