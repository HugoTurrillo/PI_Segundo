<?php
require "conexion.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$titulo = $data["titulo_obra"];
$nombre = $data["nombre_contacto"];
$email = $data["email_contacto"];
$dni = $data["dni"];
$sinopsis = $data["sinopsis"];

// Usuario y edición por defecto (puedes ajustarlo)
$id_usuario = 1; // Organizador o usuario demo
$id_edicion = 1; // Edición activa

$stmt = $pdo->prepare("
    INSERT INTO candidatura 
    (id_usuario, id_edicion, titulo_obra, nombre_contacto, email_contacto, dni, sinopsis)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([$id_usuario, $id_edicion, $titulo, $nombre, $email, $dni, $sinopsis]);

echo json_encode(["ok" => true]);
