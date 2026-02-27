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