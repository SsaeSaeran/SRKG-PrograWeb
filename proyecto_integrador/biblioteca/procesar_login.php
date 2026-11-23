<?php
    session_start();                        // Se inicia la sesión para guardar los datos del usuario
    require 'config/conexion.php';          // Se importa la conexión ($pdo)

    // Si alguien entra por la URL y no por el formulario, se le regresa al login
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: login.php");
        exit;
    }

    $correo   = trim($_POST['correo'] ?? '');   // Correo escrito en el formulario
    $password = $_POST['password'] ?? '';       // Contraseña escrita en el formulario

    if ($correo === '' || $password === '') {
        header("Location: login.php?status=vacio");
        exit;
    }

    // Se busca el usuario y tambien el nombre de su rol
    $sql = "SELECT u.id_usuario, u.nombre, u.password, u.rol_id, r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            WHERE u.correo = :correo AND u.activo = 1";

    $stmt = $pdo->prepare($sql);                // Consulta preparada para evitar inyección SQL
    $stmt->execute(['correo' => $correo]);      // El correo viaja como parametro, no pegado al SQL
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);  // Arreglo asociativo, o false si no existe

    // password_verify compara la contraseña escrita con el hash que está en la base
    if ($usuario && password_verify($password, $usuario['password'])) {

        session_regenerate_id(true);            // Se cambia el id de sesión por seguridad

        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['nombre']     = $usuario['nombre'];
        $_SESSION['rol_id']     = $usuario['rol_id'];

        header("Location: dashboard.php");
        exit;
    }

    // Si el usuario no existe o la contraseña no coincide
    header("Location: login.php?status=error");
    exit;
?>
