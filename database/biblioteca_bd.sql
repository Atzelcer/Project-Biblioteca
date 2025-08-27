-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 13:41:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblioteca_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id_libro` int(11) NOT NULL,
  `codigo_libro` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `estado` enum('Disponible','Reservado','No disponible') NOT NULL DEFAULT 'Disponible',
  `foto_libro` varchar(255) DEFAULT NULL COMMENT 'Nombre del archivo de portada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id_libro`, `codigo_libro`, `nombre`, `autor`, `estado`, `foto_libro`) VALUES
(1, 'PROG001', 'Estructura de Datos con C', 'Luis Joyanes Aguilar', 'Disponible', 'Estructura_de_Datos_con_C.jpg'),
(2, 'PROG002', 'Fundamentos de Programacion', 'Kenneth A. Lambert', 'Disponible', 'Fundamentos_de_Programacion.jpg'),
(3, 'MATH001', 'Algebra Lineal y sus Aplicaciones', 'David C. Lay', 'Reservado', 'Algebra_Lineal_y_sus_Aplicaciones.jpg'),
(4, 'MATH002', 'Calculo Infinitesimal', 'Michael Spivak', 'No disponible', 'Calculo_Infinitesimal.jpg'),
(5, 'PROG003', 'Clean Code', 'Robert C. Martin', 'Reservado', 'Clean_Code.jpg'),
(6, 'MATH003', 'Introduccion a la Teoria de Numeros', 'G.H. Hardy', 'Reservado', 'Introduccion_a_la_Teoria_de_Numeros.jpg'),
(7, 'PROG004', 'Eloquent JavaScript', 'Marijn Haverbeke', 'Reservado', 'Eloquent_JavaScript.jpg'),
(8, 'PROG005', 'Python para Todos', 'Charles Severance', 'Reservado', 'Python_para_Todos.jpg'),
(9, 'MATH004', 'Analisis Matematico', 'Tom M. Apostol', 'Reservado', 'Analisis_Matematico.jpg'),
(10, 'PROG006', 'The Pragmatic Programmer', 'Andrew Hunt, David T. Thomas', 'No disponible', 'The_Pragmatic_Programmer.jpg'),
(11, 'PROG007', 'Java The Complete Reference', 'Herbert Schildt', 'Disponible', 'Java_The_Complete_Reference.jpg'),
(12, 'PROG008', 'Learning Python', 'Mark Lutz', 'Disponible', 'Learning_Python.jpg'),
(13, 'PROG009', 'Design Patterns', 'Gang of Four', 'Reservado', 'Design_Patterns.jpg'),
(14, 'PROG010', 'Introduction to Algorithms', 'Thomas H. Cormen', 'Reservado', 'Introduction_to_Algorithms.jpg'),
(15, 'PROG011', 'Code Complete', 'Steve McConnell', 'Reservado', 'Code_Complete.jpg'),
(16, 'PROG012', 'You Dont Know JS', 'Kyle Simpson', 'Disponible', 'You_Dont_Know_JS.jpg'),
(17, 'PROG013', 'Effective Java', 'Joshua Bloch', 'No disponible', 'Effective_Java.jpg'),
(18, 'PROG014', 'Head First Design Patterns', 'Eric Freeman', 'Disponible', 'Head_First_Design_Patterns.jpg'),
(19, 'PROG015', 'Refactoring', 'Martin Fowler', 'Disponible', 'Refactoring.jpg'),
(20, 'PROG016', 'Clean Architecture', 'Robert C. Martin', 'Reservado', 'Clean_Architecture.jpg'),
(21, 'PROG017', 'Cracking the Coding Interview', 'Gayle McDowell', 'Reservado', 'Cracking_the_Coding_Interview.jpg'),
(22, 'PROG018', 'JavaScript The Good Parts', 'Douglas Crockford', 'No disponible', 'JavaScript_The_Good_Parts.jpg'),
(23, 'PROG019', 'Python Crash Course', 'Eric Matthes', 'Disponible', 'Python_Crash_Course.jpg'),
(24, 'PROG020', 'Automate the Boring Stuff', 'Al Sweigart', 'No disponible', 'Automate_the_Boring_Stuff.jpg'),
(25, 'MATH005', 'Calculo de Variable Compleja', 'James Ward Brown', 'No disponible', 'Calculo_de_Variable_Compleja.jpg'),
(26, 'MATH006', 'Algebra Abstracta', 'David S. Dummit', 'Reservado', 'Algebra_Abstracta.jpg'),
(27, 'MATH007', 'Ecuaciones Diferenciales', 'Dennis G. Zill', 'Reservado', 'Ecuaciones_Diferenciales.jpg'),
(28, 'MATH008', 'Probabilidad y Estadistica', 'Ronald E. Walpole', 'Disponible', 'Probabilidad_y_Estadistica.jpg'),
(29, 'MATH009', 'Geometria Analitica', 'Charles H. Lehmann', 'Disponible', 'Geometria_Analitica.jpg'),
(30, 'MATH010', 'Topologia General', 'John L. Kelley', 'No disponible', 'Topologia_General.jpg'),
(31, 'MATH011', 'Analisis Real', 'Walter Rudin', 'Reservado', 'Analisis_Real.jpg'),
(32, 'MATH012', 'Algebra Lineal Numerica', 'Lloyd N. Trefethen', 'Reservado', 'Algebra_Lineal_Numerica.jpg'),
(33, 'PHYS001', 'Fisica Universitaria', 'Hugh D. Young', 'Disponible', 'Fisica_Universitaria.jpg'),
(34, 'PHYS002', 'Mecanica Clasica', 'Herbert Goldstein', 'Disponible', 'Mecanica_Clasica.jpg'),
(35, 'PHYS003', 'Electromagnetismo', 'David J. Griffiths', 'No disponible', 'Electromagnetismo.jpg'),
(36, 'PHYS004', 'Termodinamica', 'Mark W. Zemansky', 'Disponible', 'Termodinamica.jpg'),
(37, 'PHYS005', 'Optica', 'Eugene Hecht', 'Reservado', 'Optica.jpg'),
(38, 'PHYS006', 'Fisica Moderna', 'Kenneth S. Krane', 'Disponible', 'Fisica_Moderna.jpg'),
(39, 'CHEM001', 'Quimica General', 'Raymond Chang', 'Disponible', 'Quimica_General.jpg'),
(40, 'CHEM002', 'Quimica Organica', 'John McMurry', 'Disponible', 'Quimica_Organica.jpg'),
(41, 'CHEM003', 'Fisicoquimica', 'Peter Atkins', 'Disponible', 'Fisicoquimica.jpg'),
(42, 'CHEM004', 'Quimica Inorganica', 'Gary L. Miessler', 'Disponible', 'Quimica_Inorganica.jpg'),
(43, 'ENG001', 'Technical Writing', 'John M. Lannon', 'Disponible', 'Technical_Writing.jpg'),
(44, 'ENG002', 'English Grammar in Use', 'Raymond Murphy', 'No disponible', 'English_Grammar_in_Use.jpg'),
(45, 'ENG003', 'Business English', 'David Cotton', 'Reservado', 'Business_English.jpg'),
(46, 'HIST001', 'Historia Universal', 'William J. Duiker', 'Disponible', 'Historia_Universal.jpg'),
(47, 'HIST002', 'Historia de America Latina', 'Leslie Bethell', 'Reservado', 'Historia_de_America_Latina.jpg'),
(48, 'HIST003', 'Historia del Arte', 'Ernst Gombrich', 'Disponible', 'Historia_del_Arte.jpg'),
(49, 'LIT001', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 'Reservado', 'Don_Quijote_de_la_Mancha.jpg'),
(50, 'LIT002', 'Cien Anos de Soledad', 'Gabriel Garcia Marquez', 'Reservado', 'Cien_Anos_de_Soledad.jpg'),
(51, 'LIT003', 'El Principito', 'Antoine de Saint-Exupery', 'Reservado', 'El_Principito.jpg'),
(52, 'LIT004', 'Cronica de una Muerte Anunciada', 'Gabriel Garcia Marquez', 'Reservado', 'Cronica_de_una_Muerte_Anunciada.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_libro` int(11) NOT NULL,
  `fichaReserva` varchar(10) NOT NULL,
  `fechaReserva` datetime DEFAULT NULL COMMENT 'Fecha cuando el bibliotecario autoriza la reserva',
  `fechaDevolucion` datetime DEFAULT NULL COMMENT 'Fecha límite de devolución',
  `fechaDevolucionReal` datetime DEFAULT NULL COMMENT 'Fecha real cuando se devolvió el libro',
  `estado` enum('pendiente','autorizada','finalizada','cancelada') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_usuario`, `id_libro`, `fichaReserva`, `fechaReserva`, `fechaDevolucion`, `fechaDevolucionReal`, `estado`) VALUES
(1, 2, 3, 'Let45A', '2025-08-12 10:00:00', '2025-08-14 10:00:00', '2025-08-25 01:50:19', 'pendiente'),
(2, 3, 6, 'Met78B', '2025-08-12 14:30:00', '2025-08-14 14:30:00', '2025-08-18 02:02:00', 'autorizada'),
(3, 4, 10, 'Tec21E', '2025-08-21 11:00:00', '2025-08-25 11:00:00', '2025-08-26 22:15:39', 'autorizada'),
(4, 5, 14, 'Mat33F', '2025-08-18 09:45:00', '2025-08-20 09:45:00', '2025-08-25 22:19:22', 'autorizada'),
(5, 6, 16, 'Pro56G', '2025-08-20 14:20:00', '2025-08-22 14:20:00', '2025-08-26 22:21:00', 'autorizada'),
(6, 7, 27, 'Phy77H', '2025-08-22 08:10:00', '2025-08-26 08:10:00', '2025-08-27 22:21:58', 'autorizada'),
(7, 8, 30, 'His89I', '2025-08-11 15:00:00', '2025-08-13 15:00:00', '2025-08-26 22:23:03', 'autorizada'),
(8, 9, 35, 'Lit90J', '2025-08-18 13:30:00', '2025-08-20 13:30:00', '2025-08-22 06:41:08', 'autorizada'),
(9, 10, 40, 'Eng65K', '2025-08-19 16:45:00', '2025-08-21 16:45:00', '2025-08-25 06:41:55', 'autorizada'),
(10, 11, 42, 'Che87L', '2025-08-20 10:25:00', '2025-08-22 10:25:00', '2025-08-26 06:42:38', 'autorizada'),
(11, 2, 8, 'Boo12C', '2025-08-20 06:44:04', '2025-08-22 06:44:04', '2025-08-26 06:44:04', 'pendiente'),
(12, 4, 12, 'Dev45M', '2025-08-20 06:44:59', '2025-08-22 06:44:59', '2025-08-25 06:44:59', 'pendiente'),
(13, 6, 18, 'Sci78N', '2025-08-22 06:45:29', '2025-08-25 06:45:29', '2025-08-26 06:45:29', 'cancelada'),
(14, 8, 22, 'Art91P', '2025-08-24 15:15:19', '2025-08-26 15:15:19', '2025-08-26 06:46:08', 'autorizada'),
(15, 10, 25, 'His34Q', '2025-08-24 07:37:58', '2025-08-26 07:37:58', '2025-08-26 06:46:24', 'autorizada'),
(16, 3, 1, 'Cod89D', '2025-08-18 09:15:00', '2025-08-20 09:15:00', '2025-08-20 06:46:41', 'finalizada'),
(17, 5, 5, 'Cle12R', '2025-08-20 11:30:00', '2025-08-22 11:30:00', '2025-08-22 06:47:20', 'finalizada'),
(18, 7, 9, 'Des56S', '2025-08-19 14:45:00', '2025-08-21 14:45:00', '2025-08-21 06:48:17', 'finalizada'),
(19, 9, 15, 'Ref78T', '2025-01-08 16:20:00', '2025-08-27 16:20:00', '2025-08-27 06:49:53', 'finalizada'),
(20, 12, 19, 'Pyt67U', '2025-08-25 09:00:00', '2025-08-27 09:00:00', '2025-08-27 06:50:31', 'autorizada'),
(21, 2, 24, 'Mat89V', '2025-08-21 11:15:00', '2025-08-22 11:15:00', '2025-08-22 06:52:52', 'autorizada'),
(22, 4, 28, 'Phy45W', '2025-08-21 13:40:00', '2025-08-25 13:40:00', '2025-08-25 06:54:36', 'autorizada'),
(23, 6, 32, 'Che67X', '2025-08-19 15:20:00', '2025-08-21 15:20:00', '2025-08-25 06:56:06', 'autorizada'),
(24, 8, 37, 'Eng89Y', '2025-08-25 08:30:00', '2025-08-27 08:30:00', '2025-08-27 06:56:50', 'autorizada'),
(25, 10, 41, 'His12Z', '2025-01-28 10:45:00', '2025-01-30 10:45:00', '2025-08-24 01:38:12', 'finalizada'),
(27, 2, 26, 'KSKHTK', NULL, NULL, NULL, 'pendiente'),
(28, 2, 9, 'QP3MQU', NULL, NULL, NULL, 'pendiente'),
(29, 2, 31, 'M3AZYM', NULL, NULL, NULL, 'pendiente'),
(30, 2, 45, 'LJJGZ4', NULL, NULL, NULL, 'pendiente'),
(31, 2, 50, 'QABX86', NULL, NULL, NULL, 'pendiente'),
(32, 1, 5, 'KZNNWD', NULL, NULL, NULL, 'pendiente'),
(33, 1, 15, 'JGAUN2', NULL, NULL, NULL, 'pendiente'),
(34, 1, 13, 'L5726T', NULL, NULL, NULL, 'pendiente'),
(35, 1, 7, 'M7MLD7', NULL, NULL, NULL, 'pendiente'),
(36, 1, 21, 'C3DJVH', NULL, NULL, NULL, 'pendiente'),
(37, 2, 52, 'DWPTWD', NULL, NULL, NULL, 'pendiente'),
(38, 2, 49, 'P3VJ6D', NULL, NULL, NULL, 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `fotografia` varchar(255) DEFAULT NULL COMMENT 'Nombre del archivo de fotografía',
  `CI` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `rol` enum('bibliotecario','lector') NOT NULL DEFAULT 'lector',
  `celular` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `estado` enum('bloqueado','normal') NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `fotografia`, `CI`, `nombre`, `apellido`, `rol`, `celular`, `password`, `direccion`, `correo`, `estado`) VALUES
(1, '1_Maria_Martinez.jpg', 12345, 'Maria Elena', 'Antezana', 'bibliotecario', 70123456, '8cb2237d0679ca88db6464eac60da96345513964', 'Av. Principal 123', 'bibliotecario@biblioteca.edu', 'normal'),
(2, '7_Pedro_Lopez.jpg', 11111, 'Juan Carlos', 'Perez', 'lector', 70234567, '7b21848ac9af35be0ddb2d6b9fc3851934db8420', 'Calle Los Pinos 456', 'juan.perez@gmail.com', 'normal'),
(3, '4_Ana_Gonzalez.jpg', 22222, 'Ana Maria', 'Rodriguez', 'lector', 70345678, '1a9b9508b6003b68ddfe03a9c8cbc4bd4388339b', 'Av. Libertadores 789', 'ana.rodriguez@gmail.com', 'normal'),
(4, '10_Diego_Lopez.jpg', 33333, 'Carlos Alberto', 'Lopez', 'lector', 70456789, '403d9917c3e950798601addf7ba82cd3c83f344b', 'Calle San Martin 321', 'carlos.lopez@gmail.com', 'normal'),
(5, '5_Carmen_Rodriguez.jpg', 44444, 'Sofia', 'Gonzalez', 'lector', 70567890, '3e511da7577d1864871b760ab30e05b56943c9b2', 'Av. America 654', 'sofia.gonzalez@gmail.com', 'normal'),
(6, '8_Jorge_Martinez.jpg', 55555, 'Diego Fernando', 'Martinez', 'lector', 70678901, '69df79bef9287d3bcb8f104a408b06de6a108fd8', 'Calle Bolivar 987', 'diego.martinez@gmail.com', 'normal'),
(7, '6_Luis_Vargas.jpg', 67890, 'Laura Patricia', 'Vargas', 'lector', 70789012, '230991abcd77e8173edb0af392e1f11120051e29', 'Calle Murillo 147', 'laura.vargas@gmail.com', 'normal'),
(8, '15_Roberto_Lopez.jpg', 13579, 'Roberto', 'Silva', 'lector', 70890123, '93ba1608fc10b710894fb9f8c89724c6eeb44d11', 'Av. 6 de Agosto 258', 'roberto.silva@gmail.com', 'normal'),
(9, '12_Carlos_Ramirez.jpg', 24680, 'Carmen', 'Torres', 'lector', 70901234, '23fbf23c9201b754adcb2fca0c8e4b19b8a7c740', 'Calle Comercio 369', 'carmen.torres@gmail.com', 'normal'),
(10, '9_Juan_Rodriguez.jpg', 35790, 'Fernando', 'Morales', 'lector', 71012345, 'c7b47a7beafeaccac334dd6b4b18f5c00b8797ec', 'Av. Ballivián 741', 'fernando.morales@gmail.com', 'normal'),
(11, '11_Jorge_Gonzalez.jpg', 46801, 'Patricia', 'Herrera', 'lector', 71123456, '5e8c5c98e7e1dda8a8c61e13ef9c7b5c7b93c5bb', 'Calle Sucre 852', 'patricia.herrera@gmail.com', 'normal'),
(12, '3_Maria_Rodriguez.jpg', 57912, 'Luis', 'Mendoza', 'lector', 71234567, 'a12b4b24e25eb2f68e3b5c83b6fba2c9c4e8f9af', 'Av. Arce 963', 'luis.mendoza@gmail.com', 'normal');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id_libro`),
  ADD UNIQUE KEY `uq_libros_codigo` (`codigo_libro`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD UNIQUE KEY `fichaReserva` (`fichaReserva`),
  ADD UNIQUE KEY `uq_ficha_reserva` (`fichaReserva`),
  ADD KEY `idx_reservas_usuario` (`id_usuario`),
  ADD KEY `idx_reservas_libro` (`id_libro`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuario_ci` (`CI`),
  ADD UNIQUE KEY `uq_usuario_correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reservas_libro` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id_libro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

