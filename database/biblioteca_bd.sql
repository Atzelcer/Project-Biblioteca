-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-08-2025 a las 03:46:18
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
  `nombre` varchar(50) NOT NULL,
  `autor` varchar(50) NOT NULL,
  `estado` enum('Disponible','Reservado','No disponible','') NOT NULL,
  `tiempo_restante` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id_libro`, `codigo_libro`, `nombre`, `autor`, `estado`, `tiempo_restante`) VALUES
(1, 'PROG001', 'Estructura de Datos con C', 'Luis Joyanes Aguilar', 'Disponible', '00:00:00'),
(2, 'PROG002', 'Fundamentos de Programación', 'Kenneth A. Lambert', 'Disponible', '00:00:00'),
(3, 'MATH001', 'Álgebra Lineal y sus Aplicaciones', 'David C. Lay', 'Reservado', '05:00:00'),
(4, 'MATH002', 'Cálculo Infinitesimal', 'Michael Spivak', 'No disponible', '00:00:00'),
(5, 'PROG003', 'Clean Code', 'Robert C. Martin', 'Disponible', '00:00:00'),
(6, 'MATH003', 'Introducción a la Teoría de Números', 'G.H. Hardy', 'Reservado', '05:00:00'),
(7, 'PROG004', 'Eloquent JavaScript', 'Marijn Haverbeke', 'Disponible', '00:00:00'),
(8, 'PROG005', 'Python para Todos', 'Charles Severance', 'Reservado', '05:00:00'),
(9, 'MATH004', 'Análisis Matemático', 'Tom M. Apostol', 'Disponible', '00:00:00'),
(10, 'PROG006', 'The Pragmatic Programmer', 'Andrew Hunt, David T. Thomas', 'No disponible', '00:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id_libro`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
