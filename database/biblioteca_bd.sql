-- =========================================================
-- Base de datos: biblioteca_bd  (script corregido)
-- Probado en MariaDB 10.4 / MySQL 8.x
-- =========================================================

-- Seguridad: evitar conflictos de FK al recrear
SET FOREIGN_KEY_CHECKS = 0;

-- Crea la BD si no existe y selecciónala
CREATE DATABASE IF NOT EXISTS biblioteca_bd
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE biblioteca_bd;

-- Borrar tablas en orden de dependencias
DROP TABLE IF EXISTS reservas;
DROP TABLE IF EXISTS libros;
DROP TABLE IF EXISTS usuario;

-- ========================
-- Tabla: usuario
-- ========================
CREATE TABLE usuario (
  id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fotografia VARCHAR(100) DEFAULT NULL,
  CI INT(11) NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  apellido VARCHAR(50) NOT NULL,
  rol ENUM('lector','bibliotecario','bloqueado') NOT NULL DEFAULT 'lector',
  celular INT(11) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  direccion VARCHAR(100) DEFAULT NULL,
  correo VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario_ci (CI),
  UNIQUE KEY uq_usuario_correo (correo)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de usuario
-- NOTA: id_usuario se autogenera
-- CONTRASEÑAS: Bibliotecario=12345, Lectores=su CI
INSERT INTO usuario (fotografia, CI, nombre, apellido, rol, celular, password, direccion, correo) VALUES
(NULL, 12345, 'Maria Elena', 'Antezana', 'bibliotecario', 70123456, '8cb2237d0679ca88db6464eac60da96345513964', 'Av. Principal 123', 'bibliotecario@biblioteca.edu'),
(NULL, 11111, 'Juan Carlos', 'Perez', 'lector', 70234567, '7b21848ac9af35be0ddb2d6b9fc3851934db8420', 'Calle Los Pinos 456', 'juan.perez@gmail.com'),
(NULL, 22222, 'Ana Maria', 'Rodriguez', 'lector', 70345678, '1a9b9508b6003b68ddfe03a9c8cbc4bd4388339b', 'Av. Libertadores 789', 'ana.rodriguez@gmail.com'),
(NULL, 33333, 'Carlos Alberto', 'Lopez', 'lector', 70456789, '403d9917c3e950798601addf7ba82cd3c83f344b', 'Calle San Martin 321', 'carlos.lopez@gmail.com'),
(NULL, 44444, 'Sofia', 'Gonzalez', 'lector', 70567890, '3e511da7577d1864871b760ab30e05b56943c9b2', 'Av. America 654', 'sofia.gonzalez@gmail.com'),
(NULL, 55555, 'Diego Fernando', 'Martinez', 'lector', 70678901, '69df79bef9287d3bcb8f104a408b06de6a108fd8', 'Calle Bolivar 987', 'diego.martinez@gmail.com'),
(NULL, 67890, 'Laura Patricia', 'Vargas', 'lector', 70789012, '230991abcd77e8173edb0af392e1f11120051e29', 'Calle Murillo 147', 'laura.vargas@gmail.com'),
(NULL, 13579, 'Roberto', 'Silva', 'lector', 70890123, '93ba1608fc10b710894fb9f8c89724c6eeb44d11', 'Av. 6 de Agosto 258', 'roberto.silva@gmail.com'),
(NULL, 24680, 'Carmen', 'Torres', 'lector', 70901234, '23fbf23c9201b754adcb2fca0c8e4b19b8a7c740', 'Calle Comercio 369', 'carmen.torres@gmail.com');

-- ========================
-- Tabla: libros
-- ========================
CREATE TABLE libros (
  id_libro INT NOT NULL AUTO_INCREMENT,
  codigo_libro VARCHAR(20) NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  autor VARCHAR(50) NOT NULL,
  estado ENUM('Disponible','Reservado','No disponible') NOT NULL DEFAULT 'Disponible',
  foto_libro VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (id_libro),
  UNIQUE KEY uq_libros_codigo (codigo_libro)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO libros (codigo_libro, nombre, autor, estado, foto_libro) VALUES
('PROG001', 'Estructura de Datos con C', 'Luis Joyanes Aguilar', 'Disponible', NULL),
('PROG002', 'Fundamentos de Programacion', 'Kenneth A. Lambert', 'Disponible', NULL),
('MATH001', 'Algebra Lineal y sus Aplicaciones', 'David C. Lay', 'Reservado', NULL),
('MATH002', 'Calculo Infinitesimal', 'Michael Spivak', 'No disponible', NULL),
('PROG003', 'Clean Code', 'Robert C. Martin', 'Disponible', NULL),
('MATH003', 'Introduccion a la Teoria de Numeros', 'G.H. Hardy', 'Reservado', NULL),
('PROG004', 'Eloquent JavaScript', 'Marijn Haverbeke', 'Disponible', NULL),
('PROG005', 'Python para Todos', 'Charles Severance', 'Reservado', NULL),
('MATH004', 'Analisis Matematico', 'Tom M. Apostol', 'Disponible', NULL),
('PROG006', 'The Pragmatic Programmer', 'Andrew Hunt, David T. Thomas', 'No disponible', NULL),
('PROG007', 'Java The Complete Reference', 'Herbert Schildt', 'Disponible', NULL),
('PROG008', 'Learning Python', 'Mark Lutz', 'Disponible', NULL),
('PROG009', 'Design Patterns', 'Gang of Four', 'Disponible', NULL),
('PROG010', 'Introduction to Algorithms', 'Thomas H. Cormen', 'Reservado', NULL),
('PROG011', 'Code Complete', 'Steve McConnell', 'Disponible', NULL),
('PROG012', 'You Dont Know JS', 'Kyle Simpson', 'Disponible', NULL),
('PROG013', 'Effective Java', 'Joshua Bloch', 'No disponible', NULL),
('PROG014', 'Head First Design Patterns', 'Eric Freeman', 'Disponible', NULL),
('PROG015', 'Refactoring', 'Martin Fowler', 'Disponible', NULL),
('PROG016', 'Clean Architecture', 'Robert C. Martin', 'Reservado', NULL),
('PROG017', 'Cracking the Coding Interview', 'Gayle McDowell', 'Disponible', NULL),
('PROG018', 'JavaScript The Good Parts', 'Douglas Crockford', 'Disponible', NULL),
('PROG019', 'Python Crash Course', 'Eric Matthes', 'Disponible', NULL),
('PROG020', 'Automate the Boring Stuff', 'Al Sweigart', 'No disponible', NULL),
('MATH005', 'Calculo de Variable Compleja', 'James Ward Brown', 'Disponible', NULL),
('MATH006', 'Algebra Abstracta', 'David S. Dummit', 'Disponible', NULL),
('MATH007', 'Ecuaciones Diferenciales', 'Dennis G. Zill', 'Reservado', NULL),
('MATH008', 'Probabilidad y Estadistica', 'Ronald E. Walpole', 'Disponible', NULL),
('MATH009', 'Geometria Analitica', 'Charles H. Lehmann', 'Disponible', NULL),
('MATH010', 'Topologia General', 'John L. Kelley', 'No disponible', NULL),
('MATH011', 'Analisis Real', 'Walter Rudin', 'Disponible', NULL),
('MATH012', 'Algebra Lineal Numerica', 'Lloyd N. Trefethen', 'Reservado', NULL),
('PHYS001', 'Fisica Universitaria', 'Hugh D. Young', 'Disponible', NULL),
('PHYS002', 'Mecanica Clasica', 'Herbert Goldstein', 'Disponible', NULL),
('PHYS003', 'Electromagnetismo', 'David J. Griffiths', 'No disponible', NULL),
('PHYS004', 'Termodinamica', 'Mark W. Zemansky', 'Disponible', NULL),
('PHYS005', 'Optica', 'Eugene Hecht', 'Reservado', NULL),
('PHYS006', 'Fisica Moderna', 'Kenneth S. Krane', 'Disponible', NULL),
('CHEM001', 'Quimica General', 'Raymond Chang', 'Disponible', NULL),
('CHEM002', 'Quimica Organica', 'John McMurry', 'Disponible', NULL),
('CHEM003', 'Fisicoquimica', 'Peter Atkins', 'Reservado', NULL),
('CHEM004', 'Quimica Inorganica', 'Gary L. Miessler', 'Disponible', NULL),
('ENG001', 'Technical Writing', 'John M. Lannon', 'Disponible', NULL),
('ENG002', 'English Grammar in Use', 'Raymond Murphy', 'No disponible', NULL),
('ENG003', 'Business English', 'David Cotton', 'Disponible', NULL),
('HIST001', 'Historia Universal', 'William J. Duiker', 'Disponible', NULL),
('HIST002', 'Historia de America Latina', 'Leslie Bethell', 'Reservado', NULL),
('HIST003', 'Historia del Arte', 'Ernst Gombrich', 'Disponible', NULL),
('LIT001', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 'Disponible', NULL),
('LIT002', 'Cien Anos de Soledad', 'Gabriel Garcia Marquez', 'Disponible', NULL),
('LIT003', 'El Principito', 'Antoine de Saint-Exupery', 'Reservado', NULL),
('LIT004', 'Cronica de una Muerte Anunciada', 'Gabriel Garcia Marquez', 'Disponible', NULL);

-- ========================
-- Tabla: reservas
-- ========================
CREATE TABLE reservas (
  id_reserva INT NOT NULL AUTO_INCREMENT,
  id_usuario INT UNSIGNED NOT NULL,
  id_libro INT NOT NULL,
  fichaReserva VARCHAR(10) NOT NULL UNIQUE,
  fechaReserva DATETIME DEFAULT NULL COMMENT 'Fecha cuando el bibliotecario autoriza la reserva',
  fechaDevolucion DATETIME DEFAULT NULL COMMENT 'Fecha límite de devolución (2 días después de fechaReserva)',
  estado ENUM('pendiente','autorizada','finalizada','cancelada') NOT NULL DEFAULT 'pendiente',
  PRIMARY KEY (id_reserva),
  KEY idx_reservas_usuario (id_usuario),
  KEY idx_reservas_libro (id_libro),
  UNIQUE KEY uq_ficha_reserva (fichaReserva),
  CONSTRAINT fk_reservas_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_reservas_libro
    FOREIGN KEY (id_libro) REFERENCES libros (id_libro)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de ejemplo para reservas
INSERT INTO reservas (id_usuario, id_libro, fichaReserva, fechaReserva, fechaDevolucion, estado) VALUES
(2, 3, 'Let45A', '2025-08-10 10:00:00', '2025-08-12 10:00:00', 'autorizada'),
(3, 6, 'Met78B', '2025-08-11 14:30:00', '2025-08-13 14:30:00', 'autorizada'),
(4, 8, 'Boo12C', NULL, NULL, 'pendiente'),
(5, 1, 'Cod89D', '2025-08-09 09:15:00', '2025-08-11 09:15:00', 'finalizada');

-- Reactivar validación de FKs
SET FOREIGN_KEY_CHECKS = 1;
