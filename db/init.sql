-- Creamos la tabla si no existe en la base de datos
CREATE TABLE IF NOT EXISTS visitantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_visitante VARCHAR(100) NOT NULL,
    motivo_visita TEXT NOT NULL,
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    salida_registrada BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS estudiante (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_Visitante INT UNIQUE, 
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    email VARCHAR(100) UNIQUE,
    carrera VARCHAR(100),
    semestre INT,
    foto_perfil VARCHAR(255),
    biografia TEXT,
    habilidades TEXT,
    github_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_Visitante) REFERENCES visitantes(id) ON DELETE CASCADE
);

-- Aquí creamos datosd e prueba así no tenemos la bdd vacía y nos sirve
--  para testear inicialmente
INSERT INTO visitantes (nombre_visitante, motivo_visita) VALUES
('Gabriel Pérez', 'Reunión con profesores'),
('Nicolas Moros', 'Retiro total de matrícula'),
('Anthony Vivenes', 'Mantenimiento de equipos'),
('Lulú Martinez', 'Visita a biblioteca'),
('Pedro Sánchez', 'Entrevista de trabajo');




-- Insert para preubas
INSERT INTO estudiante (id_Visitante, nombre, apellido, fecha_nacimiento, email, carrera, semestre, biografia, habilidades) VALUES
(1, 'Gabriel', 'Pérez', '1998-05-20', 'gabriel.perez@ejemplo.com', 'Educación', 10, 
'Interesado en metodologías de aprendizaje activo y pedagogía moderna.',
'Planificación, Oratoria, Liderazgo');

INSERT INTO estudiante (id_Visitante, nombre, apellido, fecha_nacimiento, email, carrera, semestre, biografia, habilidades) VALUES
(2, 'Nicolas', 'Moros', '2001-11-02', 'nicolas.moros@ejemplo.com', 'Administración', 4, 
'Estudiante enfocado en gestión de procesos y talento humano.',
'Excel avanzado, Contabilidad, Análisis de riesgos');

INSERT INTO estudiante (id_Visitante, nombre, apellido, fecha_nacimiento, email, carrera, semestre, biografia, habilidades) VALUES
(3, 'Anthony', 'Vivenes', '2000-01-15', 'anthony.vivenes@ejemplo.com', 'Ingeniería en Computación', 8, 
'Estudiante de Ingeniería en Computación apasionado por el desarrollo backend y la infraestructura en Docker.',
'Python, Docker, MySQL, PHP, Git');

INSERT INTO estudiante (id_Visitante, nombre, apellido, fecha_nacimiento, email, carrera, semestre, biografia, habilidades) VALUES
(4, 'Lulú', 'Martinez', '2002-03-30', 'lulu.mtz@ejemplo.com', 'Letras', 6, 
'Amante de la literatura clásica y la investigación bibliográfica.',
'Redacción creativa, Ortografía, Archivología');

INSERT INTO estudiante (id_Visitante, nombre, apellido, fecha_nacimiento, email, carrera, semestre, biografia, habilidades) VALUES
(5, 'Pedro', 'Sánchez', '1995-08-12', 'pedro.sanchez@ejemplo.com', 'Psicología Industrial', 9, 
'Especializándose en selección de personal y comportamiento organizacional.',
'Entrevistas, SPSS, Gestión de grupos');

-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@





--         PROCEDIMIENTO ALMACENADO PARA ESTADÍSTICAS
-- llama a este proceso cuando el scri haga un reporte genereal
DELIMITER $$

CREATE PROCEDURE sp_estadisticas_visitantes()
BEGIN

    SELECT COUNT(*) INTO @total FROM visitantes;

    SELECT COUNT(*) INTO @dentro FROM visitantes WHERE salida_registrada = FALSE;
    
    SELECT COUNT(*) INTO @fuera FROM visitantes WHERE salida_registrada = TRUE;
    
    SELECT 
        DATE(fecha_ingreso) as fecha,
        COUNT(*) as cantidad
    FROM visitantes
    WHERE fecha_ingreso >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(fecha_ingreso)
    ORDER BY fecha DESC;
    
    SELECT 
        @total AS total_visitantes,
        @dentro AS actualmente_dentro,
        @fuera AS ya_salieron;
END$$


DELIMITER ;

-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

-- agregaré más procedimiento cuando cumpla las exigencias básicas