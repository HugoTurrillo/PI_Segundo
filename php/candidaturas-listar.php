<?php
require "config/conexion.php";
header("Content-Type: application/json");

$categoria = $_GET["categoria"] ?? "todas";

/* ============================
   CONSULTA CORREGIDA DEFINITIVA
============================ */
$sql = "
  SELECT 
    c.id_candidatura,
    c.titulo_obra,

    /* AUTOR / EMAIL (fallback seguro) */
    COALESCE(NULLIF(c.nombre_contacto,''), u.nombre_completo) AS nombre_contacto,
    COALESCE(NULLIF(c.email_contacto,''), u.email) AS email_contacto,

    /* PERFIL:
       1. Usa rol_participante si existe
       2. Si NO existe, dedúcelo por la categoría
    */
    CASE
        WHEN u.rol_participante IS NOT NULL THEN u.rol_participante
        WHEN cat.nombre = 'Alumnos' THEN 'alumno'
        WHEN cat.nombre = 'Alumni' THEN 'alumni'
        WHEN cat.nombre = 'Profesionales' THEN 'profesional'
        ELSE '—'
    END AS rol_participante,

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

    $map = [
        "alumnos" => "Alumnos",
        "alumni" => "Alumni",
        "profesionales" => "Profesionales"
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
