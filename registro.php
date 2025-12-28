<?php
require "conexion.php";

// Solo permitir acceso por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<h2>Acceso no permitido</h2>";
    echo "<p>Este archivo solo se ejecuta al enviar el formulario de registro.</p>";
    echo "<a href='../HTML/registro.html'>Volver al formulario</a>";
    exit;
}

$nombre = trim($_POST["nombre"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];

// Comprobar si el email ya existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    header("Location: ../HTML/registro.html?error=email");
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

// Redirigir al login
header("Location: ../HTML/login.html?registro=ok");
exit;