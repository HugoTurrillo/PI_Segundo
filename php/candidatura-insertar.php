<?php
session_start();
require "conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido."]);
    exit;
}

$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

if (!$datos) {
    echo json_encode(["ok" => false, "mensaje" => "Datos no válidos."]);
    exit;
}

$titulo_obra = trim($datos["titulo_obra"] ?? "");
$nombre_contacto = trim($datos["nombre_contacto"] ?? "");
$email_contacto = trim($datos["email_contacto"] ?? "");
$dni = trim($datos["dni"] ?? "");
$sinopsis = trim($datos["sinopsis"] ?? "");

if ($titulo_obra === "" || $nombre_contacto === "" || $email_contacto === "" || $dni === "") {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios."]);
    exit;
}

// Edición activa
$stmt = $pdo->query("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");
$ed = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ed) {
    echo json_encode(["ok" => false, "mensaje" => "No hay edición activa."]);
    exit;
}

$id_edicion = $ed["id_edicion"];

// Crear usuario participante si no existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$email_contacto]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if ($u) {
    $id_usuario = $u["id_usuario"];
} else {
    $pass = password_hash("participante123", PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO usuario (nombre_completo, email, password_hash, rol)
        VALUES (?, ?, ?, 'participante')
    ");
    $stmt->execute([$nombre_contacto, $email_contacto, $pass]);
    $id_usuario = $pdo->lastInsertId();
}

// Insertar candidatura
$stmt = $pdo->prepare("
    INSERT INTO candidatura (
        id_usuario, id_edicion, titulo_obra, sinopsis,
        nombre_contacto, email_contacto, dni, estado
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, 'en_proceso')
");

$stmt->execute([
    $id_usuario, $id_edicion, $titulo_obra, $sinopsis,
    $nombre_contacto, $email_contacto, $dni
]);

// Iniciar sesión
$_SESSION["id_usuario"] = $id_usuario;
$_SESSION["nombre"] = $nombre_contacto;
$_SESSION["rol"] = "participante";

echo json_encode([
    "ok" => true,
    "redireccion" => "../HTML/participantes.html"
]);
exit;
