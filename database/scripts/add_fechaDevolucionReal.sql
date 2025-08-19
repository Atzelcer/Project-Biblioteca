-- Script para agregar la columna fechaDevolucionReal a la tabla reservas
-- Ejecutar este script en la base de datos biblioteca_bd

ALTER TABLE reservas 
ADD COLUMN fechaDevolucionReal DATETIME DEFAULT NULL COMMENT 'Fecha real cuando se devolvió el libro';

-- Verificar que se agregó correctamente
DESCRIBE reservas;
