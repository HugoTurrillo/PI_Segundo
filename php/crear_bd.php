<?php
// php/crear_bd.php
// Crea la base de datos y las tablas necesarias para el proyecto
// y añade algunos datos de prueba.

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

try {
    // Conexión inicial sin base de datos
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Crear base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    /* ============================
       TABLA USUARIO
       ============================ */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuario (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            nombre_completo VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            rol ENUM('participante','organizador') NOT NULL DEFAULT 'participante',
            fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            activo TINYINT(1) NOT NULL DEFAULT 1
        ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA EDICION_FESTIVAL
       (por si el enunciado la exige)
       ============================ */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS edicion_festival (
            id_edicion INT AUTO_INCREMENT PRIMARY KEY,
            anio INT NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            descripcion TEXT,
            fecha_inicio_inscripcion DATE,
            fecha_fin_inscripcion DATE,
            fecha_gala DATE,
            activa TINYINT(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA NOTICIA
       (coincide con tus PHP y JS)
       ============================ */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS noticia (
            id_noticia INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            contenido TEXT NOT NULL,
            fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA EVENTO
       (coincide con evento-*.php y eventos.js)
       ============================ */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS evento (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            fecha DATE NOT NULL,
            descripcion TEXT NOT NULL
        ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA GALA
       (coincide con gala-*.php y gala.js)
       ============================ */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS gala (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            lugar VARCHAR(255) NOT NULL,
            descripcion TEXT,
            imagen VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA CATEGORIAS
       (coincide con premios.js y categoria-*.php)
       ============================ */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            premios VARCHAR(255) NOT NULL,
            premio_fisico VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA PATROCINADORES
       (coincide con patrocinadores.js y patrocinador-*.php)
       ============================ */
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS patrocinadores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            logo VARCHAR(255),
            enlace VARCHAR(255),
            descripcion TEXT
        ) ENGINE=InnoDB;
    ");
    /* ============================
   TABLA CANDIDATURA
   ============================ */
$pdo->exec("
    CREATE TABLE IF NOT EXISTS candidatura (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        sinopsis TEXT,
        cartel VARCHAR(255),
        video VARCHAR(255),
        fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
    ) ENGINE=InnoDB;
");


    /* ============================
       DATOS DE PRUEBA
       ============================ */

    // EDICIÓN ACTIVA (si no existe)
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM edicion_festival");
    $totalEd = (int)$stmt->fetch(PDO::FETCH_ASSOC)["total"];

    if ($totalEd === 0) {
        $pdo->exec("
            INSERT INTO edicion_festival (anio, titulo, descripcion, fecha_inicio_inscripcion, fecha_fin_inscripcion, fecha_gala, activa)
            VALUES
            (2025, 'Festival de Cortos UEM 2025', 'Edición actual del festival', '2025-01-01', '2025-05-31', '2025-06-15', 1)
        ");
    }

    // USUARIOS DE PRUEBA (organizador + participante)
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM usuario");
    $totalUsers = (int)$stmt->fetch(PDO::FETCH_ASSOC)["total"];

    if ($totalUsers === 0) {
        $passwordOrganizador = password_hash("organizador123", PASSWORD_DEFAULT);
        $passwordParticipante = password_hash("participante123", PASSWORD_DEFAULT);

        $ins = $pdo->prepare("
            INSERT INTO usuario (nombre_completo, email, password_hash, rol)
            VALUES (?, ?, ?, ?)
        ");

        $ins->execute(["Organizador Principal", "organizador@uem.es", $passwordOrganizador, "organizador"]);
        $ins->execute(["Participante Demo", "participante@uem.es", $passwordParticipante, "participante"]);
    }

    // NOTICIAS DE PRUEBA
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM noticia");
    if ((int)$stmt->fetch(PDO::FETCH_ASSOC)["total"] === 0) {
        $pdo->exec("
            INSERT INTO noticia (titulo, contenido) VALUES
            ('Arranca el Festival de Cortos UEM', 'Ya están abiertas las inscripciones para la edición 2025.'),
            ('Finalistas anunciados', 'Publicamos la lista de cortos finalistas de esta edición.')
        ");
    }

    // EVENTOS DE PRUEBA
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM evento");
    if ((int)$stmt->fetch(PDO::FETCH_ASSOC)["total"] === 0) {
        $pdo->exec("
            INSERT INTO evento (titulo, fecha, descripcion) VALUES
            ('Masterclass de dirección', '2025-05-10', 'Sesión con directores invitados.'),
            ('Taller de montaje', '2025-05-15', 'Workshop práctico de edición de vídeo.')
        ");
    }

    // GALA DE PRUEBA
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM gala");
    if ((int)$stmt->fetch(PDO::FETCH_ASSOC)["total"] === 0) {
        $pdo->exec("
            INSERT INTO gala (titulo, fecha, hora, lugar, descripcion, imagen) VALUES
            ('Gala de Inauguración', '2025-05-12', '19:00:00', 'Auditorio Principal', 'Acto de apertura del festival.', 'gala_demo.jpg')
        ");
    }

    // CATEGORÍAS DE PRUEBA
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM categorias");
    if ((int)$stmt->fetch(PDO::FETCH_ASSOC)["total"] === 0) {
        $pdo->exec("
            INSERT INTO categorias (nombre, premios, premio_fisico) VALUES
            ('Alumnos', '1º, 2º y 3º premio', 'Cámara Canon para el 1º'),
            ('Alumni', '1º y 2º premio', 'Sin premio físico'),
            ('Carrera Profesional', '1 único premio', 'Sin premio físico')
        ");
    }

    // PATROCINADORES DE PRUEBA
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM patrocinadores");
    if ((int)$stmt->fetch(PDO::FETCH_ASSOC)["total"] === 0) {
        $pdo->exec("
            INSERT INTO patrocinadores (nombre, logo, enlace, descripcion) VALUES
            ('Canon', 'canon_logo.png', 'https://www.canon.es', 'Patrocinador principal del festival.'),
            ('Adobe', 'adobe_logo.png', 'https://www.adobe.com', 'Herramientas de edición para los participantes.')
        ");
    }

    echo "Base de datos y tablas creadas correctamente con datos de prueba.";

} catch (PDOException $e) {
    die("Error al crear la base de datos: " . $e->getMessage());
}
