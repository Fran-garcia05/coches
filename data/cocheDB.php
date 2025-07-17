<?php
/**
 * Se encarga de interactuar con la base de datos
 */
class CocheDB {
   private $db;
   private $table = 'coches';
   //recibe una conexión ($database)a una base de datos y la mete en $db
   

   public function __construct($database){
    $this->db = $database->getConexion();
   }
   //extrae todos los datos de la tabla $table 
   public function getAll(){
    //construye la consulta
    $sql = "SELECT * FROM {$this->table} ";
    //realiza la consulta con la función query()
    $resultado = $this->db->query($sql);
    //comprueba si hay respuesta ($resultado) y si la respuesta viene con datos
    if($resultado && $resultado->num_rows > 0){
        //crea un array para guardar los datos
        $coches = [];
        //en cada vuelta obtengo un array asociativo con los datos de una
        //fila y lo guardo en la variable $row
        //cuando ya no quedan filas que recorrer termina el bucle
        while($row = $resultado->fetch_assoc()){
            //al array libros le añado $row
            $coches[] = $row;
        }
        //devolvemos el resultado
        return $coches;
    }else{
    return [];
    }
   }
}