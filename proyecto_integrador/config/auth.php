<?php
    // Se inicia la sesión solo si todavía no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // $raiz dice desde que carpeta se está llamando:
    //   ""    -> paginas de la carpeta principal
    //   "../" -> paginas de la carpeta libros/
    if (!isset($raiz)) {
        $raiz = "";
    }

    // Si no hay sesión, no se puede entrar a una pagina interna
    function requiere_login() {
        global $raiz;

        if (!isset($_SESSION['usuario_id'])) {
            header("Location: " . $raiz . "login.php?status=sesion");
            exit;
        }
    }

    // El rol 1 es el Administrador, asi está guardado en la tabla roles
    function es_admin() {
        return isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1;
    }

    // Texto del rol para mostrarlo en la barra de navegación
    function nombre_rol() {
        if (es_admin()) {
            return "Administrador";
        }

        return "Usuario";
    }

    // Clase CSS de la etiqueta de estado
    function clase_estado($estado) {
        if ($estado == "Terminado") {
            return "estado-terminado";
        }

        if ($estado == "Leyendo") {
            return "estado-leyendo";
        }

        return "estado-pendiente";
    }
?>
