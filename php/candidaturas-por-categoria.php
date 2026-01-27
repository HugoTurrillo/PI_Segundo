<?php
session_start();
require "config/conexion.php";
header("Content-Type: application/json");

// ============================
// VALIDAR SESIÓN Y ROL
// ============================
if (!isset($_SESSION["id_usuario"]) || 
   !in_array($_SESSION["rol"], ["organizador", "jurado"])) {

    echo json_encode([
        "ok" => false,
        "msg" => "No autorizado"
    ]);
    exit;
}

// ============================
// VALIDAR MÉTODO
// ============================
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode([
        "ok" => false,
        "msg" => "Método no permitido"
    ]);
    exit;
}

// ============================
// VALIDAR CATEGORÍA
// ============================
$id_categoria = intval($_GET["id_categoria"] ?? 0);

if ($id_categoria <= 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "ID de categoría no válido"
    ]);
    exit;
}

// ============================
// CONSULTA
// ============================
$stmt = $conexion->prepare("
    SELECT 
        id_candidatura,
        id_usuario,
        titulo_obra,
        sinopsis,
        estado,
        motivo_rechazo,
        mensaje_subsanacion,
        dni,
        id_categoria
    FROM candidatura
    WHERE id_categoria = ?
");

$stmt->bind_param("i", $id_categoria);
$stmt->execute();

$resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();

echo json_encode([
    "ok" => true,
    "candidaturas" => $resultado
]);
exit;