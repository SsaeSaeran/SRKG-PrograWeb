<?php
    session_start();                    // Se inicia la sesión para poder manipularla

    session_unset();                    // Se limpian todas las variables de la sesión
    session_destroy();                  // Se destruye la sesión del usuario

    header("Location: login.php");      // Se regresa al login
    exit;                               // Se detiene el script
?>
