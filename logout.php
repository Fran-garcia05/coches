<?php
// Indica que el código que sigue es PHP y será procesado por el servidor.

session_start();
// Inicia la sesión PHP para acceder a los datos almacenados en $_SESSION,
// permitiendo manipular o eliminar la información de la sesión actual.

session_unset();
// Elimina todas las variables de sesión almacenadas en $_SESSION,
// limpiando cualquier dato asociado con la sesión del usuario (como id_usuario o nombre).

session_destroy();
// Destruye la sesión actual por completo, eliminando el archivo de sesión en el servidor
// y haciendo que la sesión quede inválida para futuras solicitudes.

header("Location: login.php");
// Establece una cabecera HTTP para redirigir al usuario a la página login.php,
// que probablemente contiene el formulario de inicio de sesión.

exit();
// Detiene la ejecución del script inmediatamente para asegurar que no se procese
// ningún código adicional después de la redirección.
?>