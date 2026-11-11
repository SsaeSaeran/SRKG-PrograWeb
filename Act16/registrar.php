<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="card shadow p-4 auth-card">
        <h2 class="text-center mb-4">Crear Cuenta</h2>

        <?php if (isset($_GET['status'])):
            if ($_GET['status'] === 'success'): ?>
                <div class="alert alert-success" role="alert">
                    Usuario registrado correctamente. <a href="index.php" class="alert-link">Inicia sesión aquí</a>.
                </div>
            <?php elseif ($_GET['status'] === 'empty'): ?>
                <div class="alert alert-warning" role="alert">
                    Por favor, completa todos los campos.
                </div>
            <?php elseif ($_GET['status'] === 'exists'): ?>
                <div class="alert alert-danger" role="alert">
                    El nombre de usuario ya está en uso. Elige otro.
                </div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="alert alert-danger" role="alert">
                    Ocurrió un error al procesar el registro. Intenta de nuevo.
                </div>
            <?php endif; 
             endif; ?>

        <form action="crear_usuario.php" method="post">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Registrar</button>
        </form>

        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </div>
</div>

</body>
</html>