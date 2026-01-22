<?php
session_start();
require "config/conexion.php";

header("Content-Type: application/json");

// ============================
// VALIDAR MÉTODO
// ============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "mensaje" => "Método no permitido"]);
    exit;
}

// ============================
// LEER JSON
// ============================
$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["ok" => false, "mensaje" => "Datos no válidos"]);
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
$titulo = trim($data["titulo_obra"] ?? "");
$nombre = trim($data["nombre_contacto"] ?? "");
$email = trim($data["email_contacto"] ?? "");
$dni = trim($data["dni"] ?? "");
$sinopsis = trim($data["sinopsis"] ?? "");


if ($titulo === "" || $nombre === "" || $email === "" || $dni === "") {
    echo json_encode(["ok" => false, "mensaje" => "Faltan datos obligatorios"]);
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
// Edición activa
$stmt = $conexion->prepare(
    "SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1"
);
$stmt->execute();
$res = $stmt->get_result();
$ed = $res->fetch_assoc();


if (!$ed) {
    echo json_encode(["ok" => false, "mensaje" => "No hay edición activa"]);
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
// Usuario
$stmt = $conexion->prepare(
    "SELECT id_usuario FROM usuario WHERE email=?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$u = $res->fetch_assoc();


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

    $stmt = $conexion->prepare(
        "INSERT INTO usuario (nombre_completo, email, password_hash, rol)
         VALUES (?, ?, ?, 'participante')"
    );
    $stmt->bind_param("sss", $nombre, $email, $pass);
    $stmt->execute();
    $id_usuario = $conexion->insert_id;


// Insertar candidatura
$stmt = $conexion->prepare(
    "INSERT INTO candidatura
     (id_usuario, id_edicion, titulo_obra, sinopsis,
      nombre_contacto, email_contacto, dni, estado)
     VALUES (?, ?, ?, ?, ?, ?, ?, 'en_proceso')"
);
$stmt->bind_param(
    "iisssss",
    $id_usuario,
    $id_edicion,
    $titulo,
    $sinopsis,
    $nombre,
    $email,
    $dni
);
$stmt->execute();

// Sesión

$_SESSION["id_usuario"] = $id_usuario;
$_SESSION["nombre"] = $nombre;
$_SESSION["rol"] = "participante";

// ============================
// RESPUESTA FINAL
// ============================
echo json_encode([
    "ok" => true,
    "redireccion" => "../HTML/participantes.html"
]);
