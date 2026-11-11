<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Bienvenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <span class="navbar-brand fw-semibold">Sistema Web</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small">
                Usuario activo: <strong><?= htmlspecialchars($_SESSION["usuario"]) ?></strong>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>
</nav>

<div class="home-wrapper">
    <div class="card shadow dashboard-card p-4 p-md-5 text-center">

        <h2 class="fw-bold text-dark mb-2">
            ¡Bienvenido, <?= htmlspecialchars($_SESSION["usuario"]) ?>!
        </h2>
        
        <p class="text-muted mb-4">
            Has iniciado sesión correctamente. 

        <div class="d-grid gap-2">
            <a href="logout.php" class="btn btn-danger btn-lg">
                Finalizar sesión
            </a>
        </div>
    </div>
</div>

</body>
</html>