<?php
require "config/conexion.php";
header("Content-Type: application/json");

// ============================
// OBTENER TODOS LOS PATROCINADORES
// ============================
$stmt = $conexion->prepare("SELECT * FROM patrocinador ORDER BY id_patrocinador DESC");
$stmt->execute();

$resultado = $stmt->get_result();
$patrocinadores = $resultado->fetch_all(MYSQLI_ASSOC);

$stmt->close();

// ============================
// RESPUESTA
// ============================
echo json_encode($patrocinadores);
?>
