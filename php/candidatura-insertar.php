<?php
session_start();
require "conexion.php";

header("Content-Type: application/json");

// ============================
// VALIDAR MÉTODO
// ============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido."]);
    exit;
}

// ============================
// LEER JSON
// ============================
$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

if (!$datos) {
    echo json_encode(["ok" => false, "mensaje" => "Datos no válidos."]);
    exit;
}

// ============================
// VALIDAR CAMPOS
// ============================
$titulo_obra = trim($datos["titulo_obra"] ?? "");
$nombre_contacto = trim($datos["nombre_contacto"] ?? "");
$email_contacto = trim($datos["email_contacto"] ?? "");
$dni = trim($datos["dni"] ?? "");
$sinopsis = trim($datos["sinopsis"] ?? "");

if ($titulo_obra === "" || $nombre_contacto === "" || $email_contacto === "" || $dni === "") {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios."]);
    exit;
}

// ============================
// OBTENER EDICIÓN ACTIVA
// ============================
$stmt = $conexion->prepare("SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1");
$stmt->execute();
$resultado = $stmt->get_result();
$ed = $resultado->fetch_assoc();
$stmt->close();

if (!$ed) {
    echo json_encode(["ok" => false, "mensaje" => "No hay edición activa."]);
    exit;
}

$id_edicion = $ed["id_edicion"];

// ============================
// BUSCAR USUARIO POR EMAIL
// ============================
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->bind_param("s", $email_contacto);
$stmt->execute();
$resultado = $stmt->get_result();
$u = $resultado->fetch_assoc();
$stmt->close();

if ($u) {
    $id_usuario = $u["id_usuario"];
} else {
    // Crear usuario participante
    $pass = password_hash("participante123", PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("
        INSERT INTO usuario (nombre_completo, email, password_hash, rol)
        VALUES (?, ?, ?, 'participante')
    ");
    $stmt->bind_param("sss", $nombre_contacto, $email_contacto, $pass);
    $stmt->execute();
    $id_usuario = $stmt->insert_id;
    $stmt->close();
}

// ============================
// INSERTAR CANDIDATURA
// ============================
$stmt = $conexion->prepare("
    INSERT INTO candidatura (
        id_usuario, id_edicion, titulo_obra, sinopsis,
        nombre_contacto, email_contacto, dni, estado
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, 'en_proceso')
");

$stmt->bind_param(
    "iisssss",
    $id_usuario,
    $id_edicion,
    $titulo_obra,
    $sinopsis,
    $nombre_contacto,
    $email_contacto,
    $dni
);

$stmt->execute();
$stmt->close();

// ============================
// INICIAR SESIÓN
// ============================
$_SESSION["id_usuario"] = $id_usuario;
$_SESSION["nombre"] = $nombre_contacto;
$_SESSION["rol"] = "participante";

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode([
    "ok" => true,
    "redireccion" => "../HTML/participantes.html"
]);
exit;
