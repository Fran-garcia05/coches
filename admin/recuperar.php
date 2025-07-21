<?php
// Indica que el código que sigue es PHP y será procesado por el servidor.

require_once '../config/database.php';
// Incluye el archivo config/database.php, ubicado en el directorio superior,
// que contiene la configuración o clase para conectar con la base de datos.

$db = new Database();
// Crea una instancia de la clase Database para gestionar la conexión a la base de datos.

$conn = $db->getConexion();
// Obtiene la conexión a la base de datos (objeto mysqli) desde la clase Database
// y la almacena en la variable $conn.

$message = '';
// Inicializa una variable $message vacía para almacenar mensajes de error
// que se mostrarán al usuario en la interfaz.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Verifica si la solicitud HTTP es de tipo POST (es decir, si se envió el formulario).

    $email = trim($_POST['email']);
    // Obtiene el valor del campo email del formulario y elimina espacios en blanco
    // al inicio y al final usando trim().

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    // Prepara una consulta SQL para verificar si el correo existe en la tabla usuarios.
    // El ? es un marcador de posición para el email.

    $stmt->bind_param("s", $email);
    // Vincula el valor de $email al marcador de posición (?).
    // "s" indica que es una cadena (string).

    $stmt->execute();
    // Ejecuta la consulta preparada para buscar el email en la base de datos.

    $stmt->store_result();
    // Almacena el resultado de la consulta para verificar el número de filas.

    if ($stmt->num_rows > 0) {
    // Si se encuentra al menos un usuario con el email proporcionado:

        $token = bin2hex(random_bytes(16));
        // Genera un token aleatorio de 32 caracteres (16 bytes en hexadecimal)
        // para usar en el proceso de recuperación de contraseña.

        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));
        // Calcula la fecha y hora de expiración del token (1 hora desde ahora)
        // en formato 'YYYY-MM-DD HH:MM:SS'.

        $stmt2 = $conn->prepare("UPDATE usuarios SET token_password = ?, token_expira = ? WHERE email = ?");
        // Prepara una consulta SQL para actualizar el token y la fecha de expiración
        // en la tabla usuarios para el email proporcionado.

        $stmt2->bind_param("sss", $token, $expira, $email);
        // Vincula los valores de $token, $expira y $email a los marcadores de posición.
        // "sss" indica que todos son cadenas (strings).

        $stmt2->execute();
        // Ejecuta la consulta para guardar el token y la fecha de expiración en la base de datos.

        header("Location: resetpassword.php?token=$token");
        // Redirige al usuario a la página resetpassword.php, pasando el token como parámetro en la URL.

        exit;
        // Detiene la ejecución del script para evitar que se procese más código.

    } else {
        $message = "El correo no está registrado.";
        // Si no se encuentra un usuario con el email, establece un mensaje de error.
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<!-- Declara un documento HTML con el idioma español. -->

<head>
    <meta charset="UTF-8">
    <!-- Establece la codificación de caracteres UTF-8 para soportar caracteres especiales. -->

    <title>Recuperar contraseña</title>
    <!-- Define el título de la página que se muestra en la pestaña del navegador. -->

<style>
    /* Define estilos CSS para la interfaz de usuario. */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      /* Establece la fuente del texto. */
      background: #f3f4f6;
      /* Fondo gris claro para la página. */
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      /* Centra el contenido vertical y horizontalmente, ocupando toda la altura de la ventana. */
    }

    .container {
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      /* Define un contenedor blanco con esquinas redondeadas, sombra y un ancho máximo. */
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #111827;
      /* Estiliza el título con texto centrado y color oscuro. */
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      /* Organiza el formulario en una columna con espacio entre elementos. */
    }

    input[type="email"],
    input[type="password"] {
      padding: 12px;
      font-size: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      transition: border-color 0.3s;
      /* Estiliza los campos de entrada con bordes, esquinas redondeadas y transición suave. */
    }

    input:focus {
      border-color: #2563eb;
      outline: none;
      /* Cambia el color del borde al enfocar un campo de entrada. */
    }

    button {
      padding: 12px;
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
      /* Estiliza el botón con fondo azul, texto blanco y transición suave. */
    }

    button:hover {
      background-color: #1e3a8a;
      /* Cambia el color del botón al pasar el ratón por encima. */
    }

    .message {
      text-align: center;
      margin-top: 15px;
      color: crimson;
      font-weight: bold;
      /* Estiliza los mensajes de error con texto centrado y color rojo. */
    }

    .success {
      color: green;
      /* Define un estilo para mensajes de éxito con color verde. */
    }

    a {
      display: block;
      text-align: center;
      margin-top: 15px;
      text-decoration: none;
      color: #2563eb;
      /* Estiliza los enlaces con color azul y sin subrayado por defecto. */
    }

    a:hover {
      text-decoration: underline;
      /* Añade un subrayado al pasar el ratón por encima del enlace. */
    }
</style>
</head>

<body>
    <!-- Comienza el contenido visible de la página HTML. -->

    <div class="container">
    <!-- Contenedor principal para el formulario de recuperación de contraseña. -->

    <h2>Recuperar contraseña</h2>
    <!-- Título del formulario. -->

    <form method="post">
        <!-- Define un formulario que envía datos mediante el método POST. -->

        <input type="email" name="email" placeholder="Tu correo" required>
        <!-- Campo para ingresar el correo electrónico, marcado como obligatorio. -->

        <button type="submit">Continuar</button>
        <!-- Botón para enviar el formulario y procesar la solicitud de recuperación. -->

    </form>

    <?php if ($message): ?>
        <!-- Si hay un mensaje de error (por ejemplo, correo no registrado),
             lo muestra en la página. -->

        <p style="color:red;"><?php echo $message; ?></p>
        <!-- Muestra el mensaje de error en rojo. -->

    <?php endif; ?>
    </div>
</body>
</html>