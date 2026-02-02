<?php
session_start();
require __DIR__ . "/config/conexion.php";


header("Content-Type: application/json");

// Solo permitir GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
    exit;
}

try {

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
    exit;

} catch (Exception $e) {

    echo json_encode([
        "ok" => false,
        "mensaje" => "Error al obtener ganadores.",
        "error" => $e->getMessage()
    ]);
    exit;
}
