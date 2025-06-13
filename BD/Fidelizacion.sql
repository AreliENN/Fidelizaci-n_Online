-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2025 a las 10:12:03
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
-- Base de datos: `fidelizacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id_admin` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_admin`, `nombre`, `telefono`, `contraseña`) VALUES
(1, 'Carlos Pérez', '1234567890', 'admin123'),
(2, 'Laura Gómez', '9971215425', 'admin456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `beneficios`
--

CREATE TABLE `beneficios` (
  `id_beneficio` int(11) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `vigencia_de` date DEFAULT NULL,
  `vigencia_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `beneficios`
--

INSERT INTO `beneficios` (`id_beneficio`, `nombre_empresa`, `descripcion`, `descuento`, `vigencia_de`, `vigencia_hasta`, `activo`, `imagen`) VALUES
(1, 'Farmacia SaludPlus', '25% de descuento en medicamentos seleccionados.', '25% OFF', '2025-06-01', '2025-12-31', 1, 'img/beneficios/farmacia.jpg'),
(2, 'Gimnasio FitMax', 'Acceso libre durante 3 meses con tu tarjeta.', '3 meses gratis', '2025-05-15', '2025-09-15', 1, 'img/beneficios/gimnasio.jpg'),
(3, 'Cine Centro', 'Entradas al 2x1 todos los miércoles.', '2x1 Miércoles', '2025-06-10', '2025-08-30', 1, 'img/beneficios/cine.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bonificacion_puntos`
--

CREATE TABLE `bonificacion_puntos` (
  `id_bonificacion` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `monto_compra` decimal(10,2) NOT NULL,
  `puntos_acreditados` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bonificacion_puntos`
--

INSERT INTO `bonificacion_puntos` (`id_bonificacion`, `id_cliente`, `monto_compra`, `puntos_acreditados`, `fecha`) VALUES
(1, 1, 2000.00, 100, '2025-06-12 20:16:49'),
(2, 1, 2000.00, 100, '2025-06-12 20:17:47'),
(3, 1, 2000.00, 100, '2025-06-12 20:18:21'),
(4, 1, 2000.00, 100, '2025-06-12 20:20:24'),
(5, 4, 2000.00, 100, '2025-06-13 00:18:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canje_puntos`
--

CREATE TABLE `canje_puntos` (
  `id_canje` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_premio` int(11) NOT NULL,
  `puntos_usados` int(11) NOT NULL,
  `puntos_restantes` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canje_puntos`
--

INSERT INTO `canje_puntos` (`id_canje`, `id_cliente`, `id_premio`, `puntos_usados`, `puntos_restantes`, `fecha`) VALUES
(2, 1, 2, 150, 0, '2025-06-12 19:58:46'),
(4, 1, 2, 150, 0, '2025-06-12 23:08:57'),
(5, 1, 2, 150, 0, '2025-06-12 23:12:16'),
(6, 1, 2, 160, 0, '2025-06-13 01:19:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `telefono_movil` varchar(15) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(80) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `correo_electronico` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `puntos` int(11) NOT NULL DEFAULT 0,
  `tarjeta_digital` varchar(100) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `telefono_movil`, `nombre`, `apellidos`, `contraseña`, `direccion`, `correo_electronico`, `estado`, `ciudad`, `puntos`, `tarjeta_digital`, `imagen`) VALUES
(1, '1234567890', 'Maria', 'Martínez', 'cliente123', 'Calle 1 #123', 'ana@example.com', 'CDMX', 'Ciudad de México', 140, 'si', 'img/Usuario1.png'),
(4, '9971215425', 'Mario', 'Vela', 'cliente456', 'calle 57 x 64', 'mario@gmail.com', 'CDMX', 'Ciudad de México', 500, 'si', 'img/Usuario2.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `premios`
--

CREATE TABLE `premios` (
  `id_premio` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `puntos_requeridos` int(11) NOT NULL,
  `disponibles` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `premios`
--

INSERT INTO `premios` (`id_premio`, `nombre`, `descripcion`, `puntos_requeridos`, `disponibles`, `activo`, `imagen`) VALUES
(2, 'Membresía Spotify', '3 meses de Spotify Premium', 160, 7, 1, 'img/spotify.jpg'),
(3, 'Tarjeta regalo Amazon', 'Vale de $200 MXN en Amazon', 200, 50, 1, 'img/premio_684bd0d6924d64.66882062.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjeta`
--

CREATE TABLE `tarjeta` (
  `id_tarjeta` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `numero_tarjeta` varchar(16) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `cvv` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarjeta`
--

INSERT INTO `tarjeta` (`id_tarjeta`, `id_cliente`, `numero_tarjeta`, `fecha_vencimiento`, `cvv`) VALUES
(1, 1, '7134457555776446', '2029-06-13', '561');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- Indices de la tabla `beneficios`
--
ALTER TABLE `beneficios`
  ADD PRIMARY KEY (`id_beneficio`);

--
-- Indices de la tabla `bonificacion_puntos`
--
ALTER TABLE `bonificacion_puntos`
  ADD PRIMARY KEY (`id_bonificacion`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `canje_puntos`
--
ALTER TABLE `canje_puntos`
  ADD PRIMARY KEY (`id_canje`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_premio` (`id_premio`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `telefono_movil` (`telefono_movil`);

--
-- Indices de la tabla `premios`
--
ALTER TABLE `premios`
  ADD PRIMARY KEY (`id_premio`);

--
-- Indices de la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  ADD PRIMARY KEY (`id_tarjeta`),
  ADD UNIQUE KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `beneficios`
--
ALTER TABLE `beneficios`
  MODIFY `id_beneficio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `bonificacion_puntos`
--
ALTER TABLE `bonificacion_puntos`
  MODIFY `id_bonificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `canje_puntos`
--
ALTER TABLE `canje_puntos`
  MODIFY `id_canje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `premios`
--
ALTER TABLE `premios`
  MODIFY `id_premio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  MODIFY `id_tarjeta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bonificacion_puntos`
--
ALTER TABLE `bonificacion_puntos`
  ADD CONSTRAINT `bonificacion_puntos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `canje_puntos`
--
ALTER TABLE `canje_puntos`
  ADD CONSTRAINT `canje_puntos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `canje_puntos_ibfk_2` FOREIGN KEY (`id_premio`) REFERENCES `premios` (`id_premio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  ADD CONSTRAINT `tarjeta_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
