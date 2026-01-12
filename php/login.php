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

$email = trim($datos["email"] ?? "");
$password = $datos["password"] ?? "";

if ($email === "" || $password === "") {
    echo json_encode(["ok" => false, "mensaje" => "Email y contraseña son obligatorios."]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id_usuario, nombre_completo, email, password_hash, rol 
    FROM usuario 
    WHERE email = ? AND activo = 1
");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($password, $usuario["password_hash"])) {

    $_SESSION["id_usuario"] = $usuario["id_usuario"];
    $_SESSION["nombre"] = $usuario["nombre_completo"];
    $_SESSION["email"] = $usuario["email"];
    $_SESSION["rol"] = $usuario["rol"];

    if ($usuario["rol"] === "organizador") {
        $redir = "../organizador.php";
    } else {
        $redir = "../participantes.php";
    }

    echo json_encode(["ok" => true, "redireccion" => $redir]);
    exit;
}

echo json_encode(["ok" => false, "mensaje" => "Email o contraseña incorrectos."]);
exit;
