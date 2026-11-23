<?php
    session_start();

    if (isset($_SESSION['usuario_id'])) {
        header("Location: dashboard.php");
        exit;
    }

    $status = $_GET["status"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - Mi Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>
<body class="pagina-auth">

<div class="contenedor-auth">
    <div class="tarjeta-auth">
        <div class="panel-lateral">
            <h1 class="titulo-panel">Tu<br>colección</h1>
        </div>

        <div class="panel-formulario">
            <h2 class="titulo-form">Crear cuenta</h2>
            <hr class="linea-titulo">
            <?php if ($status === "vacio"): ?>
                <div class="alert alert-warning">Todos los campos son obligatorios.</div>
            <?php elseif ($status === "correo"): ?>
                <div class="alert alert-warning">Escribe un correo válido.</div>
            <?php elseif ($status === "existe"): ?>
                <div class="alert alert-danger">Ese correo ya está registrado. Usa otro o inicia sesión.</div>
            <?php elseif ($status === "password"): ?>
                <div class="alert alert-warning">La contraseña debe tener al menos 8 caracteres.</div>
            <?php elseif ($status === "confirmacion"): ?>
                <div class="alert alert-warning">Las dos contraseñas no coinciden.</div>
            <?php elseif ($status === "error"): ?>
                <div class="alert alert-danger">No se pudo crear la cuenta. Intenta de nuevo.</div>
            <?php endif; ?>

            <form action="procesar_registro.php" method="post">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" required>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" maxlength="150" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password"
                           minlength="8" required>
                </div>

                <div class="mb-4">
                    <label for="confirmar" class="form-label">Confirmar contraseña</label>
                    <input type="password" class="form-control" id="confirmar" name="confirmar"
                           minlength="8" required>
                </div>

                <button type="submit" class="btn btn-vino w-100">Registrarme</button>
            </form>

            <p class="texto-suave mt-4 mb-0">
                ¿Ya tienes cuenta? <a href="login.php" class="enlace-simple">Inicia sesión</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
