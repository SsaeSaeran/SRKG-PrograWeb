<?php
    // Mostrar errores mientras se desarrolla el proyecto
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Datos para entrar a MySQL
    $host    = '127.0.0.1';
    $puerto  = '3306';
    $dbname  = 'biblioteca_personal';
    $usuario = 'biblioteca';
    $clave   = 'Biblioteca#2026';

    try {
        // Se crea la conexión con PDO. El charset utf8mb4 sirve para los acentos y las tildes
        $pdo = new PDO(
            "mysql:host=$host;port=$puerto;dbname=$dbname;charset=utf8mb4",
            $usuario,
            $clave
        );

        // Si una consulta sale mal, PDO lanza una excepción en lugar de fallar en silencio
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Los resultados se piden como arreglo asociativo: $fila['titulo'], $fila['autor']...
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Si no se puede conectar, se muestra el error y se detiene el programa
        die("Error de conexión: " . $e->getMessage());
    }
?>
