<?php

// importar funciones de responsables
require_once "../consultas/responsables.php";

// obtener el id enviado por url
$id = $_GET["id"];

// buscar el responsable seleccionado
$responsable = obtenerResponsablePorId($id);

// si se envia el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $identificacion = $_POST["identificacion"];
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];

    // actualizar responsable
    editarResponsable($id, $identificacion, $nombre, $apellidos);

    // volver al listado
    header("Location: listar.php");
    exit;
}

// cargar layout
require_once "../layout.php";

?>

<h1>Editar Responsable</h1>

<form method="POST" class="formulario">

    <label>Identificacion</label>
    <input
        type="text"
        name="identificacion"
        value="<?= $responsable['identificacion'] ?>"
        required>

    <label>Nombre</label>
    <input
        type="text"
        name="nombre"
        value="<?= $responsable['nombre'] ?>"
        required>

    <label>Apellidos</label>
    <input
        type="text"
        name="apellidos"
        value="<?= $responsable['apellidos'] ?>"
        required>

    <button type="submit">
        Actualizar
    </button>

</form>

<br>

<a href="listar.php" class="btn-volver">
    Volver
</a>

<?php require_once "../footer.php"; ?>