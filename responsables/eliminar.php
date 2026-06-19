<?php

// importar funciones de responsables
require_once "../consultas/responsables.php";

// obtener id enviado por url
$id = $_GET["id"];

// eliminar responsable
eliminarResponsable($id);

// regresar al listado
header("Location: listar.php");
exit;