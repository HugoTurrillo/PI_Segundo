<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

$conexion = new mysqli($host, $user, $pass, $dbname);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

$sqlEdicion = "SELECT id_edicion, anio FROM edicion_festival WHERE activa = 1 LIMIT 1";
$resEdicion = $conexion->query($sqlEdicion);

if (!$resEdicion || $resEdicion->num_rows === 0) {
    die("No hay edición activa.");
}

$edicion = $resEdicion->fetch_assoc();
$id_edicion = (int)$edicion['id_edicion'];

$sqlParticipantes = "
    SELECT DISTINCT c.email_contacto
    FROM candidatura c
    WHERE c.id_edicion = $id_edicion
";

$resPart = $conexion->query($sqlParticipantes);

if ($resPart && $resPart->num_rows > 0) {
    while ($row = $resPart->fetch_assoc()) {
        $email = $row['email_contacto'];
        // Aquí integrarías mail() o PHPMailer.
        // mail($email, "Aviso Post‑evento", "En un mes se eliminarán tus datos del sistema.", "From: ...");
    }
}

echo "Avisos procesados.";

$conexion->close();
