-- =====================================================================
-- Autopartes & Repuestos "Don Pancho" - Base de datos
-- Importar desde phpMyAdmin (pestaña Importar) o con:
--   mysql -u root < database.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS repuestos_don_pancho
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE repuestos_don_pancho;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS detalle_factura;
DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS repuestos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS sucursales;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. ROLES
-- ---------------------------------------------------------------------
CREATE TABLE roles (
    id_rol  INT AUTO_INCREMENT PRIMARY KEY,
    nombre  VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. USUARIOS (contraseñas guardadas con password_hash de PHP)
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id_usuario  INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    email     VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    id_rol      INT NOT NULL,
    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. CATEGORIAS
-- ---------------------------------------------------------------------
CREATE TABLE categorias (
    id_categoria  INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. REPUESTOS
--    fotografia: solo el NOMBRE del archivo guardado en uploads/
--    destacado : 1 = aparece en la vitrina de la página de inicio
-- ---------------------------------------------------------------------
CREATE TABLE repuestos (
    id_repuesto      INT AUTO_INCREMENT PRIMARY KEY,
    codigo           VARCHAR(20)   NOT NULL UNIQUE,
    nombre           VARCHAR(120)  NOT NULL,
    especificaciones TEXT          NOT NULL,
    precio           DECIMAL(10,2) NOT NULL,
    stock            INT           NOT NULL DEFAULT 0,
    fotografia       VARCHAR(255)  NULL,
    destacado        TINYINT(1)    NOT NULL DEFAULT 0,
    id_categoria     INT           NOT NULL,
    CONSTRAINT fk_repuestos_categorias
        FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. SUCURSALES
-- ---------------------------------------------------------------------
CREATE TABLE sucursales (
    id_sucursal  INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(80)  NOT NULL,
    ciudad       VARCHAR(60)  NOT NULL,
    direccion    VARCHAR(200) NOT NULL,
    telefono     VARCHAR(20)  NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. FACTURAS (el correlativo se guarda en numero_factura)
-- ---------------------------------------------------------------------
CREATE TABLE facturas (
    id_factura            INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura        VARCHAR(20)   NOT NULL UNIQUE,
    fecha                 DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cliente_nombre        VARCHAR(100)  NOT NULL,
    rtn                   VARCHAR(14)   NOT NULL,
    telefono              VARCHAR(8)    NOT NULL,
    modalidad_flete       VARCHAR(30)   NOT NULL,
    costo_flete           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    subtotal              DECIMAL(10,2) NOT NULL,
    porcentaje_descuento  DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
    monto_descuento       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    isv                   DECIMAL(10,2) NOT NULL,
    total                 DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. DETALLE DE FACTURA
--    descripcion guarda el nombre del repuesto al momento de la compra,
--    así la factura no se pierde si luego se elimina el repuesto.
-- ---------------------------------------------------------------------
CREATE TABLE detalle_factura (
    id_detalle       INT AUTO_INCREMENT PRIMARY KEY,
    id_factura       INT           NOT NULL,
    id_repuesto      INT           NULL,
    descripcion      VARCHAR(120)  NOT NULL,
    cantidad         DECIMAL(10,2) NOT NULL,
    precio_unitario  DECIMAL(10,2) NOT NULL,
    subtotal         DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_facturas
        FOREIGN KEY (id_factura) REFERENCES facturas(id_factura)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_repuestos
        FOREIGN KEY (id_repuesto) REFERENCES repuestos(id_repuesto)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;


-- =====================================================================
-- DATOS
-- =====================================================================

-- Roles
INSERT INTO roles (nombre) VALUES ('Admin'), ('Bodega');

-- Usuarios
--   admin  / Admin123
--   bodega / Bodega123
INSERT INTO usuarios (nombre, email, password, id_rol) VALUES
('Administrador Don Pancho', 'admin@donpancho.hn',
 '$2y$10$tYtNb3V/lBEGbOfFK.sxlulD9AcellDG2x9KJaCWgbk.iuOrgBPBi', 1),
('Encargado de Bodega', 'bodega@donpancho.hn',
 '$2y$10$1n2s0TDJT9NEha2lEsea9.N/E8uTrP9/zULcVpyrKwdwoQdHfyjyu', 2);

-- Categorías
INSERT INTO categorias (nombre) VALUES
('Frenos y Suspensión'),
('Motor y Transmisión'),
('Sistema Eléctrico'),
('Fluidos y Lubricantes');

-- Repuestos (34 en total; los 5 destacados llevan destacado = 1)
INSERT INTO repuestos
    (codigo, nombre, especificaciones, precio, stock, fotografia, destacado, id_categoria)
VALUES
-- Frenos y Suspensión (1)
('FRE-001', 'Pastillas de freno delanteras Toyota Corolla',
 'Compuesto semimetálico. Compatible: Toyota Corolla 2009-2019. Juego de 4 piezas.',
 850.00, 40, 'pastillas-freno-corolla.jpg', 1, 1),
('FRE-002', 'Discos de freno ventilados Honda Civic (par)',
 'Acero ventilado de 262 mm. Compatible: Honda Civic 2006-2015.',
 1450.00, 15, 'discos-freno-civic.jpg', 0, 1),
('SUS-001', 'Amortiguador delantero Nissan Sentra',
 'Gas presurizado. Compatible: Nissan Sentra 2013-2019. Precio por unidad.',
 1200.00, 20, 'amortiguador-sentra.jpg', 0, 1),
('SUS-002', 'Rótula inferior Hyundai Accent',
 'Acero forjado con funda de goma. Compatible: Hyundai Accent 2012-2018.',
 480.00, 30, 'rotula-accent.jpg', 0, 1),

-- Motor y Transmisión (2)
('MOT-001', 'Kit de banda de distribución Isuzu D-Max',
 'Incluye banda, tensor y polea guía. Compatible: Isuzu D-Max 3.0 diésel.',
 2350.00, 10, 'kit-distribucion-dmax.jpg', 1, 2),
('MOT-002', 'Bomba de agua Toyota Hilux',
 'Carcasa de aluminio con rodamiento sellado. Compatible: Toyota Hilux 2.7L.',
 1100.00, 12, 'bomba-agua-hilux.jpg', 0, 2),
('TRA-001', 'Kit de clutch Ford Ranger',
 'Disco, plato y collarín. Compatible: Ford Ranger 2.2L y 3.2L.',
 3900.00, 6, 'kit-clutch-ranger.jpg', 0, 2),
('MOT-003', 'Empaque de culata Nissan Frontier',
 'Multicapa de acero. Compatible: Nissan Frontier 2.5L diésel.',
 780.00, 18, 'empaque-culata-frontier.jpg', 0, 2),

-- Sistema Eléctrico (3)
('ELE-001', 'Batería 12V 650 CCA',
 'Libre de mantenimiento, 12 voltios, 650 amperios de arranque en frío.',
 2800.00, 14, 'bateria-12v.jpg', 1, 3),
('ELE-002', 'Alternador Honda Accord',
 '12V 90A. Compatible: Honda Accord 2008-2012.',
 4200.00, 5, 'alternador-accord.jpg', 0, 3),
('ELE-003', 'Bujías de iridio (juego de 4)',
 'Electrodo de iridio, larga duración. Compatible con la mayoría de motores de 4 cilindros japoneses y coreanos.',
 1050.00, 25, 'bujias-iridio.jpg', 1, 3),
('ELE-004', 'Motor de arranque Toyota Corolla',
 '12V 1.0 kW. Compatible: Toyota Corolla 2009-2019.',
 3100.00, 7, 'arranque-corolla.jpg', 0, 3),

-- Fluidos y Lubricantes (4)
('FLU-001', 'Aceite 5W-30 sintético (galón)',
 'Aceite sintético 5W-30 para motores a gasolina. Presentación de 1 galón.',
 750.00, 60, 'aceite-5w30.jpg', 1, 4),
('FLU-002', 'Líquido de frenos DOT 4 (500 ml)',
 'Líquido de frenos sintético DOT 4. Presentación de 500 ml.',
 160.00, 50, 'liquido-frenos-dot4.jpg', 0, 4),
('FLU-003', 'Refrigerante concentrado (galón)',
 'Refrigerante concentrado para todo tipo de motor. Presentación de 1 galón.',
 320.00, 45, 'refrigerante-galon.jpg', 0, 4),
('FLU-004', 'Aceite 10W-40 semisintético (galón)',
 'Aceite semisintético 10W-40 para gasolina y diésel liviano. Presentación de 1 galón.',
 620.00, 55, 'aceite-10w40.jpg', 0, 4),

-- Aceites por marca y viscosidad (categoría 4: Fluidos y Lubricantes)
('ACE-CAS-5W30',  'Aceite Castrol 5W-30 sintético (galón)',        'Aceite sintético 5W-30 para gasolina. Presentación de 1 galón.',  680.00, 30, 'aceite-castrol-5w30.jpg', 0, 4),
('ACE-CAS-10W40', 'Aceite Castrol 10W-40 semisintético (galón)',   'Aceite semisintético 10W-40. Presentación de 1 galón.',           560.00, 30, 'aceite-castrol-10w40.jpg', 0, 4),
('ACE-CAS-15W40', 'Aceite Castrol 15W-40 diésel (galón)',          'Aceite 15W-40 para motores diésel. Presentación de 1 galón.',     520.00, 30, 'aceite-castrol-15w40.jpg', 0, 4),
('ACE-MOB-5W30',  'Aceite Mobil 5W-30 sintético (galón)',          'Aceite sintético 5W-30 para gasolina. Presentación de 1 galón.',  700.00, 30, 'aceite-mobil-5w30.jpg', 0, 4),
('ACE-MOB-10W40', 'Aceite Mobil 10W-40 semisintético (galón)',     'Aceite semisintético 10W-40. Presentación de 1 galón.',           575.00, 30, 'aceite-mobil-10w40.jpg', 0, 4),
('ACE-MOB-15W40', 'Aceite Mobil 15W-40 diésel (galón)',            'Aceite 15W-40 para motores diésel. Presentación de 1 galón.',     530.00, 30, 'aceite-mobil-15w40.jpg', 0, 4),
('ACE-SHE-5W30',  'Aceite Shell 5W-30 sintético (galón)',          'Aceite sintético 5W-30 para gasolina. Presentación de 1 galón.',  650.00, 30, 'aceite-shell-5w30.jpg', 0, 4),
('ACE-SHE-10W40', 'Aceite Shell 10W-40 semisintético (galón)',     'Aceite semisintético 10W-40. Presentación de 1 galón.',           540.00, 30, 'aceite-shell-10w40.jpg', 0, 4),
('ACE-SHE-15W40', 'Aceite Shell 15W-40 diésel (galón)',            'Aceite 15W-40 para motores diésel. Presentación de 1 galón.',     500.00, 30, 'aceite-shell-15w40.jpg', 0, 4),
('ACE-VAL-5W30',  'Aceite Valvoline 5W-30 sintético (galón)',      'Aceite sintético 5W-30 para gasolina. Presentación de 1 galón.',  640.00, 30, 'aceite-valvoline-5w30.jpg', 0, 4),
('ACE-VAL-10W40', 'Aceite Valvoline 10W-40 semisintético (galón)', 'Aceite semisintético 10W-40. Presentación de 1 galón.',           530.00, 30, 'aceite-valvoline-10w40.jpg', 0, 4),
('ACE-VAL-15W40', 'Aceite Valvoline 15W-40 diésel (galón)',        'Aceite 15W-40 para motores diésel. Presentación de 1 galón.',     495.00, 30, 'aceite-valvoline-15w40.jpg', 0, 4),

-- Filtros (categoría 2: Motor y Transmisión)
('FIL-001', 'Filtro de aceite',       'Filtro de aceite de alta retención para motores a gasolina y diésel.', 220.00, 40, 'filtro-aceite.jpg', 0, 2),
('FIL-002', 'Filtro de aire',         'Filtro de aire de motor, elemento de papel plisado.',                  380.00, 40, 'filtro-aire.jpg', 0, 2),
('FIL-003', 'Filtro de combustible',  'Filtro de combustible en línea.',                                      340.00, 40, 'filtro-combustible.jpg', 0, 2),
('FIL-004', 'Filtro de cabina (A/C)', 'Filtro de cabina con carbón activado para el aire acondicionado.',     290.00, 40, 'filtro-cabina.jpg', 0, 2),

-- Adicionales que faltaban
('FRE-003', 'Pastillas cerámicas',     'Pastillas de freno cerámicas de bajo ruido y poco polvo.',          950.00, 20, 'pastillas-ceramicas.jpg', 0, 1),
('FLU-005', 'Limpiador de inyectores', 'Aditivo limpiador de inyectores para gasolina. Frasco de 355 ml.',  260.00, 35, 'limpiador-inyectores.jpg', 0, 4);

-- Sucursales (direcciones y teléfonos de ejemplo: ajustar si se dispone de los reales)
INSERT INTO sucursales (nombre, ciudad, direccion, telefono) VALUES
('Sucursal Toncontín', 'Tegucigalpa',  'Zona Toncontín, Tegucigalpa, Francisco Morazán', '2222-0001'),
('Sucursal Suyapa',    'Tegucigalpa',  'Colonia Suyapa, Tegucigalpa, Francisco Morazán', '2222-0002'),
('Sucursal Medina',    'San Pedro Sula', 'Sector Medina, San Pedro Sula, Cortés',        '2550-0003'),
('Sucursal CA-13',     'La Ceiba',     'Carretera CA-13, La Ceiba, Atlántida',           '2440-0004');

-- Facturas de ejemplo (para el historial de compras)
-- Regla usada: 3 a 9 piezas = 5% | 10 o más = 10% | ISV 15% sobre (subtotal - descuento)
INSERT INTO facturas
    (numero_factura, fecha, cliente_nombre, rtn, telefono, modalidad_flete,
     costo_flete, subtotal, porcentaje_descuento, monto_descuento, isv, total)
VALUES
('000-001-01-00000001', '2026-10-20 10:15:00', 'Taller Mecánico El Rápido',
 '08011999123456', '99887766', 'Retiro en mostrador',
 0.00, 1600.00, 0.00, 0.00, 240.00, 1840.00),
('000-001-01-00000002', '2026-10-22 14:30:00', 'Juan Carlos Mejía',
 '05011985654321', '98765432', 'Moto express',
 50.00, 3300.00, 5.00, 165.00, 470.25, 3655.25),
('000-001-01-00000003', '2026-10-25 09:00:00', 'Taller Hermanos Reyes',
 '08011990111222', '33445566', 'Flete en camioncito',
 180.00, 6200.00, 10.00, 620.00, 837.00, 6597.00);

INSERT INTO detalle_factura
    (id_factura, id_repuesto, descripcion, cantidad, precio_unitario, subtotal)
VALUES
-- Factura 1 (2 piezas)
(1, 1,  'Pastillas de freno delanteras Toyota Corolla', 1,  850.00,  850.00),
(1, 13, 'Aceite 5W-30 sintético (galón)',               1,  750.00,  750.00),
-- Factura 2 (3 piezas)
(2, 3,  'Amortiguador delantero Nissan Sentra',         1, 1200.00, 1200.00),
(2, 11, 'Bujías de iridio (juego de 4)',                2, 1050.00, 2100.00),
-- Factura 3 (10 piezas)
(3, 16, 'Aceite 10W-40 semisintético (galón)',         10,  620.00, 6200.00);