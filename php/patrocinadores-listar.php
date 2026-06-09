<?php
/**
 * Listo todos los patrocinadores ordenados por ID; lo uso en el home y en el panel de organizador.
 */

require __DIR__ . "/config/conexion.php";
header("Content-Type: application/json");

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
