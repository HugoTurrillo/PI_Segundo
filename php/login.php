<?php
// php/login.php

session_start();
require "conexion.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Leer JSON del cuerpo de la petición
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
            "mensaje" => "Email y contraseña son obligatorios."
        ]);
        exit;
    }

    // Buscar usuario por email
    $stmt = $pdo->prepare("SELECT id_usuario, nombre_completo, password_hash, rol 
                           FROM usuario 
                           WHERE email = ? AND activo = 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario["password_hash"])) {

        // Guardar datos en sesión
        $_SESSION["id_usuario"] = $usuario["id_usuario"];
        $_SESSION["nombre"] = $usuario["nombre_completo"];
        $_SESSION["rol"] = $usuario["rol"];

        // Redirigir según rol
        if ($usuario["rol"] === "organizador") {
            $redir = "../HTML/organizador.html";
        } else {
            $redir = "../HTML/participante.html";
        }

        echo json_encode([
            "ok" => true,
            "redireccion" => $redir
        ]);
        exit;

    } else {
        echo json_encode([
            "ok" => false,
            "mensaje" => "Email o contraseña incorrectos."
        ]);
        exit;
    }
} else {
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);
}
