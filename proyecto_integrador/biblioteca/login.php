<?php
    session_start();

    // Si ya hay sesión, no tiene caso mostrar el login otra vez
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
    <title>Inicio de sesión - Mi Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>
<body class="pagina-auth">

<div class="contenedor-auth">
    <div class="tarjeta-auth">
        <div class="panel-lateral">
            <h1 class="titulo-panel">Mi<br>Biblioteca</h1>
        </div>

        <div class="panel-formulario">
            <h2 class="titulo-form">Inicio de sesión</h2>
            <hr class="linea-titulo">

            <?php if ($status === "vacio"): ?>
                <div class="alert alert-warning">Escribe tu correo y tu contraseña.</div>
            <?php elseif ($status === "error"): ?>
                <div class="alert alert-danger">El correo o la contraseña no son correctos.</div>
            <?php elseif ($status === "registrado"): ?>
                <div class="alert alert-success">Cuenta creada correctamente. Ya puedes iniciar sesión.</div>
            <?php elseif ($status === "sesion"): ?>
                <div class="alert alert-warning">Primero tienes que iniciar sesión.</div>
            <?php endif; ?>

            <form action="procesar_login.php" method="post">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-vino w-100">Ingresar</button>
            </form>

            <p class="texto-suave mt-4 mb-0">
                ¿No tienes cuenta? <a href="registro.php" class="enlace-simple">Regístrate</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
