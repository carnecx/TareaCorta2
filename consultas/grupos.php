<?php
require_once '../conexion.php';

function obtenerGrupos($conn) {
    $resultado = $conn->query("SELECT id, nombre FROM grupos ORDER BY nombre");
    return $resultado->fetch_all(MYSQLI_ASSOC);
}