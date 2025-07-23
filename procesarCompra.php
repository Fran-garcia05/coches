<?php
// Indica que el código que sigue es PHP y será procesado por el servidor.

ob_clean();
// Limpia cualquier salida previa en el buffer de salida para evitar que contenido no deseado
// (como mensajes de error o espacios) interfiera con la respuesta JSON.

header('Content-Type: application/json; charset=utf-8');
// Establece la cabecera HTTP para indicar que la respuesta será en formato JSON
// y que usa codificación UTF-8 para soportar caracteres especiales (como tildes o ñ).

// --- INICIO DE CONFIGURACIÓN DE LOGGING TEMPORAL ---
ini_set('display_errors', 1);
// Habilita la visualización de errores en pantalla (útil para depuración, pero no en producción).

ini_set('display_startup_errors', 1);
// Habilita la visualización de errores de inicio de PHP (por ejemplo, problemas al cargar extensiones).

error_reporting(E_ALL);
// Configura PHP para reportar todos los tipos de errores (máximo nivel de detalle).

ini_set('log_errors', 1);
// Habilita el registro de errores en un archivo de log (en lugar de solo mostrarlos en pantalla).

ini_set('error_log', 'C:/xampp2/htdocs/coches/php_custom_error.log');
// Define la ruta del archivo donde se guardarán los errores (en este caso, un archivo personalizado).
// --- FIN DE CONFIGURACIÓN DE LOGGING TEMPORAL ---

// --- INICIO DE DEPURACIÓN DE HEADERS ---
error_log("procesarCompra.php: Content-Type recibido = " . ($_SERVER['CONTENT_TYPE'] ?? 'No Content-Type'));
// Registra en el archivo de log el valor del encabezado Content-Type recibido en la solicitud HTTP.
// Usa el operador ?? para manejar el caso en que no se reciba un Content-Type.
// --- FIN DE DEPURACIÓN DE HEADERS ---

require_once 'config/database.php';
// Incluye un archivo externo (config/database.php) que contiene la configuración o clase
// para conectar con la base de datos. Se asume que define una clase Database.

// Recibir datos JSON
$rawInput = file_get_contents('php://input');
// Lee los datos enviados en el cuerpo de la solicitud HTTP (en este caso, datos JSON)
// desde el flujo php://input, que captura la entrada "cruda" (raw).

error_log("procesarCompra.php: Raw input = " . var_export($rawInput, true));
// Registra en el archivo de log los datos crudos recibidos para depuración.
// var_export convierte los datos en una representación legible para el log.

$data = json_decode($rawInput, true);
// Convierte la cadena JSON recibida en un array asociativo de PHP.
// El parámetro true asegura que se obtenga un array en lugar de un objeto.

error_log("procesarCompra.php: Datos recibidos (var_export): " . var_export($data, true));
// Registra en el archivo de log los datos decodificados (el array $data) para depuración.

// Obtener datos específicos del array $data
$idUsuario = $data['id_usuario'] ?? null;
// Extrae el valor de id_usuario del array $data. Si no existe, asigna null.

$idCoche = $data['id_coche'] ?? null;
// Extrae el valor de id_coche del array $data. Si no existe, asigna null.

$precioCoche = isset($data['precio_coche']) ? (float)$data['precio_coche'] : null;
// Extrae el valor de precio_coche y lo convierte a un número decimal (float).
// Si no existe, asigna null. isset verifica que la clave exista y no sea null.

if (!$idUsuario || !$idCoche) {
// Verifica si idUsuario o idCoche están vacíos o son null.
// Esto asegura que los datos mínimos necesarios para procesar la compra estén presentes.

    http_response_code(400);
    // Establece el código de estado HTTP a 400 (Solicitud Incorrecta),
    // indicando que faltan datos en la solicitud.

    echo json_encode(["error" => "Faltan datos para procesar la compra."]);
    // Envía una respuesta JSON con un mensaje de error indicando que faltan datos.

    exit;
    // Detiene la ejecución del script para no continuar si faltan datos.
}

$db = new Database();
// Crea una instancia de la clase Database (definida en config/database.php)
// para establecer la conexión con la base de datos.

$conexion = $db->getConexion();
// Obtiene la conexión a la base de datos desde el objeto $db
// y la almacena en la variable $conexion.

// Verificar si el coche ya está vendido
$sqlVerificar = "SELECT estado FROM coches WHERE id = ?";
// Crea una instrucción SQL que busca el "estado" (como "Vendido" o "Disponible") de un coche en la tabla "coches" usando su ID.
// El "?" es un marcador que luego se reemplazará por el ID real del coche.

$stmtVerificar = $conexion->prepare($sqlVerificar);
// Prepara la instrucción SQL para ejecutarla de forma segura.
// $conexion es un objeto que conecta con la base de datos (creado antes en el código).
// $stmtVerificar guarda la consulta lista para usar.

$stmtVerificar->bind_param("i", $idCoche);
// Vincula el ID del coche ($idCoche) al "?" de la consulta SQL.
// "i" significa que el ID es un número entero (integer).
// $idCoche es una variable que contiene el ID del coche que queremos verificar.

$stmtVerificar->execute();
// Ejecuta la consulta SQL en la base de datos para buscar el estado del coche con el ID dado.

$resultado = $stmtVerificar->get_result();
// Guarda el resultado de la consulta en la variable $resultado.
// Esto contiene la información que la base de datos devolvió (el estado del coche).

$coche = $resultado->fetch_assoc();
// Extrae la primera fila del resultado como un array (clave-valor, por ejemplo, ["estado" => "Vendido"]).
// $coche guarda los datos del coche; si no se encuentra el coche, $coche será null.

//mensaje para cuando alguien intente comprar un coche que ya ha sido vendido
if ($coche && strtolower($coche['estado']) === 'vendido') {
// Comprueba si el coche existe ($coche no es null) y si su estado en minúsculas es "vendido".
// strtolower() convierte el estado (como "Vendido" o "VENDIDO") a minúsculas para compararlo.

    http_response_code(409); // Conflict
    // Establece un código HTTP 409, que indica un conflicto (el coche ya está vendido).
    // Esto es útil para APIs que el navegador o JavaScript entenderán.

    echo json_encode(["error" => "Este coche ya ha sido vendido."]);
    // Envía un mensaje en formato JSON ({"error": "Este coche ya ha sido vendido."}) al navegador o JavaScript.
    // Indica que no se puede comprar porque el coche ya fue vendido.

    exit;
    // Detiene el código aquí para no seguir procesando nada más.
}

if ($coche && strtolower($coche['estado']) === 'reservado') {
// Comprueba si el coche existe ($coche no es null) y si su estado en minúsculas es "reservado".
// strtolower() asegura que el estado se compare correctamente (por ejemplo, "Reservado" o "RESERVADO").

    http_response_code(409); // Conflict
    // Establece un código HTTP 409 para indicar un conflicto (el coche está reservado).

    echo json_encode(["error" => "Este vehículo ya se encuentra reservado."]);
    // Envía un mensaje JSON ({"error": "Este vehículo ya se encuentra reservado."}) al navegador o JavaScript.
    // Indica que no se puede comprar porque el coche está reservado.

    exit;
    // Detiene el código para no seguir procesando.
}


// Insertar compra
$sqlInsert = "INSERT IGNORE INTO compras (id_usuario, id_coche, precio_coche) VALUES (?, ?, ?)";
// Define una consulta SQL para insertar un registro en la tabla "compras".
// INSERT IGNORE evita errores si el registro ya existe (por ejemplo, por claves únicas).
// Los signos ? son marcadores de posición para los valores que se vincularán más adelante.

$stmtInsert = $conexion->prepare($sqlInsert);
// Prepara la consulta SQL para su ejecución segura, previniendo inyecciones SQL.
// La consulta preparada se almacena en $stmtInsert.

$stmtInsert->bind_param("iid", $idUsuario, $idCoche, $precioCoche);
// Vincula los valores a los marcadores de posición (?) en la consulta.
// "iid" indica los tipos de datos: i (integer) para id_usuario, i (integer) para id_coche,
// y d (double/float) para precio_coche.

if ($stmtInsert->execute()) {
// Ejecuta la consulta preparada para insertar el registro en la tabla "compras".
// Si la ejecución es exitosa, continúa con la actualización del estado del coche.

    $sqlUpdate = "UPDATE coches SET estado = 'vendido' WHERE id = ?";
    // Define una consulta SQL para actualizar el estado del coche en la tabla "coches"
    // a "vendido" donde el id coincida con $idCoche.

    $stmtUpdate = $conexion->prepare($sqlUpdate);
    // Prepara la consulta de actualización para su ejecución segura.

    $stmtUpdate->bind_param("i", $idCoche);
    // Vincula el valor de $idCoche al marcador de posición (?) en la consulta.
    // "i" indica que es un entero (integer).

    $stmtUpdate->execute();
    // Ejecuta la consulta para actualizar el estado del coche a "vendido".

    echo json_encode(["mensaje" => "Compra registrada correctamente."]);
    // Envía una respuesta JSON indicando que la compra se registró correctamente.

    exit;
    // Detiene la ejecución del script tras enviar la respuesta exitosa.
} else {
    // Si la inserción falla, entra en este bloque.

    http_response_code(500);
    // Establece el código de estado HTTP a 500 (Error Interno del Servidor),
    // indicando un problema al procesar la compra.

    echo json_encode(["error" => "Error al registrar la compra: " . $conexion->error]);
    // Envía una respuesta JSON con el mensaje de error específico de la base de datos.
    // $conexion->error contiene el detalle del error.

}

$stmtInsert->close();
// Cierra la consulta preparada de inserción para liberar recursos.

$stmtUpdate->close();
// Cierra la consulta preparada de actualización para liberar recursos.

$conexion->close();
// Cierra la conexión a la base de datos para liberar recursos.