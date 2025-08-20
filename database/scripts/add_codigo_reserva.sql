-- Script para agregar la columna codigo_reserva a la tabla reservas
-- Ejecutar este script si la columna no existe

ALTER TABLE reservas ADD COLUMN codigo_reserva VARCHAR(50) UNIQUE AFTER estado;

-- Actualizar las reservas existentes con códigos únicos
UPDATE reservas 
SET codigo_reserva = CONCAT('RES-', DATE_FORMAT(fecha_reserva, '%Y%m%d'), '-', LPAD(id_libro, 4, '0'), '-', LPAD(id_usuario, 4, '0'))
WHERE codigo_reserva IS NULL;
