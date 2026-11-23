-- ============================================================
--  Mi Biblioteca · Proyecto Integrador 2026B
--  Base de datos: biblioteca_personal
-- ============================================================

CREATE DATABASE IF NOT EXISTS biblioteca_personal
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE biblioteca_personal;

-- Se borran las tablas para poder correr el script varias veces.
-- Primero libros, porque tiene la llave foranea hacia usuarios.
DROP TABLE IF EXISTS libros;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

-- ------------------------------------------------------------
-- Tabla roles
-- Guarda los dos tipos de usuario que maneja el sistema.
-- ------------------------------------------------------------
CREATE TABLE roles (
    id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
) ENGINE = InnoDB;

INSERT INTO roles (id, nombre) VALUES
(1, 'Administrador'),
(2, 'Usuario');

-- ------------------------------------------------------------
-- Tabla usuarios
-- rol_id apunta a la tabla roles: 1 = Administrador, 2 = Usuario.
-- En password se guarda el hash, nunca la contraseña escrita.
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    correo     VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    rol_id     INT UNSIGNED NOT NULL DEFAULT 2,
    activo     TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id) REFERENCES roles (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Tabla libros
-- id_usuario es la llave foranea: cada libro pertenece a un usuario.
-- activo = 0 significa "deshabilitado" (borrado logico).
-- ------------------------------------------------------------
CREATE TABLE libros (
    id_libro         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario       INT UNSIGNED NOT NULL,
    titulo           VARCHAR(255) NOT NULL,
    autor            VARCHAR(200) DEFAULT NULL,
    editorial        VARCHAR(150) DEFAULT NULL,
    anio_publicacion SMALLINT UNSIGNED DEFAULT NULL,
    numero_paginas   SMALLINT UNSIGNED DEFAULT NULL,
    genero           VARCHAR(150) DEFAULT NULL,
    descripcion      TEXT         DEFAULT NULL,
    imagen           LONGBLOB     DEFAULT NULL,
    imagen_tipo      VARCHAR(100) DEFAULT NULL,
    tipo             VARCHAR(50)  NOT NULL DEFAULT 'Libro',
    idioma           VARCHAR(50)  DEFAULT NULL,
    encuadernacion   VARCHAR(40)  NOT NULL DEFAULT 'Pasta blanda',
    estado           ENUM('Pendiente', 'Leyendo', 'Terminado') NOT NULL DEFAULT 'Pendiente',
    activo           TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_libros_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_libros_usuario (id_usuario),
    INDEX idx_libros_titulo (titulo)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Cuentas de demostración
-- admin@biblioteca.mx  / Admin1234    (rol 1)
-- lector@biblioteca.mx / Lector1234   (rol 2)
-- sae@gmail.com        / Saeran1234   (rol 1)
-- ------------------------------------------------------------
INSERT INTO usuarios (nombre, correo, password, rol_id) VALUES
('Administrador', 'admin@biblioteca.mx',
 '$2y$10$KmlvnlPTdTeO/VI2EmCCrOIs2dikdkpad1ZBj5vj9IvITOYahEabC', 1),
('Lector de prueba', 'lector@biblioteca.mx',
 '$2y$10$/PqG9kXvFETBwRunVEFRA.8yHc0OoTT7eLe5fkNwpT54Gaa/O1w0K', 2),
('Sae', 'sae@gmail.com',
 '$2y$10$z79OQkhLT0XapWKW.sWFZOm1dS1Rz1BYenA4o7WI.40jryhgXke0m', 1);

SET @admin  = (SELECT id_usuario FROM usuarios WHERE correo = 'admin@biblioteca.mx');
SET @lector = (SELECT id_usuario FROM usuarios WHERE correo = 'lector@biblioteca.mx');
SET @sae    = (SELECT id_usuario FROM usuarios WHERE correo = 'sae@gmail.com');

-- ------------------------------------------------------------
-- Libros del administrador (2)
-- ------------------------------------------------------------
INSERT INTO libros (id_usuario, titulo, autor, editorial, anio_publicacion, numero_paginas, genero, tipo, idioma, encuadernacion, estado) VALUES
(@admin, 'Roverismo hacia el éxito', 'Robert Baden-Powell', 'Editorial Escultismo', NULL, 256, 'Escultismo / Desarrollo Personal', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@admin, 'Obtención y empleo de medios de enseñanza económicos y gratuitos (Manual UTEHA Breve N° 381)', 'Robert L. Schain / Murray Polner', 'UTEHA', 1968, 134, 'Pedagogía / Educación', 'Científico / Educativo', 'Español', 'Pasta blanda', 'Pendiente');

-- ------------------------------------------------------------
-- Libros del usuario de prueba (2)
-- ------------------------------------------------------------
INSERT INTO libros (id_usuario, titulo, autor, editorial, anio_publicacion, numero_paginas, genero, tipo, idioma, encuadernacion, estado) VALUES
(@lector, 'La frontera', 'Patrick Bard', 'Grijalbo', 2005, 368, 'Novela Negra / Detectivesco / Thriller', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@lector, 'Inversión extranjera directa y empresas japonesas en México: Implicaciones regionales económicas y legales', 'Varios Autores', 'Universidad de Guadalajara', NULL, 260, 'Economía / Derecho / Académico', 'Científico / Educativo', 'Español', 'Pasta blanda', 'Pendiente');

-- ------------------------------------------------------------
-- El resto de mi colección de libros
-- ------------------------------------------------------------
INSERT INTO libros (id_usuario, titulo, autor, editorial, anio_publicacion, numero_paginas, genero, tipo, idioma, encuadernacion, estado) VALUES
(@sae, 'Shin''yaku Märchen Vol. 1 (新約Marchen 1)', 'Sound Horizon / Soga Shiina', 'Kodansha (SKC)', 2015, 210, 'Fantasía Oscura', 'Manga', 'Japonés', 'Pasta blanda', 'Pendiente'),
(@sae, 'I''m the Villainess, So I''m Taming the Final Boss Vol. 1 (Manga)', 'Sarasa Nagase / Anko Yuzu', 'Yen Press', 2021, 178, 'Romance / Fantasía', 'Manga', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'I''m the Villainess, So I''m Taming the Final Boss Vol. 1 (Novela)', 'Sarasa Nagase / Mai Murasaki', 'Yen On', 2021, 216, 'Romance / Fantasía', 'Novela Ligera', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'El Psicoanalista', 'John Katzenbach', 'Ediciones B', NULL, 528, 'Thriller Psicológico / Misterio', 'Libro', 'Español', 'Pasta blanda', 'Terminado'),
(@sae, 'Welcome to the Peculiar Antique Shop: The Art of Makura Kurama (不思議なアンティークショップ)', 'Makura Kurama', 'PIE International', 2022, 160, 'Ilustración / Fantasía', 'Artbook', 'Japonés / Inglés', 'Pasta blanda', 'Terminado'),
(@sae, 'Presentimientos', 'Clara Sánchez', 'Booket', NULL, 384, 'Misterio / Drama Psicológico', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Frankenstein: The 1818 Text', 'Mary Shelley', 'Penguin Classics', 2018, 288, 'Gótico / Ciencia Ficción', 'Libro', 'Inglés', 'Pasta blanda', 'Terminado'),
(@sae, 'Crimen y castigo', 'Fiódor M. Dostoievski', 'Editorial Porrúa', NULL, 408, 'Psicológico / Filosófico', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Firefox', 'Craig Thomas', NULL, NULL, 318, 'Espionaje / Thriller', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Memorias de un amigo imaginario', 'Matthew Dicks', 'Nube de Tinta', NULL, 432, 'Drama / Contemporáneo', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Cuentos completos I', 'Fiódor M. Dostoievski', 'Fondo de Cultura Económica', NULL, 526, 'Ficción Clásica / Drama', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Narraciones Extraordinarias', 'Edgar Allan Poe', NULL, NULL, 256, 'Terror / Detectivesco / Misterio', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, '1984', 'George Orwell', 'Debolsillo', NULL, 352, 'Ciencia Ficción / Distopía', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Japanese Tales of Mystery and Imagination', 'Edogawa Rampo', 'Tuttle Publishing', NULL, 224, 'Detectivesco / Misterio / Horror', 'Libro', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'No Longer Human (Adaptación)', 'Junji Ito / Osamu Dazai', 'VIZ Media', 2019, 616, 'Horror Psicológico', 'Manga', 'Inglés', 'Pasta dura', 'Terminado'),
(@sae, 'Cómo ganar amigos e influir sobre las personas', 'Dale Carnegie', 'Debolsillo', NULL, 304, 'Desarrollo Personal', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'The Setting Sun', 'Osamu Dazai', 'New Directions (NDP258)', NULL, 192, 'Ficción Existencial / Drama', 'Libro', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'No Longer Human', 'Osamu Dazai', 'New Directions (NDP357)', NULL, 176, 'Ficción Existencial / Drama', 'Libro', 'Inglés', 'Pasta blanda', 'Terminado'),
(@sae, 'Frieren: Más allá del fin del viaje Vol. 1 (Edición Especial con Fanbook)', 'Kanehito Yamada / Tsukasa Abe', 'Panini Manga', NULL, 320, 'Fantasía / Aventura', 'Manga', 'Español', 'Pasta blanda', 'Terminado'),
(@sae, 'Perspective Made Easy', 'Ernest R. Norling', 'Dover Publications', 1999, 224, 'Arte / Dibujo Técnico', 'Libro de Arte', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'Temas Selectos de Formación Directiva', 'Elia Marum, J. Villaseñor, C. Rodríguez (Coords.)', 'Universidad de Guadalajara', NULL, 240, 'Administración / Académico', 'Científico / Educativo', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'The Human Machine', 'George B. Bridgman', 'Dover Publications', NULL, 144, 'Arte / Anatomía', 'Libro de Arte', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'Científicos en el ring', 'Juan Nepote', 'Consejo Estatal para la Cultura y las Artes (CQL)', NULL, 160, 'Divulgación Científica / Historia', 'Científico / Educativo', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'El último lobo', 'David Martín del Campo', 'Alfaguara Juvenil', NULL, 168, 'Aventura / Juvenil', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Agustín Yáñez para jóvenes. Infancia y adolescencia', 'Agustín Yáñez', NULL, NULL, 128, 'Biográfico / Narrativa', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Animales fantásticos y dónde encontrarlos', 'Newt Scamander (J.K. Rowling)', 'Salamandra', NULL, 128, 'Fantasía', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Quidditch a través de los tiempos', 'Kennilworthy Whisp (J.K. Rowling)', 'Salamandra', NULL, 128, 'Fantasía', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Alicia en el país de las maravillas', 'Lewis Carroll', 'Editores Mexicanos Unidos (EMU)', NULL, 144, 'Fantasía Clásica', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Mi lucha', 'Adolf Hitler', 'Editores Mexicanos Unidos (EMU)', NULL, 382, 'Historia / Política', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'El jinete sin cabeza y otros cuentos', 'Washington Irving', 'Editores Mexicanos Unidos (EMU)', NULL, 160, 'Gótico / Misterio', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'El Arte de la Guerra', 'Sun Tzu', 'Editores Mexicanos Unidos (EMU)', NULL, 160, 'Filosofía / Estrategia', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Cuentos de las mil y una noches', 'Anónimo', 'Editores Mexicanos Unidos (EMU)', NULL, 192, 'Folclore / Fantasía', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'El extraño caso del Dr. Jekyll y Mr. Hyde', 'Robert L. Stevenson', NULL, NULL, 128, 'Misterio / Gótico', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Las batallas en el desierto', 'José Emilio Pacheco', 'Ediciones Era', NULL, 68, 'Romance / Drama Histórico', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Introducción histórica a la Revolución Mexicana', NULL, NULL, NULL, 120, 'Historia', 'Científico / Educativo', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Los artistas de huesos', 'Madeleine Roux', 'V&R Editoras', 2016, 336, 'Terror / Misterio Gótico', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Praga 56', 'Gabriela de Fuentes', NULL, NULL, 210, 'Drama / Romance', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'El Gran Gatsby', 'F. Scott Fitzgerald', 'Editores Mexicanos Unidos (EMU)', NULL, 192, 'Drama / Clásico', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Cuentos de Edgar Allan Poe', 'Edgar Allan Poe', 'Alfaguara', NULL, 280, 'Terror / Detectivesco / Misterio', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'The Poems of Nakahara Chūya', 'Nakahara Chūya (Trad. Mackintosh & Sugiyama)', 'Gracewing', NULL, 140, 'Poesía / Literatura Japonesa', 'Poesía', 'Inglés / Japonés', 'Pasta blanda', 'Pendiente'),
(@sae, 'Barrio Santa Tere', 'Marco Aurelio Larios', NULL, NULL, 160, 'Crónica / Narrativa Urbana', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Hadas: Criaturas fantásticas', 'John Malam', NULL, NULL, 32, 'Fantasía / Mitología', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Gigantes: Criaturas fantásticas', 'John Malam', NULL, NULL, 32, 'Fantasía / Mitología', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Eternos', 'Kirsten Miller', NULL, NULL, 384, 'Fantasía / Romance Juvenil', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Odín, Thor, Loki, Frigg, Heimdallr y las Valquirias: Edda Poética, Edda Prosaica y Cuentos Nórdicos', 'Jorge Rodríguez Galicia (Comp. y Trad.)', 'Colección Fractales (EMU)', NULL, 384, 'Mitología Nórdica', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'The Complete Fiction of H. P. Lovecraft', 'H. P. Lovecraft', 'Chartwell Books', 2016, 1112, 'Horror Cósmico / Terror', 'Libro', 'Inglés', 'Pasta dura', 'Pendiente'),
(@sae, 'Rohan at the Louvre', 'Hirohiko Araki', 'NBM Publishing', 2012, 128, 'Misterio / Sobrenatural', 'Manga', 'Inglés', 'Pasta dura', 'Pendiente'),
(@sae, 'Conducta Animal (Colección de la Naturaleza)', 'Niko Tinbergen', 'Time-Life', NULL, 200, 'Biología / Zoología', 'Científico / Educativo', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Las Plantas (Colección de la Naturaleza)', 'Frits W. Went', 'Time-Life', NULL, 194, 'Botánica / Biología', 'Científico / Educativo', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Máquinas (Colección Científica)', 'Robert O''Brien', 'Time-Life', NULL, 200, 'Ingeniería / Mecánica', 'Científico / Educativo', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Taller de las Artes: Pintura-I (Enciclopedia Salvat Vol. 7)', 'Varios Autores', 'Salvat', NULL, 300, 'Arte / Pintura', 'Libro de Arte', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Black Butler Artworks: The Framian (黒執事 原画集 -The Framian-)', 'Yana Toboso', 'Square Enix', NULL, 130, 'Ilustración / Fantasía Oscura', 'Artbook', 'Japonés', 'Pasta dura', 'Pendiente'),
(@sae, 'Cuatro cuentos de Grimm', 'Hermanos Grimm', 'CONAFE', NULL, 48, 'Cuento Clásico / Infantil', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Disney Twisted-Wonderland Official Fan Book (ディズニーツイステッドワンダーランド FAN BOOK)', 'Disney / Square Enix', 'Kodansha', 2020, 144, 'Fantasía / Videojuegos', 'Fanbook', 'Japonés', 'Pasta blanda', 'Pendiente'),
(@sae, 'The Legend of Zelda: The Ultimate Fan Guide (40 Years Anniversary Special Issue)', 'Varios Autores', 'McClatchy Entertainment / A360 Media', 2026, 96, 'Videojuegos / Guía', 'Revista', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'Nietzsche I: El nacimiento de la tragedia / El caminante y su sombra / La ciencia jovial', 'Friedrich Nietzsche (Estudio introd. Germán Cano)', 'Editorial Gredos', NULL, 688, 'Filosofía', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'El diario de Ana Frank', 'Ana Frank', 'Editores Mexicanos Unidos (EMU)', NULL, 256, 'Biográfico / Historia', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'El mundo de Sofía: Novela sobre la historia de la filosofía', 'Jostein Gaarder', 'Patria / Siruela', NULL, 638, 'Filosofía / Ficción', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, '4 en 1 Saint Germain', 'Conde de Saint Germain', NULL, NULL, 448, 'Esoterismo / Metafísica', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'The Cruelty', 'Scott Bergstrom', NULL, 2017, 432, 'Thriller / Acción Juvenil', 'Libro', 'Inglés', 'Pasta blanda', 'Pendiente'),
(@sae, 'The Girl from the Other Side: Siúil, a Rún - Deluxe Edition I', 'Nagabe', 'Seven Seas Entertainment', 2022, 542, 'Fantasía Oscura / Misterio', 'Manga', 'Inglés', 'Pasta dura', 'Pendiente'),
(@sae, 'The Girl from the Other Side: Siúil, a Rún - Deluxe Edition II', 'Nagabe', 'Seven Seas Entertainment', 2022, 540, 'Fantasía Oscura / Misterio', 'Manga', 'Inglés', 'Pasta dura', 'Pendiente'),
(@sae, 'The Girl from the Other Side: Siúil, a Rún - Deluxe Edition III', 'Nagabe', 'Seven Seas Entertainment', 2023, 538, 'Fantasía Oscura / Misterio', 'Manga', 'Inglés', 'Pasta dura', 'Pendiente'),
(@sae, 'El fantasma de la ópera', 'Gaston Leroux', NULL, NULL, 320, 'Gótico / Romance / Misterio', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Frankenstein o el moderno Prometeo', 'Mary Shelley (Trad. Gabriela Damián Miravete)', 'Gran Travesía (Océano)', NULL, 312, 'Gótico / Ciencia Ficción', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Un trato con el rey de los elfos', 'Elise Kova', 'Umbriel', 2022, 384, 'Fantasía / Romance', 'Libro', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Fantasía Gótica: Relatos Apocalípticos', 'Varios Autores', 'Editorial Alma', NULL, 320, 'Gótico / Ciencia Ficción / Apocalíptico', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Fantasía Gótica: Asesinatos Escalofriantes (Un recorrido literario por los crímenes más oscuros)', 'Varios Autores', 'Editorial Alma', NULL, 320, 'Gótico / Detectivesco / Misterio', 'Libro', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Yo, el gato (吾輩は猫である)', 'Natsume Sōseki', NULL, NULL, 480, 'Sátira / Ficción Clásica Japonesa', 'Libro', 'Español', 'Pasta dura', 'Terminado'),
(@sae, 'La naturaleza y sus maravillas Vol. 2', 'Varios Autores', 'Mas Ivars Editores, S.L.', NULL, 180, 'Naturaleza / Biología Animal', 'Científico / Educativo', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'La naturaleza y sus maravillas Vol. 3', 'Varios Autores', 'Mas Ivars Editores, S.L.', NULL, 180, 'Naturaleza / Biología Animal', 'Científico / Educativo', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'La naturaleza y sus maravillas Vol. 4', 'Varios Autores', 'Mas Ivars Editores, S.L.', NULL, 180, 'Naturaleza / Biología Animal', 'Científico / Educativo', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'La naturaleza y sus maravillas Vol. 5', 'Varios Autores', 'Mas Ivars Editores, S.L.', NULL, 180, 'Naturaleza / Biología Animal', 'Científico / Educativo', 'Español', 'Pasta dura', 'Pendiente'),
(@sae, 'Obey Me! The Comic Vol. 1 (おべいみー！ The Comic 1)', 'NTT Solmare / Subaru Nitou', 'KADOKAWA (B''s-LOG COMICS)', 2021, 162, 'Fantasía / Demonios / Comedia', 'Manga', 'Japonés', 'Pasta blanda', 'Pendiente'),
(@sae, 'As the Gods Will Vol. 2 (Kamisama no Iutoori)', 'Muneyuki Kaneshiro / Akeji Fujimura', 'Distrito Manga', 2023, 192, 'Supervivencia / Horror / Thriller', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'As the Gods Will Vol. 1 (Kamisama no Iutoori)', 'Muneyuki Kaneshiro / Akeji Fujimura', 'Distrito Manga', 2022, 192, 'Supervivencia / Horror / Thriller', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 15', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2022, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 14', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2022, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 13', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2022, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 12', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2022, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 11', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2022, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 10', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2021, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 9', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2021, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 8', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2021, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 7', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2021, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 6', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2021, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 5', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2021, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 4', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2021, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 3', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2020, 192, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 2', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2020, 208, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Tenkuu Shinpan (Invasión en las alturas) Vol. 1', 'Tsuina Miura / Takahiro Oba', 'Panini Manga', 2020, 208, 'Acción / Supervivencia / Horror', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Bungo Stray Dogs Vol. 22', 'Kafka Asagiri / Sango Harukawa', 'Panini Manga', 2023, 194, 'Acción / Misterio / Sobrenatural', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Bungo Stray Dogs Vol. 02', 'Kafka Asagiri / Sango Harukawa', 'Panini Manga', 2019, 194, 'Acción / Misterio / Sobrenatural', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Bungo Stray Dogs Vol. 01', 'Kafka Asagiri / Sango Harukawa', 'Panini Manga', 2019, 194, 'Acción / Misterio / Sobrenatural', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Vanitas no Carte (Los apuntes de Vanitas) Vol. 3', 'Jun Mochizuki', 'Panini Manga', 2022, 226, 'Fantasía Oscura / Steampunk / Vampiros', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Vanitas no Carte (Los apuntes de Vanitas) Vol. 1', 'Jun Mochizuki', 'Panini Manga', 2022, 242, 'Fantasía Oscura / Steampunk / Vampiros', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'The Killer Inside (Shinai naru Boku e Satsui wo Komete) Vol. 1', 'Hajime Inoryu / Shota Ito', 'Panini Manga', 2022, 192, 'Thriller Psicológico / Misterio / Seinen', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'The Killer Inside (Shinai naru Boku e Satsui wo Komete) Vol. 2', 'Hajime Inoryu / Shota Ito', 'Panini Manga', 2022, 192, 'Thriller Psicológico / Misterio / Seinen', 'Manga', 'Español', 'Pasta blanda', 'Pendiente'),
(@sae, 'Disney Twisted-Wonderland: The Comic - Episode of Heartslabyul Vol. 1', 'Wakana Hazuki / Sumire Kowono (Yana Toboso)', 'Square Enix (G Fantasy Comics)', 2021, 198, 'Fantasía / Magia / Escolar', 'Manga', 'Japonés', 'Pasta blanda', 'Pendiente'),
(@sae, 'Japanese From Zero! 1', 'George Trombley / Yukari Takenaka', 'YesJapan Corporation', NULL, 344, 'Idiomas / Aprendizaje de Japonés', 'Científico / Educativo', 'Inglés / Japonés', 'Pasta blanda', 'Leyendo');
