<?php
require "config/conexion.php";
header("Content-Type: application/json");

$categoria = $_GET["categoria"] ?? "todas";

/* ============================
<<<<<<< HEAD
   CONSULTA
=======
   CONSULTA CORREGIDA
>>>>>>> ddc7d9b914ae1fa27af397b1db366b0b79b1a657
============================ */
$sql = "
  SELECT 
    c.id_candidatura,
    c.titulo_obra,

<<<<<<< HEAD
    /* AUTOR / EMAIL */
    COALESCE(NULLIF(c.nombre_contacto,''), u.nombre_completo) AS nombre_contacto,
    COALESCE(NULLIF(c.email_contacto,''), u.email) AS email_contacto,

   
    COALESCE(NULLIF(u.rol_participante,''), '—') AS rol_participante,
=======
    /* FIX DEFINITIVO */
    COALESCE(c.nombre_contacto, u.nombre_completo) AS nombre_contacto,
    COALESCE(c.email_contacto, u.email) AS email_contacto,
    u.rol_participante,
>>>>>>> ddc7d9b914ae1fa27af397b1db366b0b79b1a657

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
