<?php
require_once '../conexion.php';

function obtenerTareas($conn) {
    $resultado = $conn->query("SELECT t.*, CONCAT(r.nombre, ' ', r.apellidos) AS responsable FROM tareas t
        LEFT JOIN responsables r ON t.id_responsable = r.id
        ORDER BY (t.estado = 'Finalizada') ASC, t.id DESC
    ");
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

function obtenerTareaPorId($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function crearTarea($conn, $detalle, $prioridad, $fecha_limite, $id_responsable, $id_grupo) {
    $stmt = $conn->prepare("INSERT INTO tareas (detalle, prioridad, fecha_limite, id_responsable, id_grupo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $detalle, $prioridad, $fecha_limite, $id_responsable, $id_grupo);
    $stmt->execute();
}

function editarTarea($conn, $id, $detalle, $prioridad, $fecha_limite, $id_responsable, $id_grupo, $estado, $fecha_finalizacion) {
    $stmt = $conn->prepare("UPDATE tareas SET detalle = ?, prioridad = ?, fecha_limite = ?, id_responsable = ?, id_grupo = ?, estado = ?, fecha_finalizacion = ? WHERE id = ?");
    $stmt->bind_param("sssiissi", $detalle, $prioridad, $fecha_limite, $id_responsable, $id_grupo, $estado, $fecha_finalizacion, $id);
    $stmt->execute();
}

function eliminarTarea($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}