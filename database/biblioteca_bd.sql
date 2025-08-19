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
('1_Maria_Martinez.jpg', 12345, 'Maria Elena', 'Antezana', 'bibliotecario', 70123456, '8cb2237d0679ca88db6464eac60da96345513964', 'Av. Principal 123', 'bibliotecario@biblioteca.edu'),
('7_Pedro_Lopez.jpg', 11111, 'Juan Carlos', 'Perez', 'lector', 70234567, '7b21848ac9af35be0ddb2d6b9fc3851934db8420', 'Calle Los Pinos 456', 'juan.perez@gmail.com'),
('4_Ana_Gonzalez.jpg', 22222, 'Ana Maria', 'Rodriguez', 'lector', 70345678, '1a9b9508b6003b68ddfe03a9c8cbc4bd4388339b', 'Av. Libertadores 789', 'ana.rodriguez@gmail.com'),
('10_Diego_Lopez.jpg', 33333, 'Carlos Alberto', 'Lopez', 'lector', 70456789, '403d9917c3e950798601addf7ba82cd3c83f344b', 'Calle San Martin 321', 'carlos.lopez@gmail.com'),
('5_Carmen_Rodriguez.jpg', 44444, 'Sofia', 'Gonzalez', 'lector', 70567890, '3e511da7577d1864871b760ab30e05b56943c9b2', 'Av. America 654', 'sofia.gonzalez@gmail.com'),
('8_Jorge_Martinez.jpg', 55555, 'Diego Fernando', 'Martinez', 'lector', 70678901, '69df79bef9287d3bcb8f104a408b06de6a108fd8', 'Calle Bolivar 987', 'diego.martinez@gmail.com'),
('6_Luis_Vargas.jpg', 67890, 'Laura Patricia', 'Vargas', 'lector', 70789012, '230991abcd77e8173edb0af392e1f11120051e29', 'Calle Murillo 147', 'laura.vargas@gmail.com'),
('15_Roberto_Lopez.jpg', 13579, 'Roberto', 'Silva', 'lector', 70890123, '93ba1608fc10b710894fb9f8c89724c6eeb44d11', 'Av. 6 de Agosto 258', 'roberto.silva@gmail.com'),
('12_Carlos_Ramirez.jpg', 24680, 'Carmen', 'Torres', 'lector', 70901234, '23fbf23c9201b754adcb2fca0c8e4b19b8a7c740', 'Calle Comercio 369', 'carmen.torres@gmail.com');

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
('PROG001', 'Estructura de Datos con C', 'Luis Joyanes Aguilar', 'Disponible', 'Estructura_de_Datos_con_C.jpg'),
('PROG002', 'Fundamentos de Programacion', 'Kenneth A. Lambert', 'Disponible', 'Fundamentos_de_Programacion.jpg'),
('MATH001', 'Algebra Lineal y sus Aplicaciones', 'David C. Lay', 'Reservado', 'Algebra_Lineal_y_sus_Aplicaciones.jpg'),
('MATH002', 'Calculo Infinitesimal', 'Michael Spivak', 'No disponible', 'Calculo_Infinitesimal.jpg'),
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
('PROG020', 'Automate the Boring Stuff', 'Al Sweigart', 'No disponible', 'Automate_the_Boring_Stuff.jpg'),
('MATH005', 'Calculo de Variable Compleja', 'James Ward Brown', 'Disponible', 'Calculo_de_Variable_Compleja.jpg'),
('MATH006', 'Algebra Abstracta', 'David S. Dummit', 'Disponible', 'Algebra_Abstracta.jpg'),
('MATH007', 'Ecuaciones Diferenciales', 'Dennis G. Zill', 'Reservado', 'Ecuaciones_Diferenciales.jpg'),
('MATH008', 'Probabilidad y Estadistica', 'Ronald E. Walpole', 'Disponible', 'Probabilidad_y_Estadistica.jpg'),
('MATH009', 'Geometria Analitica', 'Charles H. Lehmann', 'Disponible', 'Geometria_Analitica.jpg'),
('MATH010', 'Topologia General', 'John L. Kelley', 'No disponible', 'Topologia_General.jpg'),
('MATH011', 'Analisis Real', 'Walter Rudin', 'Disponible', 'Analisis_Real.jpg'),
('MATH012', 'Algebra Lineal Numerica', 'Lloyd N. Trefethen', 'Reservado', 'Algebra_Lineal_Numerica.jpg'),
('PHYS001', 'Fisica Universitaria', 'Hugh D. Young', 'Disponible', 'Fisica_Universitaria.jpg'),
('PHYS002', 'Mecanica Clasica', 'Herbert Goldstein', 'Disponible', 'Mecanica_Clasica.jpg'),
('PHYS003', 'Electromagnetismo', 'David J. Griffiths', 'No disponible', 'Electromagnetismo.jpg'),
('PHYS004', 'Termodinamica', 'Mark W. Zemansky', 'Disponible', 'Termodinamica.jpg'),
('PHYS005', 'Optica', 'Eugene Hecht', 'Reservado', 'Optica.jpg'),
('PHYS006', 'Fisica Moderna', 'Kenneth S. Krane', 'Disponible', 'Fisica_Moderna.jpg'),
('CHEM001', 'Quimica General', 'Raymond Chang', 'Disponible', 'Quimica_General.jpg'),
('CHEM002', 'Quimica Organica', 'John McMurry', 'Disponible', 'Quimica_Organica.jpg'),
('CHEM003', 'Fisicoquimica', 'Peter Atkins', 'Reservado', 'Fisicoquimica.jpg'),
('CHEM004', 'Quimica Inorganica', 'Gary L. Miessler', 'Disponible', 'Quimica_Inorganica.jpg'),
('ENG001', 'Technical Writing', 'John M. Lannon', 'Disponible', 'Technical_Writing.jpg'),
('ENG002', 'English Grammar in Use', 'Raymond Murphy', 'No disponible', 'English_Grammar_in_Use.jpg'),
('ENG003', 'Business English', 'David Cotton', 'Disponible', 'Business_English.jpg'),
('HIST001', 'Historia Universal', 'William J. Duiker', 'Disponible', 'Historia_Universal.jpg'),
('HIST002', 'Historia de America Latina', 'Leslie Bethell', 'Reservado', 'Historia_de_America_Latina.jpg'),
('HIST003', 'Historia del Arte', 'Ernst Gombrich', 'Disponible', 'Historia_del_Arte.jpg'),
('LIT001', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 'Disponible', 'Don_Quijote_de_la_Mancha.jpg'),
('LIT002', 'Cien Anos de Soledad', 'Gabriel Garcia Marquez', 'Disponible', 'Cien_Anos_de_Soledad.jpg'),
('LIT003', 'El Principito', 'Antoine de Saint-Exupery', 'Reservado', 'El_Principito.jpg'),
('LIT004', 'Cronica de una Muerte Anunciada', 'Gabriel Garcia Marquez', 'Disponible', 'Cronica_de_una_Muerte_Anunciada.jpg');

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
-- Datos de ejemplo para reservas
INSERT INTO reservas (id_usuario, id_libro, fichaReserva, fechaReserva, fechaDevolucion, estado) VALUES
(2, 3, 'Let45A', '2025-08-10 10:00:00', '2025-08-12 10:00:00', 'autorizada'),
(3, 6, 'Met78B', '2025-08-11 14:30:00', '2025-08-13 14:30:00', 'autorizada'),
(4, 8, 'Boo12C', NULL, NULL, 'pendiente'),
(5, 1, 'Cod89D', '2025-08-09 09:15:00', '2025-08-11 09:15:00', 'finalizada'),

-- Reservas adicionales autorizadas
(6, 10, 'Tec21E', '2025-08-15 11:00:00', '2025-08-17 11:00:00', 'autorizada'),
(7, 12, 'Mat33F', '2025-08-16 09:45:00', '2025-08-18 09:45:00', 'autorizada'),
(8, 15, 'Pro56G', '2025-08-17 14:20:00', '2025-08-19 14:20:00', 'autorizada'),
(9, 18, 'Phy77H', '2025-08-18 08:10:00', '2025-08-20 08:10:00', 'autorizada'),
(2, 20, 'His89I', '2025-08-19 15:00:00', '2025-08-21 15:00:00', 'autorizada'),
(3, 25, 'Lit90J', '2025-08-20 13:30:00', '2025-08-22 13:30:00', 'autorizada'),
(4, 30, 'Eng65K', '2025-08-21 16:45:00', '2025-08-23 16:45:00', 'autorizada'),
(5, 35, 'Che87L', '2025-08-22 10:25:00', '2025-08-24 10:25:00', 'autorizada');

-- Reactivar validación de FKs
SET FOREIGN_KEY_CHECKS = 1;
