<?php
include("conexion.php");
header("Content-Type: application/json; charset=utf-8");

// ============================
// LISTAR TODOS LOS EVENTOS DE GALA
// ============================

// Opción 1: todos los campos
// $stmt = $pdo->query("SELECT * FROM gala ORDER BY id DESC");

// Opción 2: campos concretos (recomendado)
$stmt = $pdo->query("
    SELECT id, titulo, fecha, hora, lugar, descripcion, imagen
    FROM gala
    ORDER BY id DESC
");

if (!$stmt) {
    echo json_encode(["ok" => false, "msg" => "Error en la consulta"]);
    exit();
}

$gala = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no hay registros, devolvemos array vacío (no null)
echo json_encode($gala);
