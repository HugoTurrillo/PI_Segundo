<?php
require "config/conexion.php";
header("Content-Type: application/json");

session_start();
$id_usuario = $_SESSION["id_usuario"] ?? null;

if (!$id_usuario) {
    echo json_encode(["ok" => false, "mensaje" => "No autenticado"]);
    exit;
}

/* ============================
   1. RECIBIR DATOS
============================ */

$id_candidatura = intval($_POST["id_candidatura"] ?? 0);
$titulo = trim($_POST["tituloEditado"] ?? "");
$sinopsis = trim($_POST["sinopsisEditada"] ?? "");
$mensaje = trim($_POST["mensajeSubsanacion"] ?? "");

if ($id_candidatura === 0) {
    echo json_encode(["ok" => false, "mensaje" => "ID de candidatura inválido"]);
    exit;
}

if ($mensaje === "") {
    echo json_encode(["ok" => false, "mensaje" => "Debes escribir un mensaje de subsanación"]);
    exit;
}

/* ============================
   2. VERIFICAR QUE LA CANDIDATURA ES DEL USUARIO
============================ */

$sql = "SELECT * FROM candidatura WHERE id_candidatura = ? AND id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_candidatura, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "mensaje" => "No tienes permiso para editar esta candidatura"]);
    exit;
}

$candidatura = $res->fetch_assoc();
$stmt->close();

/* ============================
   3. SUBIR NUEVA PORTADA (OPCIONAL)
============================ */

$portada_ruta = null;

if (isset($_FILES["portadaEditada"]) && $_FILES["portadaEditada"]["size"] > 0) {

    $carpeta = "../uploads/candidaturas/";
    if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

    $nombre = time() . "_portada_" . basename($_FILES["portadaEditada"]["name"]);
    $portada_ruta = $carpeta . $nombre;

    move_uploaded_file($_FILES["portadaEditada"]["tmp_name"], $portada_ruta);
}

/* ============================
   4. ACTUALIZAR SOLO LO QUE EL USUARIO CAMBIA
============================ */

$sql = "UPDATE candidatura 
        SET titulo_obra = IF(?='', titulo_obra, ?),
            sinopsis = IF(?='', sinopsis, ?),
            mensaje_subsanacion = ?,
            portada_ruta = IF(? IS NULL, portada_ruta, ?),
            estado = 'en_proceso'
        WHERE id_candidatura = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sssssssi",
    $titulo, $titulo,
    $sinopsis, $sinopsis,
    $mensaje,
    $portada_ruta, $portada_ruta,
    $id_candidatura
);

$stmt->execute();
$stmt->close();

echo json_encode(["ok" => true, "mensaje" => "Subsanación enviada"]);