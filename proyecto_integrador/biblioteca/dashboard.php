<?php
    $raiz = "";
    require "config/auth.php";
    require "config/conexion.php";
    requiere_login();

    // El administrador ve toda la biblioteca y el usuario solo sus libros.
    // Por eso las consultas cambian segun el rol.
    if (es_admin()) {
        $sql = "SELECT estado, COUNT(*) AS total
                FROM libros
                WHERE activo = 1
                GROUP BY estado";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

    } else {
        $sql = "SELECT estado, COUNT(*) AS total
                FROM libros
                WHERE activo = 1 AND id_usuario = :id_usuario
                GROUP BY estado";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $_SESSION['usuario_id']]);
    }

    // Se empieza en cero para que las tres categorías existan aunque no tengan libros
    $conteos = ["Pendiente" => 0, "Leyendo" => 0, "Terminado" => 0];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $conteos[$fila['estado']] = (int) $fila['total'];
    }

    $total = $conteos["Pendiente"] + $conteos["Leyendo"] + $conteos["Terminado"];

    // Los ultimos 5 libros que se agregaron
    if (es_admin()) {
        $sql = "SELECT titulo, autor, estado, creado_en
                FROM libros
                WHERE activo = 1
                ORDER BY creado_en DESC, id_libro DESC
                LIMIT 5";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

    } else {
        $sql = "SELECT titulo, autor, estado, creado_en
                FROM libros
                WHERE activo = 1 AND id_usuario = :id_usuario
                ORDER BY creado_en DESC, id_libro DESC
                LIMIT 5";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_usuario' => $_SESSION['usuario_id']]);
    }

    $recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $titulo = "Panel";
    include "includes/header.php";
?>

<div class="cabecera-pagina">
    <div>
        <h2 class="mb-1">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></h2>
        <?php if (es_admin()): ?>
            <p class="texto-suave mb-0">Estás viendo todos los libros.</p>
        <?php endif; ?>
    </div>
    <a href="libros/crear.php" class="btn btn-vino">+ Agregar libro</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="tarjeta-indicador">
            <span class="nombre-indicador">Total de libros</span>
            <span class="numero-indicador"><?= $total ?></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="tarjeta-indicador">
            <span class="nombre-indicador">Pendientes</span>
            <span class="numero-indicador"><?= $conteos["Pendiente"] ?></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="tarjeta-indicador">
            <span class="nombre-indicador">Terminados</span>
            <span class="numero-indicador"><?= $conteos["Terminado"] ?></span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="panel-caja">
            <h3 class="titulo-caja">Libros por estado</h3>

            <!-- Los valores se pasan al script como atributos data-* -->
            <canvas id="graficaEstados" width="420" height="260"
                    data-pendiente="<?= $conteos["Pendiente"] ?>"
                    data-leyendo="<?= $conteos["Leyendo"] ?>"
                    data-terminado="<?= $conteos["Terminado"] ?>"></canvas>

            <div class="leyenda mt-3">
                <span><i class="punto punto-pendiente"></i>Pendiente</span>
                <span><i class="punto punto-leyendo"></i>Leyendo</span>
                <span><i class="punto punto-terminado"></i>Terminado</span>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel-caja">
            <h3 class="titulo-caja">Agregados recientemente</h3>

            <?php if (count($recientes) === 0): ?>
                <p class="texto-suave mb-0">Todavía no hay libros registrados.</p>
            <?php else: ?>
                <ul class="lista-reciente">
                    <?php foreach ($recientes as $libro): ?>
                        <li>
                            <div>
                                <strong><?= htmlspecialchars($libro['titulo']) ?></strong>
                                <span class="texto-suave d-block"><?= htmlspecialchars($libro['autor'] ?? '') ?></span>
                            </div>
                            <span class="badge-estado <?= clase_estado($libro['estado']) ?>">
                                <?= htmlspecialchars($libro['estado']) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="assets/js/grafica.js"></script>
<?php include "includes/footer.php"; ?>
