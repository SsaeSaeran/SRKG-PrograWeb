<?php
    $raiz = "../";
    require "../config/auth.php";
    require "../config/conexion.php";
    require "../config/imagenes.php";
    requiere_login();

    // Texto del buscador, vacio si no se busco nada
    $busqueda = trim($_GET['q'] ?? '');

    // La consulta cambia segun el rol:
    //   admin   -> todos los libros activos
    //   usuario -> solo los suyos
    if (es_admin()) {
        $sql = "SELECT l.id_libro, l.titulo, l.autor, l.genero, l.anio_publicacion, l.estado,
                       (l.imagen IS NOT NULL) AS tiene_imagen, u.nombre AS dueno
                FROM libros l
                INNER JOIN usuarios u ON u.id_usuario = l.id_usuario
                WHERE l.activo = 1";

        $parametros = [];

    } else {
        $sql = "SELECT l.id_libro, l.titulo, l.autor, l.genero, l.anio_publicacion, l.estado,
                       (l.imagen IS NOT NULL) AS tiene_imagen, u.nombre AS dueno
                FROM libros l
                INNER JOIN usuarios u ON u.id_usuario = l.id_usuario
                WHERE l.activo = 1 AND l.id_usuario = :id_usuario";

        $parametros = ['id_usuario' => $_SESSION['usuario_id']];
    }

    // Si hay busqueda se filtra por titulo o por autor
    if ($busqueda !== '') {
        $sql .= " AND (l.titulo LIKE :busqueda OR l.autor LIKE :busqueda)";
        $parametros['busqueda'] = "%" . $busqueda . "%";
    }

    $sql .= " ORDER BY l.titulo ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $status = $_GET['status'] ?? '';

    $titulo = "Libros";
    include "../includes/header.php";
?>

<div class="cabecera-pagina">
    <div>
        <h2 class="mb-1">Libros</h2>
        <p class="texto-suave mb-0">
            <?= count($libros) ?> <?= count($libros) == 1 ? "libro" : "libros" ?>
            <?= $busqueda !== "" ? "encontrados" : "en la lista" ?>
        </p>
    </div>
    <a href="crear.php" class="btn btn-vino">+ Agregar libro</a>
</div>

<?php if ($status === "creado"): ?>
    <div class="alert alert-success">Libro agregado correctamente.</div>
<?php elseif ($status === "editado"): ?>
    <div class="alert alert-success">Los cambios se guardaron.</div>
<?php elseif ($status === "eliminado"): ?>
    <div class="alert alert-success">El libro se deshabilitó.</div>
<?php elseif ($status === "permiso"): ?>
    <div class="alert alert-danger">No tienes permiso para hacer esa operación.</div>
<?php elseif ($status === "inexistente"): ?>
    <div class="alert alert-warning">Ese libro no existe o fue deshabilitado.</div>
<?php endif; ?>

<form class="row g-2 mb-4" method="get" action="index.php">
    <div class="col-sm-8 col-md-6">
        <input type="text" class="form-control" name="q" placeholder="Buscar por título o autor..."
               value="<?= htmlspecialchars($busqueda) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-vino">Buscar</button>
    </div>
    <?php if ($busqueda !== ""): ?>
        <div class="col-auto">
            <a href="index.php" class="btn btn-contorno">Limpiar</a>
        </div>
    <?php endif; ?>
</form>

<div class="panel-caja p-0">
    <?php if (count($libros) === 0): ?>
        <div class="p-4">
            <p class="texto-suave mb-1">
                <?= $busqueda !== "" ? "No hay libros que coincidan con la búsqueda." : "Todavía no tienes libros registrados." ?>
            </p>
            <?php if ($busqueda === ""): ?>
                <a href="crear.php">Agrega el primero</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table tabla-libros mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Género</th>
                        <th>Año</th>
                        <th>Estado</th>
                        <?php if (es_admin()): ?>
                            <th>Dueño</th>
                        <?php endif; ?>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($libros as $libro): ?>
                        <tr>
                            <td>
                                <div class="celda-titulo">
                                    <?php if ($libro['tiene_imagen']): ?>
                                        <img class="miniatura"
                                             src="<?= htmlspecialchars(ruta_imagen($libro['id_libro'], "../")) ?>"
                                             alt="Portada de <?= htmlspecialchars($libro['titulo']) ?>">
                                    <?php else: ?>
                                        <span class="miniatura miniatura-vacia" aria-hidden="true">
                                            <?= htmlspecialchars(mb_strtoupper(mb_substr($libro['titulo'], 0, 1))) ?>
                                        </span>
                                    <?php endif; ?>

                                    <a class="enlace-titulo" href="ver.php?id=<?= (int) $libro['id_libro'] ?>">
                                        <?= htmlspecialchars($libro['titulo']) ?>
                                    </a>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($libro['autor'] ?? '') ?></td>
                            <td><?= htmlspecialchars($libro['genero'] ?? '') ?></td>
                            <td><?= htmlspecialchars($libro['anio_publicacion'] ?? '') ?></td>
                            <td>
                                <span class="badge-estado <?= clase_estado($libro['estado']) ?>">
                                    <?= htmlspecialchars($libro['estado']) ?>
                                </span>
                            </td>
                            <?php if (es_admin()): ?>
                                <td><?= htmlspecialchars($libro['dueno']) ?></td>
                            <?php endif; ?>
                            <td>
                                <div class="acciones-tabla">
                                    <a class="btn btn-sm btn-contorno"
                                       href="ver.php?id=<?= (int) $libro['id_libro'] ?>">Ver</a>
                                    <a class="btn btn-sm btn-contorno"
                                       href="editar.php?id=<?= (int) $libro['id_libro'] ?>">Editar</a>

                                    <?php if (es_admin()): ?>
                                        <!-- El borrado va por POST, nunca por un enlace GET -->
                                        <form method="post" action="eliminar.php"
                                              onsubmit="return confirm('¿Deshabilitar este libro?');">
                                            <input type="hidden" name="id" value="<?= (int) $libro['id_libro'] ?>">
                                            <button type="submit" class="btn btn-sm btn-eliminar">Eliminar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
