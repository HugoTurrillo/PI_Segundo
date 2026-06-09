<?php
/**
 * Previsualizo qué ocurrirá al asignar o cambiar un ganador en un premio (intercambios, vaciados, etc.).
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json; charset=utf-8");
requireApiOrganizer();

$id_categoria   = intval($_POST["id_categoria"] ?? $_GET["id_categoria"] ?? 0);
$numero_premio  = intval($_POST["numero_premio"] ?? $_GET["numero_premio"] ?? 0);
$id_candidatura = intval($_POST["id_candidatura"] ?? $_GET["id_candidatura"] ?? 0);
$id_ganador     = intval($_POST["id_ganador"] ?? $_GET["id_ganador"] ?? 0);

if ($id_categoria <= 0 || $numero_premio <= 0 || $id_candidatura <= 0) {
    echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT titulo_obra, nombre_contacto
    FROM candidatura
    WHERE id_candidatura = ?
");
$stmt->bind_param("i", $id_candidatura);
$stmt->execute();
$nuevo = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$nuevo) {
    echo json_encode(["ok" => false, "error" => "Candidatura no encontrada"]);
    exit;
}

$nuevo_label = $nuevo["titulo_obra"] . " — " . $nuevo["nombre_contacto"];

$stmt = $conexion->prepare("
    SELECT g.id_ganador, g.numero_premio, cand.titulo_obra, cand.nombre_contacto
    FROM ganadores g
    INNER JOIN candidatura cand ON cand.id_candidatura = g.id_candidatura
    WHERE g.id_categoria = ? AND g.numero_premio = ?
    LIMIT 1
");
$stmt->bind_param("ii", $id_categoria, $numero_premio);
$stmt->execute();
$ocupante_slot = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conexion->prepare("
    SELECT g.id_ganador, g.numero_premio, cand.titulo_obra, cand.nombre_contacto
    FROM ganadores g
    INNER JOIN candidatura cand ON cand.id_candidatura = g.id_candidatura
    WHERE g.id_categoria = ? AND g.id_candidatura = ?
    LIMIT 1
");
$stmt->bind_param("ii", $id_categoria, $id_candidatura);
$stmt->execute();
$premio_nuevo = $stmt->get_result()->fetch_assoc();
$stmt->close();

$lineas = [];
$hay_cambio = false;

    if ($ocupante_slot && (int)$ocupante_slot["id_ganador"] === $id_ganador
    && $premio_nuevo && (int)$premio_nuevo["id_ganador"] === $id_ganador) {
    echo json_encode([
        "ok"                    => true,
        "requiere_confirmacion" => false,
        "sin_cambios"           => true,
        "mensaje"               => "Sin cambios.",
        "lineas"                => [],
    ]);
    exit;
}

if ($id_ganador > 0 && $premio_nuevo && (int)$premio_nuevo["id_ganador"] === $id_ganador
    && (int)$premio_nuevo["numero_premio"] === $numero_premio) {
    echo json_encode([
        "ok"                    => true,
        "requiere_confirmacion" => false,
        "sin_cambios"           => true,
        "mensaje"               => "Sin cambios.",
        "lineas"                => [],
    ]);
    exit;
}

if (!$ocupante_slot) {
    $lineas[] = "{$nuevo_label} ocupará el {$numero_premio}º premio.";
    if ($premio_nuevo && (int)$premio_nuevo["numero_premio"] !== $numero_premio) {
        $lineas[] = "El {$premio_nuevo["numero_premio"]}º premio quedará vacío.";
        $hay_cambio = true;
    }
} else {
    $actual_label = $ocupante_slot["titulo_obra"] . " — " . $ocupante_slot["nombre_contacto"];
    $premio_actual = (int)$ocupante_slot["numero_premio"];

    if ($premio_nuevo && (int)$premio_nuevo["id_ganador"] !== (int)$ocupante_slot["id_ganador"]) {
        $premio_ant_nuevo = (int)$premio_nuevo["numero_premio"];
        if ($premio_ant_nuevo !== $numero_premio) {
            $lineas[] = "{$nuevo_label} pasará al {$numero_premio}º premio.";
            $lineas[] = "{$actual_label} pasará al {$premio_ant_nuevo}º premio.";
            $hay_cambio = true;
        } else {
            $lineas[] = "{$nuevo_label} ocupará el {$numero_premio}º premio.";
            $lineas[] = "{$actual_label} dejará el podio.";
            $hay_cambio = true;
        }
    } elseif (!$premio_nuevo) {
        $lineas[] = "{$nuevo_label} ocupará el {$numero_premio}º premio.";
        if ($actual_label !== $nuevo_label) {
            $lineas[] = "{$actual_label} dejará el podio.";
            $hay_cambio = true;
        }
    } else {
        $lineas[] = "{$nuevo_label} permanece en el {$numero_premio}º premio.";
    }
}

echo json_encode([
    "ok"                    => true,
    "requiere_confirmacion" => $hay_cambio,
    "mensaje"               => implode(" ", $lineas),
    "lineas"                => $lineas,
]);
