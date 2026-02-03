<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

$conexion = new mysqli($host, $user, $pass);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

// ¿Existe la base?
$check = $conexion->query("SHOW DATABASES LIKE '$dbname'");

if ($check->num_rows == 0) {

    // Crear base
    $conexion->query("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conexion->select_db($dbname);

    // Crear tablas
    $conexion->query("
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

    $conexion->query("
        CREATE TABLE IF NOT EXISTS edicion_festival (
            id_edicion INT AUTO_INCREMENT PRIMARY KEY,
            anio INT NOT NULL UNIQUE,
            titulo VARCHAR(255) NOT NULL,
            descripcion TEXT,
            fecha_inicio_inscripcion DATE,
            fecha_fin_inscripcion DATE,
            fecha_gala DATE,
            activa TINYINT(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB;
    ");

    $conexion->query("
        CREATE TABLE IF NOT EXISTS categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            premios INT NOT NULL,
            premio_fisico TINYINT(1) NOT NULL
        ) ENGINE=InnoDB;
    ");

    $conexion->query("
        CREATE TABLE IF NOT EXISTS candidatura (
    id_candidatura INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_edicion INT NOT NULL,
    id_categoria INT NULL,
    titulo_obra VARCHAR(255) NOT NULL,
    sinopsis TEXT NOT NULL,
    nombre_contacto VARCHAR(150) NOT NULL,
    email_contacto VARCHAR(150) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    video_ruta VARCHAR(255) NOT NULL,
    portada_ruta VARCHAR(255) NOT NULL,
    estado ENUM('en_proceso','aceptada','rechazada') NOT NULL DEFAULT 'en_proceso',
    motivo_rechazo TEXT,
    mensaje_subsanacion TEXT,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion),
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
    ) ENGINE=InnoDB;
    ");

    $conexion->query("
        CREATE TABLE IF NOT EXISTS ganadores (
            id_ganador INT AUTO_INCREMENT PRIMARY KEY,
            id_categoria INT NOT NULL,
            numero_premio INT NOT NULL,
            id_candidatura INT NOT NULL,
            FOREIGN KEY (id_categoria) REFERENCES categorias(id),
            FOREIGN KEY (id_candidatura) REFERENCES candidatura(id_candidatura)
        ) ENGINE=InnoDB;
    ");

    $conexion->query("
        CREATE TABLE IF NOT EXISTS evento (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            descripcion TEXT NOT NULL
        ) ENGINE=InnoDB;
    ");

    $conexion->query("
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

    $conexion->query("
        CREATE TABLE IF NOT EXISTS noticia (
            id_noticia INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            contenido TEXT NOT NULL,
            fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            imagen_ruta VARCHAR(255) NULL
        ) ENGINE=InnoDB;
    ");

    $conexion->query("
        CREATE TABLE IF NOT EXISTS patrocinador (
            id_patrocinador INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            logo_ruta VARCHAR(255),
            url_web VARCHAR(255),
            descripcion VARCHAR(255)
        ) ENGINE=InnoDB;
    ");

    /* TABLAS NUEVAS PARA EL POST‑EVENTO */

    $conexion->query("
        CREATE TABLE IF NOT EXISTS post_evento (
            id_post_evento INT AUTO_INCREMENT PRIMARY KEY,
            id_edicion INT NOT NULL,
            resumen TEXT,
            ganador_alumnos VARCHAR(255),
            corto_alumnos VARCHAR(255),
            ganador_alumni VARCHAR(255),
            corto_alumni VARCHAR(255),
            ganador_profesional VARCHAR(255),
            corto_profesional VARCHAR(255),
            anio_edicion INT,
            numero_participantes INT,
            ganadores_json TEXT,
            fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion)
        ) ENGINE=InnoDB;
    ");

    $conexion->query("
        CREATE TABLE IF NOT EXISTS post_evento_imagen (
            id_imagen INT AUTO_INCREMENT PRIMARY KEY,
            id_post_evento INT NOT NULL,
            ruta_imagen VARCHAR(255) NOT NULL,
            FOREIGN KEY (id_post_evento) REFERENCES post_evento(id_post_evento)
        ) ENGINE=InnoDB;
    ");

    $conexion->query("
        CREATE TABLE IF NOT EXISTS post_evento_corto (
            id_corto INT AUTO_INCREMENT PRIMARY KEY,
            id_post_evento INT NOT NULL,
            ruta_corto VARCHAR(255) NOT NULL,
            FOREIGN KEY (id_post_evento) REFERENCES post_evento(id_post_evento)
        ) ENGINE=InnoDB;
    ");

    /* DATOS INICIALES */

    $password_organizador = password_hash("organizador123", PASSWORD_DEFAULT);
    $password_participante = password_hash("participante123", PASSWORD_DEFAULT);

    $conexion->query("
        INSERT INTO usuario (nombre_completo, email, password_hash, rol)
        VALUES 
        ('Organizador Principal', 'organizador@uem.es', '$password_organizador', 'organizador'),
        ('Participante Demo', 'participante@uem.es', '$password_participante', 'participante');
    ");

    $conexion->query("
        INSERT INTO edicion_festival (anio, titulo, descripcion, fecha_inicio_inscripcion, fecha_fin_inscripcion, fecha_gala, activa)
        VALUES (2025, 'Festival de Cortos UEM 2025', 'Edición actual del Festival', '2025-01-01', '2025-05-31', '2025-06-15', 1);
    ");

    $conexion->query("
        INSERT INTO categorias (nombre, premios, premio_fisico)
        VALUES 
        ('Alumnos', 3, 1),
        ('Alumni', 3, 0),
        ('Profesionales', 3, 0);
    ");

    $conexion->query("
        INSERT INTO evento (titulo, fecha, descripcion)
        VALUES 
        ('Masterclass de dirección', '2025-05-10', 'Sesión con directores invitados.'),
        ('Taller de montaje', '2025-05-15', 'Workshop práctico de edición de vídeo.');
    ");

    $conexion->query("
        INSERT INTO gala (titulo, fecha, hora, lugar, descripcion, imagen)
        VALUES 
        ('Gala de inauguración', '2025-06-15', '19:00:00', 'Auditorio principal', 'Apertura del festival.', 'gala.jpg');
    ");

    $conexion->query("
        INSERT INTO noticia (titulo, contenido)
        VALUES 
        ('Arranca el Festival de Cortos UEM', 'Ya están abiertas las inscripciones.'),
        ('Publicados los finalistas', 'Consulta los cortos finalistas en la web.');
    ");
}

$conexion->close();
