<?php
require "config/conexion.php";
header("Content-Type: application/json");

// Consulta sin parámetros
$stmt = $conexion->prepare("SELECT * FROM noticia ORDER BY id_noticia DESC");
$stmt->execute();

// Obtener resultados
$resultado = $stmt->get_result();
$noticias = $resultado->fetch_all(MYSQLI_ASSOC);

// Devolver JSON
echo json_encode($noticias);
exit;
?>