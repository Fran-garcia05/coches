<?php
ob_clean();
// procesarCompra.php
header('Content-Type: application/json; charset=utf-8');

// --- INICIO DE CONFIGURACIÓN DE LOGGING TEMPORAL ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp2/htdocs/coches/php_custom_error.log'); // Ruta al nuevo log
// --- FIN DE CONFIGURACIÓN DE LOGGING TEMPORAL ---

// --- INICIO DE DEPURACIÓN DE HEADERS ---
error_log("procesarCompra.php: Content-Type recibido = " . ($_SERVER['CONTENT_TYPE'] ?? 'No Content-Type'));
// --- FIN DE DEPURACIÓN DE HEADERS ---

require_once 'config/database.php'; // tu clase o conexión a la base

// Recibir datos JSON
$rawInput = file_get_contents('php://input'); // Obtener la entrada RAW
error_log("procesarCompra.php: Raw input = " . var_export($rawInput, true)); // Registrar la entrada RAW
$data = json_decode($rawInput, true);

// --- INICIO DE DEPURACIÓN ---
error_log("procesarCompra.php: Datos recibidos (var_export): " . var_export($data, true));
// --- FIN DE DEPURACIÓN ---

$idUsuario = $data['id_usuario'] ?? null;
$idCoche = $data['id_coche'] ?? null;
$precioCoche = isset($data['precio_coche']) ? (float)$data['precio_coche'] : null;

if (!$idUsuario || !$idCoche) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos para procesar la compra."]);
    exit;
}

$db = new Database();
$conexion = $db->getConexion();

// Insertar compra
$sqlInsert = "INSERT INTO compras (id_usuario, id_coche, precio_coche) VALUES (?, ?, ?)";
$stmtInsert = $conexion->prepare($sqlInsert);

$stmtInsert->bind_param("iid", $idUsuario, $idCoche, $precioCoche);

if ($stmtInsert->execute()) {
    // Actualizar estado coche a 'vendido'
    $sqlUpdate = "UPDATE coches SET estado = 'vendido' WHERE id = ?";
    $stmtUpdate = $conexion->prepare($sqlUpdate);
    $stmtUpdate->bind_param("i", $idCoche);
    $stmtUpdate->execute();

    echo json_encode(["mensaje" => "Compra registrada correctamente."]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al registrar la compra: " . $conexion->error]);
}

$stmtInsert->close();
$stmtUpdate->close();
$conexion->close();
