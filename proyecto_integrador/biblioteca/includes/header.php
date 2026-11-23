<?php
    // Encabezado de las paginas internas.
    // Antes de incluirlo se define:
    //   $titulo -> texto de la pestaña del navegador
    //   $raiz   -> "" o "../" segun la carpeta
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> | Mi Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $raiz ?>assets/css/estilos.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark barra-principal">
    <div class="container">
        <a class="navbar-brand marca" href="<?= $raiz ?>dashboard.php">Mi Biblioteca</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $raiz ?>dashboard.php">Panel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $raiz ?>libros/index.php">Libros</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="usuario-sesion">
                    <?= htmlspecialchars($_SESSION['nombre']) ?>
                    <span class="etiqueta-rol <?= es_admin() ? '' : 'etiqueta-usuario' ?>"><?= nombre_rol() ?></span>
                </span>
                <a class="btn btn-sm btn-salir" href="<?= $raiz ?>logout.php">Salir</a>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
