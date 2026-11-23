<?php
    // Manejo de las portadas.
    // La imagen se guarda dentro de la base de datos, en la columna libros.imagen.
    // Así no se pierde cuando se copia o se reinstala el proyecto.

    // Tamaño máximo permitido: 2 MB
    const IMAGEN_MAX_BYTES = 2097152;

    // Tipos de archivo que se aceptan
    const IMAGEN_EXTENSIONES = ["jpg", "jpeg", "png", "webp", "gif"];

    // Revisa el archivo que llego en $_FILES y devuelve sus bytes y su tipo,
    // o null si el usuario no eligio ninguna imagen.
    function guardar_imagen($archivo) {
        // Si no se eligio archivo no es un error, la portada es opcional
        if (!isset($archivo["error"]) || $archivo["error"] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($archivo["error"] === UPLOAD_ERR_INI_SIZE || $archivo["error"] === UPLOAD_ERR_FORM_SIZE) {
            throw new Exception("La imagen supera el tamaño permitido por el servidor.");
        }

        if ($archivo["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("No se pudo subir la imagen. Intenta de nuevo.");
        }

        if ($archivo["size"] > IMAGEN_MAX_BYTES) {
            throw new Exception("La imagen no debe pesar más de 2 MB.");
        }

        // getimagesize() abre el archivo y confirma que de verdad es una imagen.
        // No se confía en la extensión ni en el tipo que manda el navegador.
        $info = @getimagesize($archivo["tmp_name"]);

        if ($info === false) {
            throw new Exception("El archivo no es una imagen válida.");
        }

        $extension = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

        if (!in_array($extension, IMAGEN_EXTENSIONES, true)) {
            throw new Exception("Solo se permiten imágenes JPG, PNG, WEBP o GIF.");
        }

        // Se leen los bytes del archivopara guardarlos en la base
        $bytes = file_get_contents($archivo["tmp_name"]);

        if ($bytes === false) {
            throw new Exception("No se pudo leer la imagen.");
        }

        return ["datos" => $bytes, "tipo" => $info["mime"]];
    }

    // Ruta que se pone en el src de la imagen
    function ruta_imagen($id_libro, $raiz = "") {
        return $raiz . "imagen.php?id=" . (int) $id_libro;
    }
?>
