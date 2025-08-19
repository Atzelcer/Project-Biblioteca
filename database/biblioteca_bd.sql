-- =========================================================
-- Script de creación para la base de datos: biblioteca_bd
-- Solo nombres de archivo (sin rutas)
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Crear la BD
CREATE DATABASE IF NOT EXISTS biblioteca_bd
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE biblioteca_bd;

-- Eliminar tablas si existen
DROP TABLE IF EXISTS reservas;
DROP TABLE IF EXISTS libros;
DROP TABLE IF EXISTS usuario;

-- ========================
-- Tabla: usuario
-- ========================
CREATE TABLE usuario (
  id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fotografia VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo de la foto',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos iniciales de usuario (solo nombres de archivo)
INSERT INTO usuario (fotografia, CI, nombre, apellido, rol, celular, password, direccion, correo) VALUES
('1_Lucia_Perez.jpg', 12345, 'Maria Elena', 'Antezana', 'bibliotecario', 70123456, '8cb2237d0679ca88db6464eac60da96345513964', 'Av. Principal 123', 'bibliotecario@biblioteca.edu'),
('7_Juan_Gonzalez.jpg', 11111, 'Juan Carlos', 'Perez', 'lector', 70234567, '7b21848ac9af35be0ddb2d6b9fc3851934db8420', 'Calle Los Pinos 456', 'juan.perez@gmail.com'),
('13_Lucia_Ramirez.jpg', 22222, 'Ana Maria', 'Rodriguez', 'lector', 70345678, '1a9b9508b6003b68ddfe03a9c8cbc4bd4388339b', 'Av. Libertadores 789', 'ana.rodriguez@gmail.com'),
('23_Pedro_Torres.jpg', 33333, 'Carlos Alberto', 'Lopez', 'lector', 70456789, '403d9917c3e950798601addf7ba82cd3c83f344b', 'Calle San Martin 321', 'carlos.lopez@gmail.com'),
('11_Laura_Perez.jpg', 44444, 'Sofia', 'Gonzalez', 'lector', 70567890, '3e511da7577d1864871b760ab30e05b56943c9b2', 'Av. America 654', 'sofia.gonzalez@gmail.com'),
('28_Diego_Fernandez.jpg', 55555, 'Diego Fernando', 'Martinez', 'lector', 70678901, '69df79bef9287d3bcb8f104a408b06de6a108fd8', 'Calle Bolivar 987', 'diego.martinez@gmail.com'),
('14_Maria_Rodriguez.jpg', 67890, 'Laura Patricia', 'Vargas', 'lector', 70789012, '230991abcd77e8173edb0af392e1f11120051e29', 'Calle Murillo 147', 'laura.vargas@gmail.com'),
('10_Roberto_Gonzalez.jpg', 13579, 'Roberto', 'Silva', 'lector', 70890123, '93ba1608fc10b710894fb9f8c89724c6eeb44d11', 'Av. 6 de Agosto 258', 'roberto.silva@gmail.com'),
('12_Carmen_Ramirez.jpg', 24680, 'Carmen', 'Torres', 'lector', 70901234, '23fbf23c9201b754adcb2fca0c8e4b19b8a7c740', 'Calle Comercio 369', 'carmen.torres@gmail.com');

-- ========================
-- Tabla: libros
-- ========================
CREATE TABLE libros (
  id_libro INT NOT NULL AUTO_INCREMENT,
  codigo_libro VARCHAR(20) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  autor VARCHAR(100) NOT NULL,
  estado ENUM('Disponible','Reservado','No disponible') NOT NULL DEFAULT 'Disponible',
  foto_libro VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo de portada',
  PRIMARY KEY (id_libro),
  UNIQUE KEY uq_libros_codigo (codigo_libro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos iniciales de libros (solo nombres de archivo)
INSERT INTO libros (codigo_libro, nombre, autor, estado, foto_libro) VALUES
('PROG001', 'Estructura de Datos con C', 'Luis Joyanes Aguilar', 'Disponible', 'Estructura_de_Datos_con_C.jpg'),
('PROG002', 'Fundamentos de Programacion', 'Kenneth A. Lambert', 'Disponible', 'Fundamentos_de_Programacion.jpg'),
('MATH001', 'Algebra Lineal y sus Aplicaciones', 'David C. Lay', 'Reservado', 'Algebra_Lineal_y_sus_Aplicaciones.jpg'),
('MATH002', 'Calculo Infinitesimal', 'Michael Spivak', 'No disponible', 'Automate_the_Boring_Stuff.jpg'),
('PROG003', 'Clean Code', 'Robert C. Martin', 'Disponible', 'Clean_Code.jpg'),
('MATH003', 'Introduccion a la Teoria de Numeros', 'G.H. Hardy', 'Reservado', 'Introduccion_a_la_Teoria_de_Numeros.jpg'),
('PROG004', 'Eloquent JavaScript', 'Marijn Haverbeke', 'Disponible', 'Eloquent_JavaScript.jpg'),
('PROG005', 'Python para Todos', 'Charles Severance', 'Reservado', 'Python_para_Todos.jpg'),
('MATH004', 'Analisis Matematico', 'Tom M. Apostol', 'Disponible', 'Analisis_Matematico.jpg'),
('PROG006', 'The Pragmatic Programmer', 'Andrew Hunt, David T. Thomas', 'No disponible', 'The_Pragmatic_Programmer.jpg'),
('PROG007', 'Java The Complete Reference', 'Herbert Schildt', 'Disponible', 'Java_The_Complete_Reference.jpg'),
('PROG008', 'Learning Python', 'Mark Lutz', 'Disponible', 'Learning_Python.jpg'),
('PROG009', 'Design Patterns', 'Gang of Four', 'Disponible', 'Design_Patterns.jpg'),
('PROG010', 'Introduction to Algorithms', 'Thomas H. Cormen', 'Reservado', 'Introduction_to_Algorithms.jpg'),
('PROG011', 'Code Complete', 'Steve McConnell', 'Disponible', 'Code_Complete.jpg'),
('PROG012', 'You Dont Know JS', 'Kyle Simpson', 'Disponible', 'You_Dont_Know_JS.jpg'),
('PROG013', 'Effective Java', 'Joshua Bloch', 'No disponible', 'Effective_Java.jpg'),
('PROG014', 'Head First Design Patterns', 'Eric Freeman', 'Disponible', 'Head_First_Design_Patterns.jpg'),
('PROG015', 'Refactoring', 'Martin Fowler', 'Disponible', 'Refactoring.jpg'),
('PROG016', 'Clean Architecture', 'Robert C. Martin', 'Reservado', 'Clean_Architecture.jpg'),
('PROG017', 'Cracking the Coding Interview', 'Gayle McDowell', 'Disponible', 'Cracking_the_Coding_Interview.jpg'),
('PROG018', 'JavaScript The Good Parts', 'Douglas Crockford', 'Disponible', 'JavaScript_The_Good_Parts.jpg'),
('PROG019', 'Python Crash Course', 'Eric Matthes', 'Disponible', 'Python_Crash_Course.jpg'),
('PROG020', 'Automate the Boring Stuff', 'Al Sweigart', 'No disponible', 'Automate_the_Boring_Stuff.jpg');

-- ========================
-- Tabla: reservas
-- ========================
CREATE TABLE reservas (
  id_reserva INT NOT NULL AUTO_INCREMENT,
  id_usuario INT UNSIGNED NOT NULL,
  id_libro INT NOT NULL,
  fichaReserva VARCHAR(10) NOT NULL UNIQUE,
  fechaReserva DATETIME DEFAULT NULL COMMENT 'Fecha cuando el bibliotecario autoriza la reserva',
  fechaDevolucion DATETIME DEFAULT NULL COMMENT 'Fecha límite de devolución',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de ejemplo reservas
INSERT INTO reservas (id_usuario, id_libro, fichaReserva, fechaReserva, fechaDevolucion, estado) VALUES
(2, 3, 'Let45A', '2025-08-10 10:00:00', '2025-08-12 10:00:00', 'autorizada'),
(3, 6, 'Met78B', '2025-08-11 14:30:00', '2025-08-13 14:30:00', 'autorizada'),
(4, 8, 'Boo12C', NULL, NULL, 'pendiente'),
(5, 1, 'Cod89D', '2025-08-09 09:15:00', '2025-08-11 09:15:00', 'finalizada');

SET FOREIGN_KEY_CHECKS = 1;
