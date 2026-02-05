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

/* ===============================
   CREAR BASE DE DATOS SI NO EXISTE
================================ */

$check = $conexion->query("SHOW DATABASES LIKE '$dbname'");

if ($check->num_rows === 0) {

    $conexion->query("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conexion->select_db($dbname);

    /* ===============================
       CREACIÓN DE TABLAS
    ================================ */

    $conexion->query("
        CREATE TABLE usuario (
                id_usuario INT AUTO_INCREMENT PRIMARY KEY,
                nombre_completo VARCHAR(150) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                rol ENUM('participante','organizador') NOT NULL,
                rol_participante ENUM('alumno','alumni','profesional') DEFAULT NULL,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                activo TINYINT(1) DEFAULT 1
);
    ");

    $conexion->query("
        CREATE TABLE edicion_festival (
            id_edicion INT AUTO_INCREMENT PRIMARY KEY,
            anio INT UNIQUE NOT NULL,
            titulo VARCHAR(255) NOT NULL,
            descripcion TEXT,
            fecha_inicio_inscripcion DATE,
            fecha_fin_inscripcion DATE,
            fecha_gala DATE,
            activa TINYINT(1) DEFAULT 0
        );
    ");

    $conexion->query("
        CREATE TABLE categorias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            premios INT NOT NULL,
            premio_fisico TINYINT(1) NOT NULL
        );
    ");

    $conexion->query("
        CREATE TABLE candidatura (
            id_candidatura INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            id_edicion INT NOT NULL,
            id_categoria INT,
            titulo_obra VARCHAR(255) NOT NULL,
            sinopsis TEXT NOT NULL,
            nombre_contacto VARCHAR(150) NOT NULL,
            email_contacto VARCHAR(150) NOT NULL,
            dni VARCHAR(20) NOT NULL,
            video_ruta VARCHAR(255) NOT NULL,
            portada_ruta VARCHAR(255) NOT NULL,
            estado ENUM('en_proceso','aceptada','rechazada') DEFAULT 'en_proceso',
            motivo_rechazo TEXT,
            mensaje_subsanacion TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
            FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion),
            FOREIGN KEY (id_categoria) REFERENCES categorias(id)
        );
    ");

    $conexion->query("
        CREATE TABLE ganadores (
            id_ganador INT AUTO_INCREMENT PRIMARY KEY,
            id_categoria INT NOT NULL,
            numero_premio INT NOT NULL,
            id_candidatura INT NOT NULL,
            FOREIGN KEY (id_categoria) REFERENCES categorias(id),
            FOREIGN KEY (id_candidatura) REFERENCES candidatura(id_candidatura)
        );
    ");

    $conexion->query("
        CREATE TABLE evento (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            descripcion TEXT NOT NULL
        );
    ");

    $conexion->query("
        CREATE TABLE gala (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            lugar VARCHAR(255) NOT NULL,
            descripcion TEXT,
            imagen VARCHAR(255)
        );
    ");

    $conexion->query("
        CREATE TABLE noticia (
            id_noticia INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            contenido TEXT NOT NULL,
            fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            imagen_ruta VARCHAR(255)
        );
    ");

    $conexion->query("
        CREATE TABLE patrocinador (
            id_patrocinador INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            logo_ruta VARCHAR(255),
            url_web VARCHAR(255),
            descripcion VARCHAR(255)
        );
    ");

    $conexion->query("
        CREATE TABLE post_evento (
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
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion)
        );
    ");

    $conexion->query("
        CREATE TABLE post_evento_imagen (
            id_imagen INT AUTO_INCREMENT PRIMARY KEY,
            id_post_evento INT NOT NULL,
            ruta_imagen VARCHAR(255) NOT NULL,
            FOREIGN KEY (id_post_evento) REFERENCES post_evento(id_post_evento)
        );
    ");

    $conexion->query("
        CREATE TABLE post_evento_corto (
            id_corto INT AUTO_INCREMENT PRIMARY KEY,
            id_post_evento INT NOT NULL,
            ruta_corto VARCHAR(255) NOT NULL,
            FOREIGN KEY (id_post_evento) REFERENCES post_evento(id_post_evento)
        );
    ");

    /* ===============================
       DATOS DE PRUEBA
================================ */

    $passOrg = password_hash("organizador123", PASSWORD_DEFAULT);
    $passPar = password_hash("participante123", PASSWORD_DEFAULT);

    /* ===============================
   1. USUARIOS (IDs 1–12)
================================ */

    $conexion->query("
    INSERT INTO usuario (nombre_completo,email,password_hash,rol) VALUES
    ('Organizador Principal','organizador@uem.es','$passOrg','organizador'),
    ('Participante Principal','participante@uem.es','$passPar','participante'),
    ('Usuario 3','u3@correo.es','$passPar','participante'),
    ('Usuario 4','u4@correo.es','$passPar','participante'),
    ('Usuario 5','u5@correo.es','$passPar','participante'),
    ('Usuario 6','u6@correo.es','$passPar','participante'),
    ('Usuario 7','u7@correo.es','$passPar','participante'),
    ('Usuario 8','u8@correo.es','$passPar','participante'),
    ('Usuario 9','u9@correo.es','$passPar','participante'),
    ('Usuario 10','u10@correo.es','$passPar','participante'),
    ('Usuario 11','u11@correo.es','$passPar','participante'),
    ('Usuario 12','u12@correo.es','$passPar','participante');
");

    /* ===============================
   2. EDICIÓN
================================ */

    $conexion->query("
    INSERT INTO edicion_festival (anio,titulo,descripcion,fecha_inicio_inscripcion,fecha_fin_inscripcion,fecha_gala,activa)
    VALUES (2025,'Festival Cortos UEM 2025','Edición oficial 2025','2025-01-01','2025-05-31','2025-06-15',1);
");

    /* ===============================
   3. CATEGORÍAS (IDs 1–3)
================================ */

    $conexion->query("
    INSERT INTO categorias (nombre,premios,premio_fisico) VALUES
    ('Alumnos',3,1),
    ('Alumni',3,0),
    ('Profesionales',3,0);
");

    /* ===============================
   4. CANDIDATURAS (11 entradas)
================================ */

    $conexion->query("
    INSERT INTO candidatura
    (id_usuario,id_edicion,id_categoria,titulo_obra,sinopsis,nombre_contacto,email_contacto,dni,video_ruta,portada_ruta,estado,motivo_rechazo,mensaje_subsanacion)
    VALUES
    (3,1,2,'Horizonte Perdido','Documental social','Carlos López','carlos@correo.es','22222222B','/videos/horizonte.mp4','/portadas/horizonte.jpg','aceptada',NULL,NULL),
    (4,1,3,'Latidos Rotos','Experimental visual','Lucía Martín','lucia@correo.es','33333333C','/videos/latidos_rotos.mp4','/portadas/latidos_rotos.jpg','rechazada','No cumple requisitos técnicos',NULL),
    (10,1,3,'Ciclos','Experimental conceptual','María Sánchez','maria@correo.es','99999999J','/videos/ciclos.mp4','/portadas/ciclos.jpg','en_proceso',NULL,NULL),
    (11,1,1,'Niebla','Drama introspectivo','Pablo Herrera','pablo@correo.es','10101010K','/videos/niebla.mp4','/portadas/niebla.jpg','rechazada','Iluminación insuficiente',NULL),
    (12,1,2,'Senderos','Documental de viaje','Laura Díaz','laura@correo.es','12121212L','/videos/senderos.mp4','/portadas/senderos.jpg','aceptada',NULL,NULL);
");

    /* ===============================
   5. GANADORES 
================================ */

    $conexion->query("
    INSERT INTO ganadores (id_categoria,numero_premio,id_candidatura) VALUES
    (1,1,1),
    (2,1,2);
");

    /* ===============================
   6. EVENTOS
================================ */

    $conexion->query("
    INSERT INTO evento (titulo,fecha,hora,descripcion) VALUES
    ('Masterclass Dirección','2025-05-10','10:00:00','Clase magistral'),
    ('Taller Montaje','2025-05-15','16:00:00','Edición profesional');
");

    /* ===============================
   7. GALA
================================ */

    $conexion->query("
    INSERT INTO gala (titulo,fecha,hora,lugar,descripcion,imagen) VALUES
    ('Gala Final','2025-06-15','19:00:00','Auditorio','Entrega de premios','gala.jpg');
");

    /* ===============================
   8. NOTICIAS
================================ */

    $conexion->query("
    INSERT INTO noticia (titulo,contenido,imagen_ruta) VALUES
    ('Festival en marcha','Inscripciones abiertas','grabacion.jpg'),
    ('Ganadores anunciados','Lista oficial','festival.jpeg');
");

    /* ===============================
   9. PATROCINADORES
================================ */

    $conexion->query("
    INSERT INTO patrocinador (nombre,logo_ruta,url_web,descripcion) VALUES
    ('Nike','nike.jpg','https://nike.com','Marca Deportiva'),
    ('Canon','canon.png','https://canon.es','Equipamiento audiovisual');
");

    /* ===============================
   10. POST-EVENTO
================================ */

    $conexion->query("
    INSERT INTO post_evento
    (id_edicion,resumen,ganador_alumnos,corto_alumnos,ganador_alumni,corto_alumni,ganador_profesional,corto_profesional,anio_edicion,numero_participantes,ganadores_json)
    VALUES
    (1,'Gran éxito del festival','Ana García','Sombras','Carlos López','Horizonte','Ana García','Latidos',2025,120,'{\"alumnos\":\"Sombras\",\"alumni\":\"Horizonte\"}');
");

    /* ===============================
   11. POST-EVENTO IMÁGENES
================================ */

    $conexion->query("
    INSERT INTO post_evento_imagen (id_post_evento,ruta_imagen) VALUES
    (1,'post/img1.jpg'),
    (1,'post/img2.jpg');
");

    /* ===============================
   12. POST-EVENTO CORTOS
================================ */

    $conexion->query("
    INSERT INTO post_evento_corto (id_post_evento,ruta_corto) VALUES
    (1,'post/corto1.mp4'),
    (1,'post/corto2.mp4');
");
}

$conexion->close();
