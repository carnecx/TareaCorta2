<?php

$host     = "localhost";
$dbname   = "control_tareas";
$usuario  = "root";
$password = "";

$conn = new mysqli($host, $usuario, $password, $dbname);

if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");