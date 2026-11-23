<?php
    // Sirve la portada de un libro.
    // La imagen está guardada en la base (columna LONGBLOB), asi que este
    // archivo lee los bytes y los manda al navegador con el tipo correcto.
    // Se usa asi: <img src="imagen.php?id=5">
    require "config/auth.php";
    require "config/conexion.php";
    requiere_login();

    $id = (int) ($_GET['id'] ?? 0);

    // Se trae la imagen, su tipo y el dueño del libro
    $sql = "SELECT imagen, imagen_tipo, id_usuario
            FROM libros
            WHERE id_libro = :id AND activo = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si el libro no existe o no tiene imagen, se responde 404
    if (!$libro || empty($libro['imagen'])) {
        http_response_code(404);
        exit;
    }

    // El admin ve cualquiera, el usuario solo los suyos
    if (!es_admin() && (int) $libro['id_usuario'] !== (int) $_SESSION['usuario_id']) {
        http_response_code(403);
        exit;
    }

    // Se avisa al navegador que lo que sigue son bytes de una imagen
    header("Content-Type: " . $libro['imagen_tipo']);
    header("Content-Length: " . strlen($libro['imagen']));

    // Se imprimen los bytes y se termina
    echo $libro['imagen'];
    exit;
?>
