<?php
    session_start(); //SIEMPRE DEBE DE IR AL PRINCIPIO AL USAR UNA VARIBLE DE SESIÓN
    require "conexion.php";

    $usuario = trim($_POST["usuario"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if( $usuario == "" || $password == ""){
        
        exit ("Error: todos los campos son obligatorios");
    } 

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $consulta = $conexion->prepare($sql);
    $consulta->execute([$usuario]);

    $datos = $consulta->fetch(PDO:: FETCH_ASSOC);

    if($datos && password_verify($password, $datos["password"])){
        //YUPIIIIII
        $_SESSION["usuario"] = $datos["usuario"];

        header("Location: home.php");
        exit;
    }
     else {
        echo "Contraseña o usuario incorrectos";
    }