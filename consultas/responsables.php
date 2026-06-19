<?php

// importar la conexion a la base de datos
require_once __DIR__ . "/../conexion.php";

// obtener todos los responsables
function listarResponsables()
{
    global $conn;

    $sql = "SELECT * FROM responsables ORDER BY id DESC";

    return $conn->query($sql);
}

// obtener un responsable por id
function obtenerResponsablePorId($id)
{
    global $conn;

    $sql = "SELECT * FROM responsables WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

// crear un nuevo responsable
function crearResponsable($identificacion, $nombre, $apellidos)
{
    global $conn;

    $sql = "INSERT INTO responsables (identificacion, nombre, apellidos)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $identificacion, $nombre, $apellidos);

    return $stmt->execute();
}

// actualizar los datos de un responsable
function editarResponsable($id, $identificacion, $nombre, $apellidos)
{
    global $conn;

    $sql = "UPDATE responsables
            SET identificacion = ?, nombre = ?, apellidos = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $identificacion, $nombre, $apellidos, $id);

    return $stmt->execute();
}

// eliminar un responsable por id
function eliminarResponsable($id)
{
    global $conn;

    $sql = "DELETE FROM responsables WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}