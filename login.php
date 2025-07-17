<?php
session_start();
require_once 'config/database.php'; // Ajusta la ruta si es diferente

$db = new Database();             // Instanciamos la clase
$conn = $db->getConexion();       // Obtenemos la conexión mysqli

$message = '';

// Registro de usuario
if (isset($_POST['register'])) {
    $nombre = trim($_POST['reg_nombre']);
    $email = trim($_POST['reg_email']);
    $password = trim($_POST['reg_password']);
    $fecha_registro = date('Y-m-d H:i:s');

    if (empty($nombre) || empty($email) || empty($password)) {
        $message = "Completa todos los campos para registrarte.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "El correo ya está registrado.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre, email, password, fecha_registro) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $nombre, $email, $password_hash, $fecha_registro);

            if ($stmt_insert->execute()) {
                $message = "Usuario registrado con éxito. Ahora puedes iniciar sesión.";
            } else {
                $message = "Error al registrar el usuario.";
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }
}

// Login de usuario
if (isset($_POST['login'])) {
    $email = trim($_POST['login_correo']);
    $password = trim($_POST['login_password']);

    if (empty($email) || empty($password)) {
        $message = "Por favor completa todos los campos para iniciar sesión.";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            header('Location: ../index.html'); 
            exit;
        } else {
            $message = "Correo o contraseña incorrectos.";
        }

        $stmt->close();
    }
}

$db->close(); // Cierra la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Login y Registro</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f3f4f6;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        padding-top: 50px;
    }

    .container {
        background: white;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
    }

    h2 {
        text-align: center;
        color: #111827;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        transition: border 0.3s;
    }

    input:focus {
        border-color: #2563eb;
        outline: none;
    }

    button {
        padding: 12px;
        background: #2563eb;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s;
    }

    button:hover {
        background: #1e40af;
    }

    .message {
        text-align: center;
        margin-top: 15px;
        color: crimson;
        font-weight: bold;
    }

    .divider {
        margin: 30px 0 15px;
        text-align: center;
        position: relative;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .divider::before,
    .divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: #d1d5db;
    }

    .divider::before {
        left: 0;
    }

    .divider::after {
        right: 0;
    }
</style>

</head>
<body>

<div class="container">
    <h2>Iniciar sesión</h2>
    <form method="post" action="">
        <input type="email" name="login_correo" placeholder="Correo electrónico" required>
        <input type="password" name="login_password" placeholder="Contraseña" required>
        <button type="submit" name="login">Iniciar sesión</button>

        <div style="text-align:right; margin-top: 8px;">
        <a href="recuperar.php" style="font-size: 0.9rem; color: #2563eb;">¿Olvidaste tu contraseña?</a>
    </div>
    </form>

    <div class="divider">¿No tienes cuenta?</div>

    <h2>Registrarse</h2>
    <form method="post" action="">
        <input type="text" name="reg_nombre" placeholder="Nombre completo" required>
        <input type="email" name="reg_email" placeholder="Correo electrónico" required>
        <input type="password" name="reg_password" placeholder="Contraseña" required>
        <button type="submit" name="register">Registrarse</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
