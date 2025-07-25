<?php
// Indica que el código que sigue es PHP y será procesado por el servidor.

session_start();
// Inicia una sesión PHP para almacenar datos del usuario (como id_usuario y nombre)
// que persisten entre páginas mientras el usuario está conectado.

require_once 'config/database.php';
// Incluye el archivo config/database.php, que contiene la configuración o clase
// para conectar con la base de datos (se asume que define la clase Database).

$db = new Database();
// Crea una instancia de la clase Database para gestionar la conexión a la base de datos.

$conn = $db->getConexion();
// Obtiene la conexión a la base de datos (objeto mysqli) desde la clase Database
// y la almacena en la variable $conn.

$message = '';
$message_type = '';
// Inicializa una variable $message vacía para almacenar mensajes de éxito o error
// que se mostrarán al usuario en la interfaz.

// Registro de usuario
if (isset($_POST['register'])) {
// Verifica si se ha enviado el formulario de registro (botón con name="register").

    $nombre = trim($_POST['reg_nombre']);
    // Obtiene el valor del campo reg_nombre (nombre completo) del formulario
    // y usa trim() para eliminar espacios en blanco al inicio y final.

    $email = trim($_POST['reg_email']);
    // Obtiene el valor del campo reg_email (correo electrónico) y elimina espacios.

    $password = trim($_POST['reg_password']);
    // Obtiene el valor del campo reg_password (contraseña) y elimina espacios.

    $fecha_registro = date('Y-m-d H:i:s');
    // Obtiene la fecha y hora actual en formato 'YYYY-MM-DD HH:MM:SS'
    // para registrar cuándo se creó el usuario.

    if (empty($nombre) || empty($email) || empty($password)) {
    // Verifica si alguno de los campos (nombre, email, contraseña) está vacío.

        $message = "Completa todos los campos para registrarte.";
        $message_type = 'error';
        // Si falta algún campo, establece un mensaje de error para mostrar al usuario.

    } else {
        // Si todos los campos están completos, continúa con el proceso de registro.

        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        // Prepara una consulta SQL para verificar si el correo ya está registrado
        // en la tabla usuarios. El ? es un marcador de posición para el email.

        $stmt->bind_param("s", $email);
        // Vincula el valor de $email al marcador de posición (?).
        // "s" indica que el valor es una cadena (string).

        $stmt->execute();
        // Ejecuta la consulta preparada para buscar el email en la base de datos.

        $stmt->store_result();
        // Almacena el resultado de la consulta para verificar el número de filas.

        if ($stmt->num_rows > 0) {
        // Si num_rows es mayor a 0, significa que el correo ya está registrado.

            $message = "El correo ya está registrado.";
            $message_type = 'error';
            // Establece un mensaje de error para indicar que el email ya existe.

        } else {
            // Si el correo no está registrado, procede a registrar el nuevo usuario.

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            // Genera un hash seguro de la contraseña usando el algoritmo por defecto
            // (actualmente bcrypt, que es seguro para almacenar contraseñas).

            $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre, email, password, fecha_registro) VALUES (?, ?, ?, ?)");
            // Prepara una consulta SQL para insertar un nuevo usuario en la tabla usuarios.
            // Los ? son marcadores de posición para los valores.

            $stmt_insert->bind_param("ssss", $nombre, $email, $password_hash, $fecha_registro);
            // Vincula los valores a los marcadores de posición.
            // "ssss" indica que todos los valores son cadenas (strings).

            if ($stmt_insert->execute()) {
            // Ejecuta la consulta para insertar el usuario en la base de datos.

                $message = "Usuario registrado con éxito. Ahora puedes iniciar sesión.";
                $message_type = 'success';
                // Si la inserción es exitosa, establece un mensaje de éxito.

            } else {
                $message = "Error al registrar el usuario.";
                $message_type = 'error';
                // Si falla la inserción, establece un mensaje de error.

            }

            $stmt_insert->close();
            // Cierra la consulta preparada de inserción para liberar recursos.
        }

        $stmt->close();
        // Cierra la consulta preparada de verificación de email para liberar recursos.
    }
}

// Login de usuario
if (isset($_POST['login'])) {
// Verifica si se ha enviado el formulario de inicio de sesión (botón con name="login").

    $email = trim($_POST['login_correo']);
    // Obtiene el valor del campo login_correo (email) y elimina espacios.

    $password = trim($_POST['login_password']);
    // Obtiene el valor del campo login_password (contraseña) y elimina espacios.

    if (empty($email) || empty($password)) {
    // Verifica si alguno de los campos (email, contraseña) está vacío.

        $message = "Por favor completa todos los campos para iniciar sesión.";
        $message_type = 'error';
        // Si falta algún campo, establece un mensaje de error.

    } else {
        // Si los campos están completos, continúa con el proceso de inicio de sesión.

        $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
        // Prepara una consulta SQL para buscar un usuario por su email
        // y obtener su id, nombre y contraseña hasheada.

        $stmt->bind_param("s", $email);
        // Vincula el valor de $email al marcador de posición (?).
        // "s" indica que es una cadena (string).

        $stmt->execute();
        // Ejecuta la consulta preparada.

        $result = $stmt->get_result();
        // Obtiene el resultado de la consulta como un objeto de resultado.

        $user = $result->fetch_assoc();
        // Obtiene la primera fila del resultado como un array asociativo
        // con las claves id, nombre y password.
        var_dump($user['id']);

        if ($user && password_verify($password, $user['password'])) {
        // Verifica si se encontró un usuario ($user no es false) y si la contraseña
        // ingresada coincide con el hash almacenado usando password_verify.

            $_SESSION['id_usuario'] = $user['id'];
            // Almacena el ID del usuario en la sesión para usarlo en otras páginas.

            $_SESSION['user_name'] = $user['nombre'];
            // Almacena el nombre del usuario en la sesión.

            session_write_close();
            // Guarda los datos de la sesión de forma explícita.

            header('Location: index.php');
            // Redirige al usuario a la página index.php tras un inicio de sesión exitoso.

            exit;
            // Detiene la ejecución del script para evitar que se procese más código.

        } else {
            $message = "Correo o contraseña incorrectos.";
            $message_type = 'error';
            // Si no se encuentra el usuario o la contraseña no coincide, establece un mensaje de error.
        }

        $stmt->close();
        // Cierra la consulta preparada para liberar recursos.
    }
}

$db->close();
// Cierra la conexión a la base de datos para liberar recursos.
?>

<!DOCTYPE html>
<html lang="es">
<!-- Declara un documento HTML con el idioma español. -->

<head>
    <meta charset="UTF-8" />
    <!-- Establece la codificación de caracteres UTF-8 para soportar caracteres especiales. -->

    <title>Login y Registro</title>
    <!-- Define el título de la página que se muestra en la pestaña del navegador. -->

<style>
    /* Define estilos CSS para la interfaz de usuario. */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        /* Establece la fuente del texto. */
        background: #f3f4f6;
        /* Fondo gris claro para la página. */
        margin: 0;
        padding: 0;
        /* Elimina márgenes y relleno por defecto. */
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        padding-top: 50px;
        /* Centra el contenido vertical y horizontalmente con un espacio superior. */
    }

    .container {
        background: white;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
        /* Define un contenedor blanco con esquinas redondeadas, sombra y un ancho máximo. */
    }

    h2 {
        text-align: center;
        color: #111827;
        margin-bottom: 20px;
        /* Estiliza los títulos con texto centrado y color oscuro. */
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        /* Organiza los formularios en una columna con espacio entre elementos. */
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        transition: border 0.3s;
        /* Estiliza los campos de entrada con bordes, esquinas redondeadas y transición suave. */
    }

    input:focus {
        border-color: #2563eb;
        outline: none;
        /* Cambia el color del borde al enfocar un campo de entrada. */
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
        /* Estiliza los botones con fondo azul, texto blanco y transición suave. */
    }

    button:hover {
        background: #1e40af;
        /* Cambia el color del botón al pasar el ratón por encima. */
    }

    .message {
        text-align: center;
        margin-top: 15px;
        color: crimson;
        font-weight: bold;
        /* Estiliza los mensajes de error o éxito con texto centrado y color rojo. */
    }

    .message.success {
        color: green;
    }

    .divider {
        margin: 30px 0 15px;
        text-align: center;
        position: relative;
        font-size: 0.9rem;
        color: #6b7280;
        /* Estiliza el separador entre formularios con texto gris pequeño. */
    }

    .divider::before,
    .divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: #d1d5db;
        /* Crea líneas horizontales a ambos lados del texto del separador. */
    }

    .divider::before {
        left: 0;
        /* Posiciona la línea izquierda del separador. */
    }

    .divider::after {
        right: 0;
        /* Posiciona la línea derecha del separador. */
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
        <a href="admin/recuperar.php" style="font-size: 0.9rem; color: #2563eb;">¿Olvidaste tu contraseña?</a>
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
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
