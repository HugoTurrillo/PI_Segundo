<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "festival_cortos_uem";

$conexion = new mysqli($host, $user, $pass);

if ($conexion->connect_error) {
    die("Error de conexión: " . $e->getMessage());
}

$comprobar_db = "SHOW DATABASES LIKE '$dbname'";
$resultado = $conexion->query($comprobar_db);

if ($resultado->num_rows <=0){
    //Cifrado de contraseñas para admin y participante
    $password_organizador = password_hash("organizador123", PASSWORD_DEFAULT);
    $password_participante = password_hash("participante123", PASSWORD_DEFAULT);


    $sql = "
    -- Crear la database
    CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    
    USE $dbname;
    
    -- Crear la tabla usuario
    CREATE TABLE IF NOT EXISTS usuario (
        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
        nombre_completo VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        rol ENUM('participante','organizador') NOT NULL DEFAULT 'participante',
        fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        activo TINYINT(1) NOT NULL DEFAULT 1
    ) ENGINE=InnoDB;
    
    -- Crear tabla edición
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

    -- Crear tabla categorias
    CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        premios INT NOT NULL,
        premio_fisico TINYINT(1) NOT NULL
    ) ENGINE=InnoDB;

    -- Crear tabla candidaturas
    CREATE TABLE IF NOT EXISTS candidatura (
    id_candidatura INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_edicion INT NOT NULL,
    id_categoria INT NULL,
    titulo_obra VARCHAR(255) NOT NULL,
    sinopsis TEXT,
    nombre_contacto VARCHAR(150) NOT NULL,
    email_contacto VARCHAR(150) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    estado ENUM('en_proceso','aceptada','rechazada') NOT NULL DEFAULT 'en_proceso',
    motivo_rechazo TEXT,
    mensaje_subsanacion TEXT,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_edicion) REFERENCES edicion_festival(id_edicion),
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
) ENGINE=InnoDB;


    -- Crear tabla evento
    CREATE TABLE IF NOT EXISTS evento (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        fecha DATE NOT NULL,
        descripcion TEXT NOT NULL
    ) ENGINE=InnoDB;

    -- Crear tabla gala
    CREATE TABLE IF NOT EXISTS gala (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        fecha DATE NOT NULL,
        hora TIME NOT NULL,
        lugar VARCHAR(255) NOT NULL,
        descripcion TEXT,
        imagen VARCHAR(255)
    ) ENGINE=InnoDB;

    -- Crear tabla noticia
    CREATE TABLE IF NOT EXISTS noticia (
        id_noticia INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        contenido TEXT NOT NULL,
        fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    -- Crear tabla patrocinador
    CREATE TABLE IF NOT EXISTS patrocinador (
        id_patrocinador INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        logo_ruta VARCHAR(255),
        url_web VARCHAR(255)
    ) ENGINE=InnoDB;


    -- Inserción de datos de prueba
    INSERT INTO edicion_festival (anio, titulo, descripcion, fecha_inicio_inscripcion, fecha_fin_inscripcion, fecha_gala, activa)
        VALUES (2025, 'Festival de Cortos UEM 2025', 'Edición actual del Festival', '2025-01-01', '2025-05-31', '2025-06-15', 1)
        ON DUPLICATE KEY UPDATE titulo = VALUES(titulo);


    INSERT INTO usuario (nombre_completo, email, password_hash, rol)
        VALUES ('Organizador Principal', 'organizador@uem.es', '$password_organizador', 'organizador');

    INSERT INTO usuario (nombre_completo, email, password_hash, rol)
        VALUES ('Participante Demo', 'participante@uem.es', '$password_participante', 'participante');

    INSERT INTO categorias (id, nombre, premios, premio_fisico) VALUES
        (1, 'Alumnos', 3, 1),
        (2, 'Alumni', 2, 0),
        (3, 'Profesionales', 1, 0)
    ;

    INSERT INTO evento (id, titulo, fecha, descripcion) VALUES
        (1, 'Masterclass de dirección', '2025-05-10', 'Sesión con directores invitados.'),
        (2, 'Taller de montaje', '2025-05-15', 'Workshop práctico de edición de vídeo.')
    ;

    INSERT INTO gala (id, titulo, fecha, hora, lugar, descripcion, imagen) VALUES
        (1, 'Gala de inauguración', '2025-06-15', '19:00:00', 'Auditorio principal', 'Apertura del festival.', 'gala.jpg')
    ;

    INSERT INTO noticia (id_noticia, titulo, contenido) VALUES
        (1, 'Arranca el Festival de Cortos UEM', 'Ya están abiertas las inscripciones.'),
        (2, 'Publicados los finalistas', 'Consulta los cortos finalistas en la web.')
    ;

    ";

    if ($conexion->multi_query($sql)) {
            while ($conexion->next_result()) {;}
            echo "Tabla creada y actualizada correctamente.";
        } else {
            echo "Error: {$conexion->error}";
        }
}

//echo "Base de datos creada correctamente.";
$conexion->close();
