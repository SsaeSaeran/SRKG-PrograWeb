<?php
    // Datos de conexión
    $servidor = "localhost";
    $basedatos = "dummy";
    $usuario = "root";
    $contrasena = "Indignodeserhumano26.";

    try{
        $conexion = new PDO (
            "mysql:host=$servidor; dbname=$basedatos; charset=utf8mb4", $usuario, $contrasena
        );
        //configuramos manejo de errores
        $conexion ->setAttribute(
            PDO:: ATTR_ERRMODE,
            PDO:: ERRMODE_EXCEPTION
        );
        //echo "Conexión creada con éxito";
    }catch (PDOException $e){
        die("Error al conectar con la base de datos.");
    }
