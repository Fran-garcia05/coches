
<?php
// Indica que el código que sigue es PHP y será procesado por el servidor.

header('Content-Type: application/json; charset=utf-8');
// Establece la cabecera HTTP para indicar que la respuesta será en formato JSON
// y que usa codificación UTF-8 para soportar caracteres especiales (como tildes o ñ).

$conexion = new mysqli("localhost", "root", "", "ventacoches");
// Crea una conexión a la base de datos MySQL. Los parámetros son:
// - "localhost": El servidor de la base de datos (en este caso, el mismo servidor).
// - "root": El nombre de usuario de la base de datos (usuario por defecto de MySQL).
// - "": La contraseña del usuario (vacía en este caso, sin contraseña).
// - "ventacoches": El nombre de la base de datos a la que se conecta.
// La conexión se guarda en la variable $conexion.

$conexion->set_charset("utf8");
// Configura la conexión para usar el conjunto de caracteres UTF-8,
// asegurando que los datos con caracteres especiales (como tildes) se manejen correctamente.

if ($conexion->connect_error) {
// Verifica si hubo un error al conectar con la base de datos.
// $conexion->connect_error contiene el mensaje de error si la conexión falla.

  http_response_code(500);
  // Si hay un error, establece el código de estado HTTP a 500
  // (Error Interno del Servidor), indicando un problema en el servidor.

  echo json_encode(["error" => "Error de conexión: " . $conexion->connect_error]);
  // Envía una respuesta en formato JSON con un mensaje de error.
  // json_encode convierte el array PHP ["error" => mensaje] en una cadena JSON,
  // por ejemplo: {"error": "Error de conexión: mensaje de error"}.

  exit;
  // Detiene la ejecución del script para no continuar si hay un error.
}

$sql = "SELECT id, marca, modelo, año, precio, kilometraje, combustible, fecha_publicacion, imagen, estado FROM coches ORDER BY fecha_publicacion DESC LIMIT 36";
// Define una consulta SQL que selecciona columnas específicas (id, marca, modelo, etc.)
// de la tabla "coches" en la base de datos. Ordena los resultados por fecha de publicación
// (de más reciente a más antigua con DESC) y limita la respuesta a 36 registros.

$resultado = $conexion->query($sql);
// Ejecuta la consulta SQL en la base de datos usando la conexión establecida.
// El resultado se guarda en la variable $resultado, que contiene los datos obtenidos
// o un valor falso si la consulta falla.

if (!$resultado) {
// Verifica si la consulta falló (por ejemplo, si hay un error en la sintaxis SQL).
// $resultado será falso si la consulta no se ejecutó correctamente.

  http_response_code(500);
  // Establece el código de estado HTTP a 500 (Error Interno del Servidor)
  // para indicar que la consulta falló.

  echo json_encode(["error" => "Error en la consulta: " . $conexion->error]);
  // Envía una respuesta en formato JSON con el mensaje de error de la consulta.
  // $conexion->error contiene el mensaje específico del error en la consulta.

  exit;
  // Detiene la ejecución del script para no continuar si hay un error.
}

$coches = [];
// Crea un array vacío llamado $coches que almacenará los datos de los coches
// obtenidos de la base de datos.

while ($fila = $resultado->fetch_assoc()) {
// Itera sobre los resultados de la consulta, obteniendo cada fila como un array asociativo.
// fetch_assoc() devuelve cada fila con claves que corresponden a los nombres de las columnas
// (como id, marca, modelo, etc.) y avanza al siguiente registro en cada iteración.

  $coches[] = $fila;
  // Agrega la fila actual (un coche con sus datos) al array $coches.
}

echo json_encode($coches);
// Convierte el array $coches (que contiene todos los registros de la consulta)
// en una cadena JSON y la envía como respuesta al cliente.
// Por ejemplo, [{"id": 1, "marca": "Toyota", ...}, {"id": 2, "marca": "Ford", ...}].

exit;
// Detiene la ejecución del script después de enviar la respuesta JSON.