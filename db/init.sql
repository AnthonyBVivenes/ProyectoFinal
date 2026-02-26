-- Creamos la tabla si no existe en la base de datos
CREATE TABLE IF NOT EXISTS visitantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_visitante VARCHAR(100) NOT NULL,
    motivo_visita TEXT NOT NULL,
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    salida_registrada BOOLEAN DEFAULT FALSE
);

-- Aquí creamos datosd e prueba así no tenemos la bdd vacía y nos sirve
--  para testear inicialmente


INSERT INTO visitantes (nombre_visitante, motivo_visita) VALUES
('Gabriel Pérez', 'Reunión con profesores'),
('Nicolas Moros', 'Retiro total de matrícula'),
('Anthony Vivenes', 'Mantenimiento de equipos'),
('Lulú Martinez', 'Visita a biblioteca'),
('Pedro Sánchez', 'Entrevista de trabajo');