CREATE DATABASE IF NOT EXISTS biblioteca_personal
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Se crea el usuario para las dos formas de entrar: localhost y 127.0.0.1
CREATE USER IF NOT EXISTS 'biblioteca'@'localhost'
    IDENTIFIED BY 'Biblioteca#2026';

CREATE USER IF NOT EXISTS 'biblioteca'@'127.0.0.1'
    IDENTIFIED BY 'Biblioteca#2026';

GRANT ALL PRIVILEGES ON biblioteca_personal.* TO 'biblioteca'@'localhost';
GRANT ALL PRIVILEGES ON biblioteca_personal.* TO 'biblioteca'@'127.0.0.1';

-- Si el usuario ya existía con otra contraseña, se deja con la que espera la aplicación
ALTER USER 'biblioteca'@'localhost' IDENTIFIED BY 'Biblioteca#2026';
ALTER USER 'biblioteca'@'127.0.0.1' IDENTIFIED BY 'Biblioteca#2026';

FLUSH PRIVILEGES;