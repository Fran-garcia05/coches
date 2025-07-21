<?php
// Indica que el código que sigue es PHP y será procesado por el servidor.

// Importamos el archivo config.php
require_once 'config.php';
// Incluye el archivo config.php, que contiene las constantes de configuración
// (como DB_HOST, DB_USER, DB_PASS, DB_NAME) necesarias para la conexión a la base de datos.

// Clase para establecer la conexión con la base de datos
class Database {
    // Definimos los atributos
    // Les ponemos el valor de las constantes de config.php
    private $host = DB_HOST;
    // Almacena el nombre del servidor de la base de datos (por ejemplo, 'localhost')
    // definido en la constante DB_HOST del archivo config.php.

    private $username = DB_USER;
    // Almacena el nombre de usuario para la base de datos
    // definido en la constante DB_USER del archivo config.php.

    private $password = DB_PASS;
    // Almacena la contraseña del usuario de la base de datos
    // definida en la constante DB_PASS del archivo config.php.

    private $database = DB_NAME;
    // Almacena el nombre de la base de datos
    // definido en la constante DB_NAME del archivo config.php.

    // Guarda la conexión con la base de datos
    // La conexión con la base de datos es un objeto de tipo mysqli
    private $conexion;
    // Declara una propiedad privada para almacenar el objeto de conexión mysqli,
    // que se usará para interactuar con la base de datos.

    public function __construct()
    // Constructor de la clase, que se ejecuta automáticamente al crear una instancia de Database.
    {
        $this->connect();
        // Llama al método privado connect() para establecer la conexión con la base de datos
        // cuando se crea un objeto de esta clase.
    }

    // Abre la conexión con la base de datos
    private function connect(){
        $this->conexion = new mysqli($this->host, $this->username, $this->password, $this->database);
        // Crea una nueva conexión a la base de datos usando la clase mysqli,
        // pasando el host, usuario, contraseña y nombre de la base de datos.

        if($this->conexion->connect_error){
            // Verifica si hubo un error al intentar conectar con la base de datos.
            die("Error de conexión: " . $this->conexion->connect_error);
            // Si hay un error, termina la ejecución del script y muestra el mensaje de error
            // proporcionado por connect_error (por ejemplo, "Access denied for user").
        }

        $this->conexion->set_charset("utf8");
        // Configura el conjunto de caracteres de la conexión a UTF-8,
        // asegurando que los datos con caracteres especiales (como tildes o ñ) se manejen correctamente.
    }

    public function getConexion(){
        // Método público que permite obtener el objeto de conexión mysqli.
        return $this->conexion;
        // Devuelve la propiedad $conexion para que otros scripts puedan usarla
        // para ejecutar consultas a la base de datos.
    }

    public function close(){
        // Método público para cerrar la conexión con la base de datos.
        if($this->conexion){
            // Verifica si la conexión existe (no es null).
            $this->conexion->close();
            // Cierra la conexión con la base de datos, liberando los recursos del servidor.
        }
    }
}
?>