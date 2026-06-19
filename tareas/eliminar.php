<?php
require_once '../consultas/tareas.php';

$id = $_GET['id'];
eliminarTarea($conn, $id);

header("Location: listar.php");
exit;
?>