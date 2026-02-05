<?php
require "config/conexion.php";
header("Content-Type: application/json");

$categoria = $_GET["categoria"] ?? "todas";

$sql = "
  SELECT 
    c.id_candidatura,
    c.titulo_obra,
    c.nombre_contacto,
    c.email_contacto,
    u.rol_participante,
    c.estado,
    c.motivo_rechazo,
    c.mensaje_subsanacion,
    c.id_categoria,
    cat.nombre AS categoria_nombre
  FROM candidatura c
  INNER JOIN usuario u ON u.id_usuario = c.id_usuario
  LEFT JOIN categorias cat ON cat.id = c.id_categoria
";

if ($categoria !== "todas") {

    // Mapeo del desplegable → nombre real en la tabla categorias
    $map = [
        "alumnos" => "alumnos",
        "alumni" => "Alumni",
        "profesionales" => "profesionales"
    ];

    if (isset($map[$categoria])) {
        $sql .= " WHERE cat.nombre = ? ";
        $categoria = $map[$categoria];
    }
}

$sql .= " ORDER BY c.fecha_creacion DESC ";

$stmt = $conexion->prepare($sql);

if ($categoria !== "todas") {
    $stmt->bind_param("s", $categoria);
}

$stmt->execute();
$res = $stmt->get_result();

echo json_encode($res->fetch_all(MYSQLI_ASSOC));
exit;
