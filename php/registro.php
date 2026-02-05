<?php
require __DIR__ . "/config/conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok"=>false,"mensaje"=>"Método no permitido."]);
    exit;
}

$entrada = file_get_contents("php://input");
$datos = json_decode($entrada, true);

if (!$datos) {
    echo json_encode(["ok"=>false,"mensaje"=>"Datos no válidos."]);
    exit;
}

$nombre = trim($datos["nombre"] ?? "");
$email = trim($datos["email"] ?? "");
$password = $datos["password"] ?? "";
$rol_participante = $datos["rol_participante"] ?? "";

if ($nombre === "" || $email === "" || $password === "") {
    echo json_encode(["ok"=>false,"mensaje"=>"Todos los campos son obligatorios."]);
    exit;
}

if (!in_array($rol_participante, ["alumno","alumni","profesional"])) {
    echo json_encode(["ok"=>false,"mensaje"=>"Perfil inválido."]);
    exit;
}

/* COMPROBAR EMAIL EXISTENTE */
$stmt = $conexion->prepare(
    "SELECT id_usuario FROM usuario WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["ok"=>false,"mensaje"=>"El email ya está registrado."]);
    exit;
}
$stmt->close();

/* INSERTAR USUARIO */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conexion->prepare(
    "INSERT INTO usuario (nombre_completo,email,password_hash,rol,rol_participante)
     VALUES (?,?,?,'participante',?)"
);
$stmt->bind_param("ssss", $nombre, $email, $password_hash, $rol_participante);
$stmt->execute();
$stmt->close();

echo json_encode(["ok"=>true,"mensaje"=>"Registro completado."]);
exit;
