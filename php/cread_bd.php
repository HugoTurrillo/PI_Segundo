<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

try {
    // Conexión inicial sin base de datos
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");

    echo "Base de datos creada correctamente.<br>";

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
       TABLA CANDIDATURA
       ============================ */
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS candidatura (
        id_candidatura INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        id_edicion INT NOT NULL,
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
        motivo_rechazo TEXT NULL,
        fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        fecha_ultima_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA NOTIFICACION_CANDIDATURA
       ============================ */
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

    /* ============================
       TABLA NOTICIA
       ============================ */
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
       TABLA EVENTO_CALENDARIO
       ============================ */
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS evento_calendario (
        id_evento INT AUTO_INCREMENT PRIMARY KEY,
        id_edicion INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        descripcion TEXT,
        fecha_hora_inicio DATETIME NOT NULL,
        fecha_hora_fin DATETIME NOT NULL,
        tipo ENUM('inscripcion','gala','charla','proyeccion','otro') NOT NULL DEFAULT 'otro',
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA GALA
       ============================ */
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS gala (
        id_gala INT AUTO_INCREMENT PRIMARY KEY,
        id_edicion INT NOT NULL,
        localizacion VARCHAR(255) NOT NULL,
        programa TEXT,
        enlace_streaming VARCHAR(255),
        mostrar_streaming TINYINT(1) NOT NULL DEFAULT 0,
        reportaje_texto TEXT,
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA GALA_MEDIA
       ============================ */
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS gala_media (
        id_media INT AUTO_INCREMENT PRIMARY KEY,
        id_gala INT NOT NULL,
        tipo ENUM('foto','video') NOT NULL,
        ruta_archivo VARCHAR(255) NOT NULL,
        FOREIGN KEY (id_gala) REFERENCES gala(id_gala)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA CATEGORIA_PREMIO
       ============================ */
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS categoria_premio (
        id_categoria INT AUTO_INCREMENT PRIMARY KEY,
        nombre_categoria VARCHAR(150) NOT NULL,
        descripcion TEXT
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA PREMIO
       ============================ */
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
       TABLA PATROCINADOR
       ============================ */
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS patrocinador (
        id_patrocinador INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        logo_ruta VARCHAR(255),
        url_web VARCHAR(255)
    ) ENGINE=InnoDB;
    ");

    /* ============================
       TABLA EDICION_PATROCINADOR (N:M)
       ============================ */
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
       TABLA CORTO_EDICION_ANTERIOR
       ============================ */
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS corto_edicion_anterior (
        id_corto_anterior INT AUTO_INCREMENT PRIMARY KEY,
        id_edicion INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        video_ruta VARCHAR(255) NOT NULL,
        descripcion TEXT,
        FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion)
    ) ENGINE=InnoDB;
    ");

    echo "Todas las tablas fueron creadas correctamente.";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}