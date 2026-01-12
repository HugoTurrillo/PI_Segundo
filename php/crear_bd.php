<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    /* ============================
       CREAR BASE DE DATOS
       ============================ */
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");

    /* ============================
       TABLAS BASE
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
       TABLAS CATALOGO
       ============================ */

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        premios INT NOT NULL,
        premio_fisico TINYINT(1) NOT NULL
    ) ENGINE=InnoDB;
    ");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS categoria_premio (
        id_categoria INT AUTO_INCREMENT PRIMARY KEY,
        nombre_categoria VARCHAR(150) NOT NULL,
        descripcion TEXT
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLAS PRINCIPALES
       ============================ */

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS candidatura (
        id_candidatura INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        id_edicion INT NOT NULL,
        id_categoria INT NULL,
        titulo_obra VARCHAR(255) NOT NULL,
        ficha_tecnico_artistica TEXT,
        cartel_ruta VARCHAR(255),
        sinopsis TEXT,
        nombre_contacto VARCHAR(150) NOT NULL,
        email_contacto VARCHAR(150) NOT NULL,
        dni VARCHAR(20) NOT NULL,
        expediente VARCHAR(50),
        video_ruta VARCHAR(255),
        estado ENUM('en_proceso','aceptada','rechazada') NOT NULL DEFAULT 'en_proceso',
        motivo_rechazo TEXT,
        fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fecha_ultima_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP 
            ON UPDATE CURRENT_TIMESTAMP,

        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion),
        FOREIGN KEY (id_categoria) REFERENCES categorias(id)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLAS DEPENDIENTES
       ============================ */

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS ganadores (
        id_ganador INT AUTO_INCREMENT PRIMARY KEY,
        id_categoria INT NOT NULL,
        numero_premio INT NOT NULL,
        id_candidatura INT NOT NULL,
        FOREIGN KEY (id_categoria) REFERENCES categorias(id),
        FOREIGN KEY (id_candidatura) REFERENCES candidatura(id_candidatura)
    ) ENGINE=InnoDB;
    ");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS notificacion_candidatura (
        id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
        id_candidatura INT NOT NULL,
        tipo ENUM('creacion','actualizacion','cambio_estado') NOT NULL,
        email_destino VARCHAR(150) NOT NULL,
        asunto VARCHAR(255) NOT NULL,
        mensaje TEXT NOT NULL,
        fecha_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_candidatura) REFERENCES candidatura(id_candidatura)
    ) ENGINE=InnoDB;
    ");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS premio (
        id_premio INT AUTO_INCREMENT PRIMARY KEY,
        id_edicion INT NOT NULL,
        id_categoria INT NOT NULL,
        descripcion_premio TEXT,
        id_candidatura_ganadora INT NULL,
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion),
        FOREIGN KEY (id_categoria) REFERENCES categoria_premio(id_categoria),
        FOREIGN KEY (id_candidatura_ganadora) REFERENCES candidatura(id_candidatura)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLAS EVENTOS Y CONTENIDO
       ============================ */

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS evento (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        fecha DATE NOT NULL,
        descripcion TEXT NOT NULL
    ) ENGINE=InnoDB;
    ");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS gala (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        fecha DATE NOT NULL,
        hora TIME NOT NULL,
        lugar VARCHAR(255) NOT NULL,
        descripcion TEXT,
        imagen VARCHAR(255)
    ) ENGINE=InnoDB;
    ");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS noticia (
        id_noticia INT AUTO_INCREMENT PRIMARY KEY,
        id_edicion INT NULL,
        titulo VARCHAR(255) NOT NULL,
        contenido TEXT NOT NULL,
        imagen_ruta VARCHAR(255),
        fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        publicada TINYINT(1) NOT NULL DEFAULT 0,
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLAS PATROCINIO
       ============================ */

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS patrocinador (
        id_patrocinador INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        logo_ruta VARCHAR(255),
        url_web VARCHAR(255)
    ) ENGINE=InnoDB;
    ");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS edicion_patrocinador (
        id_edicion INT NOT NULL,
        id_patrocinador INT NOT NULL,
        PRIMARY KEY (id_edicion, id_patrocinador),
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion),
        FOREIGN KEY (id_patrocinador) REFERENCES patrocinador(id_patrocinador)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       DATOS DE PRUEBA
       ============================ */

    $pdo->exec("
    INSERT INTO edicion_festival (anio, titulo, descripcion, fecha_inicio_inscripcion, fecha_fin_inscripcion, fecha_gala, activa)
    VALUES (2025, 'Festival de Cortos UEM 2025', 'Edición actual del Festival', '2025-01-01', '2025-05-31', '2025-06-15', 1)
    ON DUPLICATE KEY UPDATE titulo = VALUES(titulo);
    ");

    $pdo->prepare("
        INSERT IGNORE INTO usuario (nombre_completo, email, password_hash, rol)
        VALUES ('Organizador Principal', 'organizador@uem.es', ?, 'organizador')
    ")->execute([password_hash("organizador123", PASSWORD_DEFAULT)]);

    $pdo->prepare("
        INSERT IGNORE INTO usuario (nombre_completo, email, password_hash, rol)
        VALUES ('Participante Demo', 'participante@uem.es', ?, 'participante')
    ")->execute([password_hash("participante123", PASSWORD_DEFAULT)]);

    echo "Base de datos creada correctamente.";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
