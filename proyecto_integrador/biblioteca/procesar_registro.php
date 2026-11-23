<?php
    session_start();
    require 'config/conexion.php';

    // Solo se acepta POST, si alguien entra por la URL se le regresa
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: registro.php");
        exit;
    }

    // Se reciben los datos del formulario
    $nombre    = trim($_POST['nombre'] ?? '');
    $correo    = trim($_POST['correo'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    // Aunque el HTML ya pide los campos, aqui se revisan otra vez porque el navegador se puede saltar
    if ($nombre === '' || $correo === '' || $password === '' || $confirmar === '') {
        header("Location: registro.php?status=vacio");
        exit;
    }

    // El correo tiene que tener un formato valido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?status=correo");
        exit;
    }

    // La contraseña debe tener al menos 8 caracteres
    if (strlen($password) < 8) {
        header("Location: registro.php?status=password");
        exit;
    }

    // Las dos contraseñas tienen que ser iguales
    if ($password !== $confirmar) {
        header("Location: registro.php?status=confirmacion");
        exit;
    }

    try {
        // Se revisa que el correo no este registrado
        $revisar = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo");
        $revisar->execute(['correo' => $correo]);

        if ($revisar->rowCount() > 0) {
            header("Location: registro.php?status=existe");
            exit;
        }

        // La contraseña se guarda encriptada, nunca en texto plano
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // El rol 2 es Usuario. Aunque alguien mande otro rol, aqui se ignora
        $sql = "INSERT INTO usuarios (nombre, correo, password, rol_id)
                VALUES (:nombre, :correo, :password, 2)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre'   => $nombre,
            'correo'   => $correo,
            'password' => $hash
        ]);

        header("Location: login.php?status=registrado");
        exit;

    } catch (PDOException $e) {
        header("Location: registro.php?status=error");
        exit;
    }
?>
