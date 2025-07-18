<?php
session_start(); // Inicia la sesión PHP

header('Content-Type: application/json');

if (isset($_SESSION['id_usuario'])) {
    echo json_encode(['id_usuario' => $_SESSION['id_usuario']]);
} else {
    echo json_encode(['id_usuario' => null]); // O un mensaje de error si prefieres
}
?>