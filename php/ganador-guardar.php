<?php
/**
 * Guardo o actualizo un ganador por categoría y número de premio.
 * Una candidatura solo puede tener un premio por categoría; al cambiar premio o nominado se intercambian posiciones.
 */

require __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/config/auth.php";
header("Content-Type: application/json");
requireApiOrganizer();

$id_ganador     = intval($_POST["id_ganador"] ?? 0);
$id_categoria   = intval($_POST["id_categoria"] ?? 0);
$numero_premio  = intval($_POST["numero_premio"] ?? 0);
$id_candidatura = intval($_POST["id_candidatura"] ?? 0);

if ($id_categoria <= 0 || $numero_premio <= 0 || $id_candidatura <= 0) {
    echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
    exit;
}

$stmt_cat = $conexion->prepare("SELECT premios FROM categorias WHERE id = ?");
$stmt_cat->bind_param("i", $id_categoria);
$stmt_cat->execute();
$cat = $stmt_cat->get_result()->fetch_assoc();
$stmt_cat->close();

if (!$cat) {
    echo json_encode(["ok" => false, "error" => "Categoría no encontrada"]);
    exit;
}

$max_premios = (int)$cat["premios"];
if ($numero_premio > $max_premios) {
    echo json_encode([
        "ok" => false,
        "error" => "Esta categoría solo tiene {$max_premios} premio(s)"
    ]);
    exit;
}

// Asigno la categoría a la candidatura si aún no la tiene
$stmt_check = $conexion->prepare("
    SELECT id_categoria FROM candidatura WHERE id_candidatura = ?
");
$stmt_check->bind_param("i", $id_candidatura);
$stmt_check->execute();
$cand = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();

if (!$cand) {
    echo json_encode(["ok" => false, "error" => "Candidatura no encontrada"]);
    exit;
}

if ($cand["id_categoria"] === null || (int)$cand["id_categoria"] !== (int)$id_categoria) {
    $stmt_nom = $conexion->prepare("
        UPDATE candidatura SET id_categoria = ? WHERE id_candidatura = ?
    ");
    $stmt_nom->bind_param("ii", $id_categoria, $id_candidatura);
    $stmt_nom->execute();
    $stmt_nom->close();
}

// Si la candidatura ya tiene premio en esta categoría, actualizo ese registro
if ($id_ganador <= 0) {
    $stmt = $conexion->prepare("
        SELECT id_ganador
        FROM ganadores
        WHERE id_categoria = ? AND id_candidatura = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $id_categoria, $id_candidatura);
    $stmt->execute();
    $existente = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($existente) {
        $id_ganador = (int)$existente["id_ganador"];
    }
}

/* ======================================================
   CASO EDICIÓN → INTERCAMBIO DE PREMIOS Y/O CANDIDATURAS
====================================================== */
if ($id_ganador > 0) {

    $stmt = $conexion->prepare("
        SELECT numero_premio, id_candidatura
        FROM ganadores
        WHERE id_ganador = ?
    ");
    $stmt->bind_param("i", $id_ganador);
    $stmt->execute();
    $actual = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$actual) {
        echo json_encode(["ok" => false, "error" => "Ganador no encontrado"]);
        exit;
    }

    $premio_actual      = (int)$actual["numero_premio"];
    $candidatura_actual = (int)$actual["id_candidatura"];

    $conexion->begin_transaction();

    // 1) Cambio de premio → intercambio con quien ocupe el premio destino
    if ($numero_premio !== $premio_actual) {
        $stmt = $conexion->prepare("
            SELECT id_ganador
            FROM ganadores
            WHERE id_categoria = ?
              AND numero_premio = ?
              AND id_ganador != ?
            LIMIT 1
        ");
        $stmt->bind_param("iii", $id_categoria, $numero_premio, $id_ganador);
        $stmt->execute();
        $otro_premio = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($otro_premio) {
            $stmt = $conexion->prepare("
                UPDATE ganadores
                SET numero_premio = ?
                WHERE id_ganador = ?
            ");
            $stmt->bind_param("ii", $premio_actual, $otro_premio["id_ganador"]);
            if (!$stmt->execute()) {
                $conexion->rollback();
                echo json_encode(["ok" => false, "error" => "Error al intercambiar premios"]);
                exit;
            }
            $stmt->close();
        }
    }

    // 2) Cambio de nominado → intercambio de candidaturas con quien ya tenga ese premio
    if ($id_candidatura !== $candidatura_actual) {
        $stmt = $conexion->prepare("
            SELECT id_ganador
            FROM ganadores
            WHERE id_categoria = ?
              AND id_candidatura = ?
              AND id_ganador != ?
            LIMIT 1
        ");
        $stmt->bind_param("iii", $id_categoria, $id_candidatura, $id_ganador);
        $stmt->execute();
        $otro_candidatura = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($otro_candidatura) {
            $stmt = $conexion->prepare("
                UPDATE ganadores
                SET id_candidatura = ?
                WHERE id_ganador = ?
            ");
            $stmt->bind_param("ii", $candidatura_actual, $otro_candidatura["id_ganador"]);
            if (!$stmt->execute()) {
                $conexion->rollback();
                echo json_encode(["ok" => false, "error" => "Error al intercambiar candidaturas"]);
                exit;
            }
            $stmt->close();
        }
    }

    // 3) Elimino filas duplicadas de la misma candidatura (solo una fila por candidatura)
    $stmt = $conexion->prepare("
        DELETE FROM ganadores
        WHERE id_categoria = ?
          AND id_candidatura = ?
          AND id_ganador != ?
    ");
    $stmt->bind_param("iii", $id_categoria, $id_candidatura, $id_ganador);
    if (!$stmt->execute()) {
        $conexion->rollback();
        echo json_encode(["ok" => false, "error" => "Error al limpiar duplicados"]);
        exit;
    }
    $stmt->close();

    // 4) Actualizo el ganador editado
    $stmt = $conexion->prepare("
        UPDATE ganadores
        SET id_categoria = ?, numero_premio = ?, id_candidatura = ?
        WHERE id_ganador = ?
    ");
    $stmt->bind_param("iiii", $id_categoria, $numero_premio, $id_candidatura, $id_ganador);
    if (!$stmt->execute()) {
        $conexion->rollback();
        echo json_encode(["ok" => false, "error" => "Error al actualizar ganador"]);
        exit;
    }
    $stmt->close();

    $conexion->commit();

    echo json_encode([
        "ok" => true,
        "msg" => "Ganador actualizado correctamente"
    ]);
    exit;
}

/* ======================================================
   CASO CREAR GANADOR (Gestionar ganadores)
====================================================== */

$conexion->begin_transaction();

$stmt = $conexion->prepare("
    SELECT id_ganador
    FROM ganadores
    WHERE id_categoria = ? AND numero_premio = ?
    LIMIT 1
");
$stmt->bind_param("ii", $id_categoria, $numero_premio);
$stmt->execute();
$ocupante = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($ocupante) {
    $hueco = null;
    for ($p = 1; $p <= $max_premios; $p++) {
        if ($p === $numero_premio) {
            continue;
        }
        $stmt_h = $conexion->prepare("
            SELECT id_ganador FROM ganadores
            WHERE id_categoria = ? AND numero_premio = ?
            LIMIT 1
        ");
        $stmt_h->bind_param("ii", $id_categoria, $p);
        $stmt_h->execute();
        $libre = $stmt_h->get_result()->num_rows === 0;
        $stmt_h->close();
        if ($libre) {
            $hueco = $p;
            break;
        }
    }

    if ($hueco === null) {
        $conexion->rollback();
        echo json_encode([
            "ok" => false,
            "error" => "Ese premio ya tiene ganador y no hay posiciones libres. Edita un ganador para intercambiar."
        ]);
        exit;
    }

    $stmt = $conexion->prepare("
        UPDATE ganadores SET numero_premio = ? WHERE id_ganador = ?
    ");
    $stmt->bind_param("ii", $hueco, $ocupante["id_ganador"]);
    if (!$stmt->execute()) {
        $conexion->rollback();
        echo json_encode(["ok" => false, "error" => "Error al reubicar ganador"]);
        exit;
    }
    $stmt->close();
}

$stmt = $conexion->prepare("
    INSERT INTO ganadores (id_categoria, numero_premio, id_candidatura)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iii", $id_categoria, $numero_premio, $id_candidatura);
if (!$stmt->execute()) {
    $conexion->rollback();
    echo json_encode(["ok" => false, "error" => "Error al asignar ganador"]);
    exit;
}
$stmt->close();

$conexion->commit();

echo json_encode([
    "ok" => true,
    "msg" => "Ganador asignado correctamente"
]);
exit;
