CREATE DATABASE IF NOT EXISTS `fidelizacion`;
USE `fidelizacion`;

-- Tabla: administrador
CREATE TABLE `administrador` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY (`telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: beneficios
CREATE TABLE `beneficios` (
  `id_beneficio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `vigencia_de` date DEFAULT NULL,
  `vigencia_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_beneficio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: cliente
CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
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
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY (`telefono_movil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: premios
CREATE TABLE `premios` (
  `id_premio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `puntos_requeridos` int(11) NOT NULL,
  `disponibles` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_premio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: canje_puntos
CREATE TABLE `canje_puntos` (
  `id_canje` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_premio` int(11) NOT NULL,
  `puntos_usados` int(11) NOT NULL,
  `puntos_restantes` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_canje`),
  FOREIGN KEY (`id_cliente`) REFERENCES `cliente`(`id_cliente`) ON DELETE CASCADE,
  FOREIGN KEY (`id_premio`) REFERENCES `premios`(`id_premio`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: tarjeta
CREATE TABLE `tarjeta` (
  `id_tarjeta` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `numero_tarjeta` varchar(16) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `cvv` varchar(3) NOT NULL,
  PRIMARY KEY (`id_tarjeta`),
  UNIQUE KEY (`id_cliente`),
  FOREIGN KEY (`id_cliente`) REFERENCES `cliente`(`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: bonificacion_puntos
CREATE TABLE `bonificacion_puntos` (
  `id_bonificacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `monto_compra` decimal(10,2) NOT NULL,
  `puntos_acreditados` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_bonificacion`),
  FOREIGN KEY (`id_cliente`) REFERENCES `cliente`(`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
