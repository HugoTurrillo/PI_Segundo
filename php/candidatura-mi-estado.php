<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "ok" => false,
        "msg" => "No autenticado"
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

$stmt = $conexion->prepare("
    SELECT 
        c.id_candidatura,
        c.titulo_obra,
        c.sinopsis,
        c.estado,
        c.motivo_rechazo,
        c.mensaje_subsanacion,
        c.nombre_contacto,
        c.email_contacto,
        c.dni,
        e.titulo AS edicion
    FROM candidatura c
    INNER JOIN edicion_festival e ON e.id_edicion = c.id_edicion
    WHERE c.id_usuario = ?
    LIMIT 1
");

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode([
        "ok" => true,
        "candidatura" => null
    ]);
    exit;
}

echo json_encode([
    "ok" => true,
    "candidatura" => $res->fetch_assoc()
]);
exit;
