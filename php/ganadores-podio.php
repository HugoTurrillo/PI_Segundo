<?php
/**
 * Devuelvo el podio de una categoría: todos los premios (ocupados o vacíos).
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json; charset=utf-8");
requireApiOrganizer();

$id_categoria = intval($_GET["id_categoria"] ?? 0);

if ($id_categoria <= 0) {
    echo json_encode(["ok" => false, "error" => "Categoría no indicada"]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT id, nombre, premios, premio_fisico
    FROM categorias
    WHERE id = ?
");
$stmt->bind_param("i", $id_categoria);
$stmt->execute();
$categoria = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$categoria) {
    echo json_encode(["ok" => false, "error" => "Categoría no encontrada"]);
    exit;
}

$max_premios = (int)$categoria["premios"];

$stmt = $conexion->prepare("
    SELECT
        g.id_ganador,
        g.numero_premio,
        g.id_candidatura,
        cand.titulo_obra,
        cand.nombre_contacto
    FROM ganadores g
    INNER JOIN candidatura cand ON cand.id_candidatura = g.id_candidatura
    WHERE g.id_categoria = ?
    ORDER BY g.numero_premio
");
$stmt->bind_param("i", $id_categoria);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$por_premio = [];
foreach ($filas as $f) {
    $por_premio[(int)$f["numero_premio"]] = $f;
}

$podio = [];
for ($p = 1; $p <= $max_premios; $p++) {
    if (isset($por_premio[$p])) {
        $g = $por_premio[$p];
        $podio[] = [
            "numero_premio"   => $p,
            "ocupado"         => true,
            "id_ganador"      => (int)$g["id_ganador"],
            "id_candidatura"  => (int)$g["id_candidatura"],
            "titulo_obra"     => $g["titulo_obra"],
            "nombre_contacto" => $g["nombre_contacto"],
        ];
    } else {
        $podio[] = [
            "numero_premio" => $p,
            "ocupado"       => false,
        ];
    }
}

echo json_encode([
    "ok"        => true,
    "categoria" => $categoria,
    "podio"     => $podio,
]);
