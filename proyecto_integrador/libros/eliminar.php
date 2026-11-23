<?php
    $raiz = "../";
    require "../config/auth.php";
    require "../config/conexion.php";
    requiere_login();

    // Solo el administrador puede eliminar
    if (!es_admin()) {
        header("Location: index.php?status=permiso");
        exit;
    }

    // Solo por POST, un enlace GET no debe borrar nada
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php");
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: index.php?status=inexistente");
        exit;
    }

    // Borrado logico: no se borra la fila, solo se marca activo = 0.
    // Asi el libro se puede recuperar y no se rompe el historial.
    $sql = "UPDATE libros SET activo = 0 WHERE id_libro = :id AND activo = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    header("Location: index.php?status=eliminado");
    exit;
?>
