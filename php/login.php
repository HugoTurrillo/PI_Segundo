<?php
session_start();
require __DIR__ . "/config/conexion.php";

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

$email = trim($datos["email"] ?? "");
$password = $datos["password"] ?? "";

if ($email === "" || $password === "") {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Email y contraseña obligatorios."
    ]);
    exit;
}

$stmt = $conexion->prepare("
    SELECT id_usuario, nombre_completo, password_hash, rol
    FROM usuario
    WHERE email = ? AND activo = 1
");
$stmt->bind_param("s", $email);
$stmt->execute();

$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Email o contraseña incorrectos."
    ]);
    exit;
}

$usuario = $res->fetch_assoc();

if (!password_verify($password, $usuario["password_hash"])) {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Email o contraseña incorrectos."
    ]);
    exit;
}

$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nombre"] = $usuario["nombre_completo"];
$_SESSION["rol"] = $usuario["rol"];
$_SESSION["email"] = $email;

$redir = ($usuario["rol"] === "organizador")
    ? "../HTML/organizador.html"
    : "../HTML/participante.html"; 

echo json_encode([
    "ok" => true,
    "redireccion" => $redir
]);
exit;
