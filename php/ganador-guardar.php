<?php
require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

$id_ganador     = intval($_POST["id_ganador"] ?? 0);
$id_categoria   = intval($_POST["id_categoria"] ?? 0);
$numero_premio  = intval($_POST["numero_premio"] ?? 0);
$id_candidatura = intval($_POST["id_candidatura"] ?? 0);

if ($id_categoria <= 0 || $numero_premio <= 0 || $id_candidatura <= 0) {
    echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
    exit;
}

/* ======================================================
   CASO EDICIÓN → POSIBLE INTERCAMBIO
====================================================== */
if ($id_ganador > 0) {

    // Obtener el ganador que estamos editando
    $stmt = $conexion->prepare("
        SELECT numero_premio
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

    $premio_actual = (int)$actual["numero_premio"];

    // ¿Existe otro ganador en el premio destino?
    $stmt = $conexion->prepare("
        SELECT id_ganador
        FROM ganadores
        WHERE id_categoria = ?
          AND numero_premio = ?
          AND id_ganador != ?
    ");
    $stmt->bind_param("iii", $id_categoria, $numero_premio, $id_ganador);
    $stmt->execute();
    $otro = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $conexion->begin_transaction();

    try {

        // Si hay otro ganador → INTERCAMBIO
        if ($otro) {

            // Mover el otro ganador al premio actual
            $stmt = $conexion->prepare("
                UPDATE ganadores
                SET numero_premio = ?
                WHERE id_ganador = ?
            ");
            $stmt->bind_param("ii", $premio_actual, $otro["id_ganador"]);
            $stmt->execute();
            $stmt->close();
        }

        // Actualizar el ganador editado
        $stmt = $conexion->prepare("
            UPDATE ganadores
            SET id_categoria = ?, numero_premio = ?, id_candidatura = ?
            WHERE id_ganador = ?
        ");
        $stmt->bind_param(
            "iiii",
            $id_categoria,
            $numero_premio,
            $id_candidatura,
            $id_ganador
        );
        $stmt->execute();
        $stmt->close();

        $conexion->commit();

        echo json_encode([
            "ok" => true,
            "msg" => "Ganador actualizado correctamente"
        ]);
        exit;

    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode([
            "ok" => false,
            "error" => "Error al intercambiar premios"
        ]);
        exit;
    }
}

/* ======================================================
   CASO CREAR GANADOR (SIN CAMBIOS)
====================================================== */

// comprobar duplicado
$stmt = $conexion->prepare("
    SELECT id_ganador
    FROM ganadores
    WHERE id_categoria = ? AND numero_premio = ?
");
$stmt->bind_param("ii", $id_categoria, $numero_premio);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["ok" => false, "error" => "Ese premio ya tiene ganador"]);
    exit;
}

// insertar
$stmt = $conexion->prepare("
    INSERT INTO ganadores (id_categoria, numero_premio, id_candidatura)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iii", $id_categoria, $numero_premio, $id_candidatura);
$stmt->execute();

echo json_encode([
    "ok" => true,
    "msg" => "Ganador asignado correctamente"
]);
exit;
