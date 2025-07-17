<?php
header('Content-Type: application/json; charset=utf-8');

$conexion = new mysqli("localhost", "root", "", "ventacoches");
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Error de conexión: " . $conexion->connect_error]);
  exit;
}

$sql = "SELECT marca, modelo, año, precio, kilometraje, combustible, fecha_publicacion, imagen, estado FROM coches WHERE estado = 'Disponible' ORDER BY fecha_publicacion DESC LIMIT 36";
$resultado = $conexion->query($sql);

if (!$resultado) {
  http_response_code(500);
  echo json_encode(["error" => "Error en la consulta: " . $conexion->error]);
  exit;
}

$coches = [];

while ($fila = $resultado->fetch_assoc()) {
  $coches[] = $fila;
}

echo json_encode($coches);
exit;