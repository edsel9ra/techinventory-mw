CREATE TABLE tbl_tipos_equipos (
    tipo_equipo_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_equipo VARCHAR(50)
);

CREATE TABLE tbl_sedes (
    sede_id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_sede CHAR(3) NOT NULL,
    nombre_sede VARCHAR(50) NOT NULL,
    estado TINYINT(1) DEFAULT 1
);

CREATE TABLE tbl_roles (
    rol_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50),
    descripcion_rol VARCHAR(200)
);

CREATE TABLE tbl_usuarios (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usr VARCHAR(100) NOT NULL,
    cargo_usr VARCHAR(100) NOT NULL,
    correo_usr VARCHAR(100) NOT NULL,
    passwd_usr VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (rol_id) REFERENCES tbl_roles(rol_id)
);

CREATE TABLE tbl_equipos (
    equipo_id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT,
    tipo_equipo_id INT,
    cod_equipo VARCHAR(20) UNIQUE,
    marca_equipo VARCHAR(100),
    modelo_equipo VARCHAR(100),
    serial_equipo VARCHAR(250),
    estado ENUM('Activo', 'Inactivo', 'Baja'), /*Activo, Inactivo, Dado de Baja*/
    detalle_equipo_id INT,
    responsable VARCHAR(100),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_baja DATETIME DEFAULT NULL,
    FOREIGN KEY (sede_id) REFERENCES tbl_sedes(sede_id),
    FOREIGN KEY (tipo_equipo_id) REFERENCES tbl_tipos_equipos(tipo_equipo_id)
);

CREATE TABLE tbl_equipo_imagenes (
    imagen_id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    ruta_imagen VARCHAR(255),
    descripcion VARCHAR(255),
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES tbl_equipos(equipo_id) ON DELETE CASCADE
);

CREATE TABLE tbl_monitores (
    monitor_id INT AUTO_INCREMENT PRIMARY KEY,
    tamanio_pulgadas DECIMAL(4,1),
    asignado TINYINT(1) DEFAULT 0,
    nombre_equipo_asignado VARCHAR(50)
);

CREATE TABLE tbl_computadores (
    computador_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_pc VARCHAR(50),
    tipo_computador ENUM('Desktop', 'Laptop', 'AIO', 'HioScreen') NOT NULL,
    procesador VARCHAR(100),
    ram VARCHAR(50),
    disco VARCHAR(50),
    capacidad_disco INT NULL,
    os VARCHAR(50),
    licencia_microsoft TINYINT(1) DEFAULT 0,
    tiene_monitor TINYINT(1) NULL DEFAULT 0,
    monitor_id INT NULL,
    tipo_cargador VARCHAR(50)
);

CREATE TABLE tbl_impresoras (
    impresora_id INT AUTO_INCREMENT PRIMARY KEY,
    tecnologia VARCHAR(50),
    conexion VARCHAR(50)
);

CREATE TABLE tbl_tablets (
    tablet_id INT AUTO_INCREMENT PRIMARY KEY,
    procesador VARCHAR(100),
    ram VARCHAR(50),
    rom INT NULL,
    os VARCHAR(50),
    version_os VARCHAR(50)
);

CREATE TABLE tbl_mantenimientos (
    mantenimiento_id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    tipo ENUM('Preventivo', 'Correctivo') NOT NULL,
    fecha_realizado DATE NOT NULL,
    tecnico VARCHAR(100),
    descripcion TEXT,
    acciones_realizadas TEXT,
    observaciones TEXT,
    revisado_por VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES tbl_equipos(equipo_id)
);

CREATE TABLE tbl_eventos_calendario (
    evento_id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME DEFAULT NULL,
    all_day TINYINT(1) DEFAULT 1,
    color VARCHAR(20) DEFAULT '#3788d8',
    
    sede_id INT DEFAULT NULL,
    activo TINYINT(1) DEFAULT 1,

    creado_por VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (sede_id) REFERENCES tbl_sedes(sede_id)
);