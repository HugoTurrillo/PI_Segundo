<?php
require "config/conexion.php";
header("Content-Type: application/json");

// 1) OBTENER LA GALA
$sql = "SELECT * FROM gala LIMIT 1";
$res = $conexion->query($sql);

if (!$res || $res->num_rows == 0) {
    echo json_encode([
        "ok" => false,
        "msg" => "No existe ninguna gala"
    ]);
    exit;
}

$gala = $res->fetch_assoc();

// 2) OBTENER EL POST-EVENTO (EL PRIMERO QUE HAYA)
$sqlPost = "SELECT id_post_evento, resumen AS post_evento_texto, publicado 
            FROM post_evento 
            LIMIT 1";
$resPost = $conexion->query($sqlPost);

if ($resPost && $resPost->num_rows > 0) {
    $post = $resPost->fetch_assoc();
    $gala["id_post_evento"] = $post["id_post_evento"];
    $gala["post_evento_texto"] = $post["post_evento_texto"];
    $gala["post_evento_publicado"] = $post["publicado"];
} else {
    // Si no hay post_evento, devolvemos valores vacíos
    $gala["id_post_evento"] = null;
    $gala["post_evento_texto"] = "";
    $gala["post_evento_publicado"] = 0;
}

// 3) RESPUESTA FINAL
echo json_encode([
    "ok" => true,
    "data" => $gala
]);
