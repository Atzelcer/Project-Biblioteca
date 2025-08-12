-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-08-2025 a las 01:47:04
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
  `foto_libro` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id_libro`, `codigo_libro`, `nombre`, `autor`, `estado`, `foto_libro`) VALUES
(1, 'PROG001', 'Estructura de Datos con C', 'Luis Joyanes Aguilar', 'Disponible', NULL),
(2, 'PROG002', 'Fundamentos de Programación', 'Kenneth A. Lambert', 'Disponible', NULL),
(3, 'MATH001', 'Álgebra Lineal y sus Aplicaciones', 'David C. Lay', 'Reservado', NULL),
(4, 'MATH002', 'Cálculo Infinitesimal', 'Michael Spivak', 'No disponible', NULL),
(5, 'PROG003', 'Clean Code', 'Robert C. Martin', 'Disponible', NULL),
(6, 'MATH003', 'Introducción a la Teoría de Números', 'G.H. Hardy', 'Reservado', NULL),
(7, 'PROG004', 'Eloquent JavaScript', 'Marijn Haverbeke', 'Disponible', NULL),
(8, 'PROG005', 'Python para Todos', 'Charles Severance', 'Reservado', NULL),
(9, 'MATH004', 'Análisis Matemático', 'Tom M. Apostol', 'Disponible', NULL),
(10, 'PROG006', 'The Pragmatic Programmer', 'Andrew Hunt, David T. Thomas', 'No disponible', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_libro` int(11) NOT NULL,
  `fecha_reserva` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activa','finalizada','cancelada') NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `fotografia` varchar(100) NOT NULL,
  `ci_anverso` varchar(100) NOT NULL,
  `ci_reverso` varchar(100) NOT NULL,
  `CI` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `rol` enum('lector','bibliotecario','bloqueado') NOT NULL DEFAULT 'lector',
  `celular` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `correo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `fotografia`, `ci_anverso`, `ci_reverso`, `CI`, `nombre`, `apellido`, `rol`, `celular`, `password`, `direccion`, `correo`) VALUES
(2, 'Mendez.jpg', 'Mendez_anverso.jpg', 'Mendez_reverso.jpg', 12398028, 'Alex', 'Mendez', 'lector', 69688100, '98fc2c4a28b8c59cd2026605da64eb4ea654c32a', 'german busch 709', 'pilotomendez777@gmail.com'),
(3, '', '', '', 3639672, 'Victor', 'Mendez', 'bibliotecario', 74426126, '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'german busch 709', 'victorramiromendezcondori77@gmail.com'),
(6, '', '', '', 12345678, 'Tilin', 'Tolon', 'lector', 77777777, '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'americas', 'tilinTolon@gmail.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id_libro`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_libro` (`id_libro`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id_libro`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
