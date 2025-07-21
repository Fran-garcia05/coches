<?php
// Indica que el código que sigue es PHP y será procesado por el servidor.

session_start();
// Inicia una sesión PHP para acceder a los datos almacenados en $_SESSION,
// como el ID del usuario, que persiste mientras el usuario está autenticado.

header('Content-Type: application/json');
// Establece la cabecera HTTP para indicar que la respuesta será en formato JSON,
// lo que permite que el cliente (como un navegador o una aplicación) procese los datos correctamente.

if (isset($_SESSION['id_usuario'])) {
// Verifica si la clave 'id_usuario' existe en la sesión, lo que indica que un usuario
// está autenticado (ha iniciado sesión previamente).

    echo json_encode(['id_usuario' => $_SESSION['id_usuario']]);
    // Si el usuario está autenticado, devuelve un objeto JSON con el ID del usuario.
    // json_encode convierte el array asociativo ['id_usuario' => valor] en una cadena JSON,
    // por ejemplo: {"id_usuario": 123}.

} else {
    // Si no hay un usuario autenticado (es decir, 'id_usuario' no está definido en la sesión):

    echo json_encode(['id_usuario' => null]);
    // Devuelve un objeto JSON con id_usuario establecido a null, indicando que no hay
    // un usuario autenticado. Podría usarse un mensaje de error en su lugar si se desea.

}
?>