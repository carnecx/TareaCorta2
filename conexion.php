<?php
<<<<<<< Updated upstream

$host     = "localhost";
$dbname   = "control_tareas";
$usuario  = "root";
$password = "";

$conn = new mysqli($host, $usuario, $password, $dbname);

if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
=======
$host = "localhost";
$puerto = "3307";
$usuario = "root";
$password = "12345";
$baseDatos = "control_tareas";

$conexion = new mysqli($host, $usuario, $password, $baseDatos, $puerto);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
>>>>>>> Stashed changes
