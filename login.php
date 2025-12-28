<?php
session_start();
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

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
            header("Location: admin/index.php");
        } else {
            header("Location: participante/index.php");
        }
        exit;

    } else {
        // Error de login
        $error = "Email o contraseña incorrectos";
        header("Location: login.html?error=1");
        exit;
    }
}