<?php
// php/registro.php

require "conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
    exit;
}

$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

if (!$datos) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Datos no válidos."
    ]);
    exit;
}

$nombre = trim($datos["nombre"] ?? "");
$email = trim($datos["email"] ?? "");
$password = $datos["password"] ?? "";

if ($nombre === "" || $email === "" || $password === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Todos los campos son obligatorios."
    ]);
    exit;
}

// Comprobar si el email ya existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "El email ya está registrado."
    ]);
    exit;
}

// Cifrar contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$stmt = $pdo->prepare("
    INSERT INTO usuario (nombre_completo, email, password_hash, rol)
    VALUES (?, ?, ?, 'participante')
");
$stmt->execute([$nombre, $email, $password_hash]);

echo json_encode([
    "ok" => true,
    "mensaje" => "Registro completado."
]);
exit;
