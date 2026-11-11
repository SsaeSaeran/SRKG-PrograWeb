<?php
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($usuario === "" || $password === "") {
        header("Location: registrar.php?status=empty");
        exit();
    }

    try {
        $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE usuario = ?";
        $consultaCheck = $conexion->prepare($sqlCheck);
        $consultaCheck->execute([$usuario]);

        if ($consultaCheck->fetchColumn() > 0) {
            header("Location: registrar.php?status=exists");
            exit();
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario, password) VALUES (?, ?)";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$usuario, $passwordHash]);

        header("Location: registrar.php?status=success");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
            header("Location: registrar.php?status=exists");
            exit();
        }

        header("Location: registrar.php?status=error");
        exit();
    }
} else {
    header("Location: registrar.php");
    exit();
}