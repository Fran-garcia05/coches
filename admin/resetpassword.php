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

$token = $_GET['token'] ?? '';
// Obtiene el valor del parámetro token de la URL (por ejemplo, reset_password.php?token=abc).
// Usa el operador ?? para asignar una cadena vacía si no se proporciona un token.

$message = '';
// Inicializa una variable $message vacía para almacenar mensajes de éxito o error
// que se mostrarán al usuario en la interfaz.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Verifica si la solicitud HTTP es de tipo POST (es decir, si se envió el formulario).

    $token = $_POST['token'];
    // Obtiene el valor del campo token enviado desde el formulario (input oculto).

    $newPassword = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);
    // Obtiene la nueva contraseña del campo new_password, elimina espacios con trim(),
    // y genera un hash seguro usando el algoritmo por defecto (bcrypt).

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_password = ? AND token_expira > NOW()");
    // Prepara una consulta SQL para buscar un usuario con el token proporcionado
    // y verificar que el token no haya expirado (token_expira debe ser mayor a la fecha actual).

    $stmt->bind_param("s", $token);
    // Vincula el valor de $token al marcador de posición (?).
    // "s" indica que es una cadena (string).

    $stmt->execute();
    // Ejecuta la consulta preparada para buscar el usuario con el token.

    $stmt->store_result();
    // Almacena el resultado de la consulta para verificar el número de filas.

    if ($stmt->num_rows === 1) {
    // Si se encuentra exactamente un usuario con el token válido y no expirado:

        $stmt2 = $conn->prepare("UPDATE usuarios SET password = ?, token_password = NULL, token_expira = NULL WHERE token_password = ?");
        // Prepara una consulta SQL para actualizar la contraseña del usuario,
        // y elimina el token_password y token_expira (los establece a NULL).

        $stmt2->bind_param("ss", $newPassword, $token);
        // Vincula los valores de $newPassword y $token a los marcadores de posición.
        // "ss" indica que ambos son cadenas (strings).

        $stmt2->execute();
        // Ejecuta la consulta para actualizar la contraseña y limpiar el token.

        $message = "Contraseña actualizada con éxito. <a href='../login.php'>Inicia sesión aquí</a>";
        // Establece un mensaje de éxito con un enlace para que el usuario inicie sesión.

    } else {
        $message = "Token inválido o expirado.";
        // Si no se encuentra un usuario o el token ha expirado, establece un mensaje de error.
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<!-- Declara un documento HTML con el idioma español. -->

<head>
    <meta charset="UTF-8">
    <!-- Establece la codificación de caracteres UTF-8 para soportar caracteres especiales. -->

    <title>Restablecer contraseña</title>
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
    <!-- Contenedor principal para el formulario de restablecimiento de contraseña. -->

    <h2>Restablecer contraseña</h2>
    <!-- Título del formulario. -->

    <?php if (!$message): ?>
    <!-- Si no hay mensaje (es decir, no se ha procesado el formulario o no hay error),
         muestra el formulario para ingresar la nueva contraseña. -->

    <form method="post">
        <!-- Define un formulario que envía datos mediante el método POST. -->

        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <!-- Campo oculto que incluye el token recibido desde la URL.
             htmlspecialchars protege contra ataques XSS al escapar caracteres especiales. -->

        <input type="password" name="new_password" placeholder="Nueva contraseña" required>
        <!-- Campo para ingresar la nueva contraseña, marcado como obligatorio. -->

        <button type="submit">Actualizar</button>
        <!-- Botón para enviar el formulario y actualizar la contraseña. -->

    </form>
    <?php else: ?>
        <!-- Si hay un mensaje (éxito o error), muestra el mensaje en lugar del formulario. -->

        <p><?php echo $message; ?></p>
        <!-- Muestra el mensaje (éxito o error) definido en el código PHP. -->

    <?php endif; ?>
    </div>
</body>
</html>