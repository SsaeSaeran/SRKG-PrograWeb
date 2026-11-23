<?php
    // Punto de entrada del proyecto
    session_start();

    // Si ya hay sesión se va al panel, si no, al login
    if (isset($_SESSION['usuario_id'])) {
        header("Location: dashboard.php");
    } else {
        header("Location: login.php");
    }

    exit;
?>
