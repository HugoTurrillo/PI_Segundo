<?php
/**
 * Guardo el contenido del postevento (resumen, ganadores, cortos) para la edición activa; solo organizador.
 */

require_once __DIR__ . "/config/auth.php";
requireApiOrganizer();
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

$conexion = new mysqli($host, $user, $pass, $dbname);

if ($conexion->connect_error) {
    echo json_encode(["ok" => false, "msg" => "Error de conexión a la base de datos"]);
    exit;
}

$conexion->set_charset("utf8mb4");

$resumen = $_POST['resumen'] ?? '';
$ganador_alumnos = $_POST['ganador_alumnos'] ?? '';
$corto_alumnos = $_POST['corto_alumnos'] ?? '';
$ganador_alumni = $_POST['ganador_alumni'] ?? '';
$corto_alumni = $_POST['corto_alumni'] ?? '';
$ganador_profesional = $_POST['ganador_profesional'] ?? '';
$corto_profesional = $_POST['corto_profesional'] ?? '';
$anio = $_POST['anio'] ?? null;
$participantes = $_POST['participantes'] ?? null;
$ganadores_json = $_POST['ganadores_json'] ?? '';

if (trim($resumen) === '' || trim($anio) === '' || trim($participantes) === '') {
    echo json_encode(["ok" => false, "msg" => "Faltan campos obligatorios."]);
    exit;
}

$sqlEdicion = "SELECT id_edicion FROM edicion_festival WHERE activa = 1 LIMIT 1";
$resEdicion = $conexion->query($sqlEdicion);

if (!$resEdicion || $resEdicion->num_rows === 0) {
    echo json_encode(["ok" => false, "msg" => "No se encontró una edición activa."]);
    exit;
}

$filaEdicion = $resEdicion->fetch_assoc();
$id_edicion = (int)$filaEdicion['id_edicion'];

$stmt = $conexion->prepare("
    INSERT INTO post_evento
    (id_edicion, resumen, ganador_alumnos, corto_alumnos, ganador_alumni, corto_alumni,
     ganador_profesional, corto_profesional, anio_edicion, numero_participantes, ganadores_json)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isssssssiis",
    $id_edicion,
    $resumen,
    $ganador_alumnos,
    $corto_alumnos,
    $ganador_alumni,
    $corto_alumni,
    $ganador_profesional,
    $corto_profesional,
    $anio,
    $participantes,
    $ganadores_json
);

if (!$stmt->execute()) {
    echo json_encode(["ok" => false, "msg" => "Error al guardar el post‑evento."]);
    exit;
}

$id_post_evento = $stmt->insert_id;
$stmt->close();

$dirImagenes = __DIR__ . "/../uploads/postevento/";
$dirCortos = __DIR__ . "/../uploads/postevento_cortos/";

if (!is_dir($dirImagenes)) {
    mkdir($dirImagenes, 0777, true);
}
if (!is_dir($dirCortos)) {
    mkdir($dirCortos, 0777, true);
}

if (!empty($_FILES['imagenes']['name'][0])) {
    $total = count($_FILES['imagenes']['name']);
    for ($i = 0; $i < $total; $i++) {
        $nombreTmp = $_FILES['imagenes']['tmp_name'][$i];
        $nombreOriginal = basename($_FILES['imagenes']['name'][$i]);
        $ext = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nuevoNombre = "img_" . $id_post_evento . "_" . time() . "_" . $i . "." . $ext;
        $rutaDestino = $dirImagenes . $nuevoNombre;

        if (move_uploaded_file($nombreTmp, $rutaDestino)) {
            $rutaBD = "uploads/postevento/" . $nuevoNombre;
            $stmtImg = $conexion->prepare("
                INSERT INTO post_evento_imagen (id_post_evento, ruta_imagen)
                VALUES (?, ?)
            ");
            $stmtImg->bind_param("is", $id_post_evento, $rutaBD);
            $stmtImg->execute();
            $stmtImg->close();
        }
    }
}

if (!empty($_FILES['cortos_ganadores']['name'][0])) {
    $total = count($_FILES['cortos_ganadores']['name']);
    for ($i = 0; $i < $total; $i++) {
        $nombreTmp = $_FILES['cortos_ganadores']['tmp_name'][$i];
        $nombreOriginal = basename($_FILES['cortos_ganadores']['name'][$i]);
        $ext = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nuevoNombre = "corto_" . $id_post_evento . "_" . time() . "_" . $i . "." . $ext;
        $rutaDestino = $dirCortos . $nuevoNombre;

        if (move_uploaded_file($nombreTmp, $rutaDestino)) {
            $rutaBD = "uploads/postevento_cortos/" . $nuevoNombre;
            $stmtCorto = $conexion->prepare("
                INSERT INTO post_evento_corto (id_post_evento, ruta_corto)
                VALUES (?, ?)
            ");
            $stmtCorto->bind_param("is", $id_post_evento, $rutaBD);
            $stmtCorto->execute();
            $stmtCorto->close();
        }
    }
}

echo json_encode([
    "ok" => true,
    "msg" => "Post‑evento guardado correctamente."
]);

$conexion->close();
