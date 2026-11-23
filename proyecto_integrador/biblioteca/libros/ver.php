<?php
    // Pagina de detalle de UN libro.
    // Es una sola plantilla para los 104 libros: el libro que se muestra
    // depende del id que llega por la URL. Ejemplo: ver.php?id=7
    $raiz = "../";
    require "../config/auth.php";
    require "../config/conexion.php";
    require "../config/imagenes.php";
    requiere_login();

    $errores = [];
    $status  = $_GET['status'] ?? '';

    // Si falla el guardado de la descripción, el editor se abre solo
    $error_descripcion = false;

    // El id llega por GET cuando se abre la pagina y por POST al guardar
    $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

    // Se busca el libro junto con el nombre de su dueño
    $sql = "SELECT l.id_libro, l.id_usuario, l.titulo, l.autor, l.editorial,
                   l.anio_publicacion, l.numero_paginas, l.genero, l.descripcion,
                   l.tipo, l.idioma, l.encuadernacion, l.estado,
                   (l.imagen IS NOT NULL) AS tiene_imagen,
                   u.nombre AS dueno
            FROM libros l
            INNER JOIN usuarios u ON u.id_usuario = l.id_usuario
            WHERE l.id_libro = :id AND l.activo = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$libro) {
        header("Location: index.php?status=inexistente");
        exit;
    }

    // El admin ve cualquiera, el usuario solo los suyos
    if (!es_admin() && (int) $libro['id_usuario'] !== (int) $_SESSION['usuario_id']) {
        header("Location: index.php?status=permiso");
        exit;
    }

    // La pagina tiene dos formularios pequeños y cada uno manda su propio
    // "accion" para saber que guardar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $accion = $_POST['accion'] ?? '';

        // Formulario de la izquierda: solo la portada
        if ($accion === 'imagen') {
            $quitar = isset($_POST['quitar_imagen']);

            // Si se eligio una portada nueva, se leen sus bytes
            $imagen_nueva = null;

            try {
                $imagen_nueva = guardar_imagen($_FILES['imagen'] ?? []);
            } catch (Exception $e) {
                $errores[] = $e->getMessage();
            }

            if (count($errores) === 0) {
                if ($imagen_nueva !== null) {
                    $datos_imagen = $imagen_nueva['datos'];
                    $tipo_imagen  = $imagen_nueva['tipo'];
                } elseif ($quitar) {
                    // Quitar la portada actual: se dejan las columnas en NULL
                    $datos_imagen = null;
                    $tipo_imagen  = null;
                } else {
                    $errores[] = "Elige una imagen o marca \"Quitar la portada actual\".";
                }
            }

            if (count($errores) === 0) {
                $sql = "UPDATE libros
                        SET imagen = :imagen, imagen_tipo = :imagen_tipo
                        WHERE id_libro = :id";

                $parametros = [
                    'imagen'      => $datos_imagen,
                    'imagen_tipo' => $tipo_imagen,
                    'id'          => $id,
                ];

                // El usuario solo puede modificar sus propios libros
                if (!es_admin()) {
                    $sql .= " AND id_usuario = :id_usuario";
                    $parametros['id_usuario'] = $_SESSION['usuario_id'];
                }

                try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($parametros);
                } catch (PDOException $e) {
                    $errores[] = "No se pudo guardar la portada. Intenta de nuevo.";
                }

                if (count($errores) === 0) {
                    header("Location: ver.php?id=" . $id . "&status=portada");
                    exit;
                }
            }
        }

        // Formulario de la derecha: solo la descripción
        if ($accion === 'descripcion') {

            $descripcion = trim($_POST['descripcion'] ?? '');

            if (mb_strlen($descripcion) > 2000) {
                $errores[] = "La descripción no debe pasar de 2000 caracteres.";
            }

            if (count($errores) === 0) {
                $sql = "UPDATE libros SET descripcion = :descripcion WHERE id_libro = :id";

                $parametros = [
                    'descripcion' => $descripcion !== '' ? $descripcion : null,
                    'id'          => $id,
                ];

                if (!es_admin()) {
                    $sql .= " AND id_usuario = :id_usuario";
                    $parametros['id_usuario'] = $_SESSION['usuario_id'];
                }

                try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($parametros);
                } catch (PDOException $e) {
                    $errores[] = "No se pudo guardar la descripción. Intenta de nuevo.";
                }

                if (count($errores) === 0) {
                    header("Location: ver.php?id=" . $id . "&status=descripcion");
                    exit;
                }
            }

            // Si hubo error, el textarea conserva lo que se escribio
            $libro['descripcion'] = $descripcion;
            $error_descripcion = true;
        }
    }

    $titulo = $libro['titulo'];
    include "../includes/header.php";
?>

<div class="cabecera-pagina">
    <div>
        <p class="texto-suave mb-1">
            <a class="enlace-simple" href="index.php">&larr; Volver a la lista</a>
        </p>
        <h2 class="mb-1"><?= htmlspecialchars($libro['titulo']) ?></h2>
        <p class="texto-suave mb-0">
            <?= htmlspecialchars($libro['autor'] !== '' ? $libro['autor'] : "Autor no registrado") ?>
            <?php if (es_admin()): ?>
                &middot; Dueño: <?= htmlspecialchars($libro['dueno']) ?>
            <?php endif; ?>
        </p>
    </div>
    <a href="editar.php?id=<?= (int) $libro['id_libro'] ?>" class="btn btn-contorno">Editar todos los datos</a>
</div>

<?php if ($status === "portada"): ?>
    <div class="alert alert-success">La portada se guardó.</div>
<?php elseif ($status === "descripcion"): ?>
    <div class="alert alert-success">La descripción se guardó.</div>
<?php elseif ($status === "editado"): ?>
    <div class="alert alert-success">Los cambios del libro se guardaron.</div>
<?php endif; ?>

<?php if (count($errores) > 0): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="detalle-libro">

    <!-- Columna izquierda: la portada y su formulario -->
    <div class="detalle-portada">

        <?php if ($libro['tiene_imagen']): ?>
            <img class="portada-detalle"
                 src="<?= htmlspecialchars(ruta_imagen($libro['id_libro'], "../")) ?>"
                 alt="Portada de <?= htmlspecialchars($libro['titulo']) ?>">
        <?php else: ?>
            <div class="portada-detalle portada-vacia">
                <span>Sin portada</span>
                <small>Sube una imagen abajo</small>
            </div>
        <?php endif; ?>

        <div class="panel-caja panel-portada">
            <h3 class="titulo-caja">Portada</h3>

            <form method="post" action="ver.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= (int) $libro['id_libro'] ?>">
                <input type="hidden" name="accion" value="imagen">

                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" id="imagen" name="imagen"
                       accept="image/jpeg,image/png,image/webp,image/gif">
                <div class="form-text mb-3">JPG, PNG, WEBP o GIF. Máximo 2 MB.</div>

                <?php if ($libro['tiene_imagen']): ?>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="quitar_imagen" name="quitar_imagen">
                        <label class="form-check-label" for="quitar_imagen">Quitar la portada actual</label>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-vino w-100">
                    <?= $libro['tiene_imagen'] ? "Cambiar portada" : "Subir portada" ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Columna derecha: los datos del libro y la descripción -->
    <div class="detalle-datos">
        <div class="panel-caja">
            <h3 class="titulo-caja">Datos del libro</h3>

            <ul class="lista-datos">
                <li><span>Editorial</span><strong><?= htmlspecialchars($libro['editorial'] ?: "—") ?></strong></li>
                <li><span>Año</span><strong><?= htmlspecialchars($libro['anio_publicacion'] ?: "—") ?></strong></li>
                <li><span>Páginas</span><strong><?= htmlspecialchars($libro['numero_paginas'] ?: "—") ?></strong></li>
                <li><span>Género</span><strong><?= htmlspecialchars($libro['genero'] ?: "—") ?></strong></li>
                <li><span>Tipo</span><strong><?= htmlspecialchars($libro['tipo']) ?></strong></li>
                <li><span>Idioma</span><strong><?= htmlspecialchars($libro['idioma'] ?: "—") ?></strong></li>
                <li><span>Encuadernación</span><strong><?= htmlspecialchars($libro['encuadernacion']) ?></strong></li>
                <li>
                    <span>Estado</span>
                    <strong>
                        <span class="badge-estado <?= clase_estado($libro['estado']) ?>">
                            <?= htmlspecialchars($libro['estado']) ?>
                        </span>
                    </strong>
                </li>
            </ul>
        </div>

        <div class="panel-caja">
            <h3 class="titulo-caja">Descripción</h3>

            <?php if (!empty($libro['descripcion'])): ?>
                <p class="descripcion-libro"><?= nl2br(htmlspecialchars($libro['descripcion'])) ?></p>
            <?php else: ?>
                <p class="texto-suave descripcion-libro">Todavía no hay descripción.</p>
            <?php endif; ?>

            <!-- Con <details> el editor se abre y se cierra sin JavaScript -->
            <details class="editor-descripcion" <?= (empty($libro['descripcion']) || $error_descripcion) ? "open" : "" ?>>
                <summary><?= !empty($libro['descripcion']) ? "Editar descripción" : "Escribir descripción" ?></summary>

                <form method="post" action="ver.php">
                    <input type="hidden" name="id" value="<?= (int) $libro['id_libro'] ?>">
                    <input type="hidden" name="accion" value="descripcion">

                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="5" maxlength="2000"
                              placeholder="De qué trata el libro, por qué te gusta, dónde lo compraste..."><?= htmlspecialchars($libro['descripcion'] ?? '') ?></textarea>
                    <div class="form-text">Máximo 2000 caracteres.</div>

                    <button type="submit" class="btn btn-vino mt-3">Guardar descripción</button>
                </form>
            </details>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
