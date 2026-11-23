<?php
    $raiz = "../";
    require "../config/auth.php";
    require "../config/conexion.php";
    require "../config/imagenes.php";
    requiere_login();

    $errores = [];

    // Valores iniciales del formulario, se vuelven a usar si algo falla
    $datos = [
        "titulo"           => "",
        "autor"            => "",
        "editorial"        => "",
        "anio_publicacion" => "",
        "numero_paginas"   => "",
        "genero"           => "",
        "descripcion"      => "",
        "tipo"             => "Libro",
        "idioma"           => "Español",
        "encuadernacion"   => "Pasta blanda",
        "estado"           => "Pendiente",
    ];

    $estados_validos = ["Pendiente", "Leyendo", "Terminado"];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Se reciben los datos y se quitan los espacios de sobra
        $datos['titulo']           = trim($_POST['titulo'] ?? '');
        $datos['autor']            = trim($_POST['autor'] ?? '');
        $datos['editorial']        = trim($_POST['editorial'] ?? '');
        $datos['anio_publicacion'] = trim($_POST['anio_publicacion'] ?? '');
        $datos['numero_paginas']   = trim($_POST['numero_paginas'] ?? '');
        $datos['genero']           = trim($_POST['genero'] ?? '');
        $datos['descripcion']      = trim($_POST['descripcion'] ?? '');
        $datos['tipo']             = trim($_POST['tipo'] ?? 'Libro');
        $datos['idioma']           = trim($_POST['idioma'] ?? 'Español');
        $datos['encuadernacion']   = trim($_POST['encuadernacion'] ?? 'Pasta blanda');
        $datos['estado']           = trim($_POST['estado'] ?? 'Pendiente');

        // Validaciones del lado del servidor
        if ($datos['titulo'] === '') {
            $errores[] = "El título es obligatorio.";
        }

        if ($datos['autor'] === '') {
            $errores[] = "El autor es obligatorio.";
        }

        if ($datos['genero'] === '') {
            $errores[] = "El género es obligatorio.";
        }

        $anio_actual = (int) date('Y');

        if ($datos['anio_publicacion'] !== '') {
            if (!ctype_digit($datos['anio_publicacion'])
                || (int) $datos['anio_publicacion'] < 1400
                || (int) $datos['anio_publicacion'] > $anio_actual) {
                $errores[] = "El año debe ser un número entre 1400 y " . $anio_actual . ".";
            }
        }

        if ($datos['numero_paginas'] !== '') {
            if (!ctype_digit($datos['numero_paginas']) || (int) $datos['numero_paginas'] < 1) {
                $errores[] = "El número de páginas debe ser mayor que cero.";
            }
        }

        if (!in_array($datos['estado'], $estados_validos, true)) {
            $errores[] = "El estado seleccionado no es válido.";
        }

        if (mb_strlen($datos['descripcion']) > 2000) {
            $errores[] = "La descripción no debe pasar de 2000 caracteres.";
        }

        // La portada solo se lee si los demas datos están bien
        $imagen = null;

        if (count($errores) === 0) {
            try {
                $imagen = guardar_imagen($_FILES['imagen'] ?? []);
            } catch (Exception $e) {
                $errores[] = $e->getMessage();
            }
        }

        // Si todo está bien se guarda el libro
        if (count($errores) === 0) {
            $sql = "INSERT INTO libros
                        (id_usuario, titulo, autor, editorial, anio_publicacion, numero_paginas,
                         genero, descripcion, tipo, idioma, encuadernacion, estado, imagen, imagen_tipo)
                    VALUES
                        (:id_usuario, :titulo, :autor, :editorial, :anio, :paginas,
                         :genero, :descripcion, :tipo, :idioma, :encuadernacion, :estado, :imagen, :imagen_tipo)";

            $stmt = $pdo->prepare($sql);

            $parametros = [
                'id_usuario'     => $_SESSION['usuario_id'],
                'titulo'         => $datos['titulo'],
                'autor'          => $datos['autor'],
                'editorial'      => $datos['editorial'] !== '' ? $datos['editorial'] : null,
                'anio'           => $datos['anio_publicacion'] !== '' ? (int) $datos['anio_publicacion'] : null,
                'paginas'        => $datos['numero_paginas'] !== '' ? (int) $datos['numero_paginas'] : null,
                'genero'         => $datos['genero'],
                'descripcion'    => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
                'tipo'           => $datos['tipo'] !== '' ? $datos['tipo'] : 'Libro',
                'idioma'         => $datos['idioma'] !== '' ? $datos['idioma'] : 'Español',
                'encuadernacion' => $datos['encuadernacion'] !== '' ? $datos['encuadernacion'] : 'Pasta blanda',
                'estado'         => $datos['estado'],
                'imagen'         => $imagen !== null ? $imagen['datos'] : null,
                'imagen_tipo'    => $imagen !== null ? $imagen['tipo'] : null,
            ];

            try {
                $stmt->execute($parametros);
            } catch (PDOException $e) {
                $errores[] = "No se pudo guardar el libro. Intenta de nuevo.";
            }

            if (count($errores) === 0) {
                header("Location: index.php?status=creado");
                exit;
            }
        }
    }

    $titulo = "Agregar libro";
    include "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Agregar libro</h2>
    <a href="index.php" class="btn btn-contorno">Volver a la lista</a>
</div>

<?php if (count($errores) > 0): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="panel-caja">
    <form method="post" action="crear.php" enctype="multipart/form-data" class="row g-3">

        <div class="col-12">
            <label for="titulo" class="form-label">Título *</label>
            <input type="text" class="form-control" id="titulo" name="titulo" maxlength="200"
                   value="<?= htmlspecialchars($datos['titulo']) ?>" required>
        </div>

        <div class="col-md-6">
            <label for="autor" class="form-label">Autor *</label>
            <input type="text" class="form-control" id="autor" name="autor" maxlength="150"
                   value="<?= htmlspecialchars($datos['autor']) ?>" required>
        </div>

        <div class="col-md-6">
            <label for="editorial" class="form-label">Editorial</label>
            <input type="text" class="form-control" id="editorial" name="editorial" maxlength="100"
                   value="<?= htmlspecialchars($datos['editorial']) ?>">
        </div>

        <div class="col-md-6">
            <label for="genero" class="form-label">Género *</label>
            <input type="text" class="form-control" id="genero" name="genero" maxlength="120"
                   placeholder="Ej. Acción / Horror" value="<?= htmlspecialchars($datos['genero']) ?>" required>
        </div>

        <div class="col-12">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="2000"
                      placeholder="De qué trata el libro, por qué te gusta, dónde lo compraste..."><?= htmlspecialchars($datos['descripcion']) ?></textarea>
        </div>

        <div class="col-12">
            <label for="imagen" class="form-label">Portada (imagen)</label>
            <input type="file" class="form-control" id="imagen" name="imagen"
                   accept="image/jpeg,image/png,image/webp,image/gif">
            <div class="form-text">JPG, PNG, WEBP o GIF. Máximo 2 MB. Es opcional.</div>
        </div>

        <div class="col-md-3">
            <label for="anio_publicacion" class="form-label">Año</label>
            <input type="number" class="form-control" id="anio_publicacion" name="anio_publicacion"
                   min="1400" max="<?= date('Y') ?>" value="<?= htmlspecialchars($datos['anio_publicacion']) ?>">
        </div>

        <div class="col-md-3">
            <label for="numero_paginas" class="form-label">Páginas</label>
            <input type="number" class="form-control" id="numero_paginas" name="numero_paginas"
                   min="1" value="<?= htmlspecialchars($datos['numero_paginas']) ?>">
        </div>

        <div class="col-md-4">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-select" id="tipo" name="tipo">
                <?php foreach (["Libro", "Manga", "Novela Ligera", "Artbook", "Fanbook", "Revista", "Poesía", "Libro de Arte", "Científico / Educativo"] as $opcion): ?>
                    <option value="<?= $opcion ?>" <?= $datos['tipo'] === $opcion ? "selected" : "" ?>>
                        <?= $opcion ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="idioma" class="form-label">Idioma</label>
            <select class="form-select" id="idioma" name="idioma">
                <?php foreach (["Español", "Inglés", "Japonés", "Inglés / Japonés", "Japonés / Inglés"] as $opcion): ?>
                    <option value="<?= $opcion ?>" <?= $datos['idioma'] === $opcion ? "selected" : "" ?>>
                        <?= $opcion ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="encuadernacion" class="form-label">Encuadernación</label>
            <select class="form-select" id="encuadernacion" name="encuadernacion">
                <?php foreach (["Pasta blanda", "Pasta dura"] as $opcion): ?>
                    <option value="<?= $opcion ?>" <?= $datos['encuadernacion'] === $opcion ? "selected" : "" ?>>
                        <?= $opcion ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="estado" class="form-label">Estado *</label>
            <select class="form-select" id="estado" name="estado">
                <?php foreach ($estados_validos as $opcion): ?>
                    <option value="<?= $opcion ?>" <?= $datos['estado'] === $opcion ? "selected" : "" ?>>
                        <?= $opcion ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 pt-2">
            <button type="submit" class="btn btn-vino">Guardar libro</button>
            <a href="index.php" class="btn btn-contorno">Cancelar</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
