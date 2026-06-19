<?php

// importar funciones
require_once "../consultas/responsables.php";

// guardar responsable
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $identificacion = $_POST["identificacion"];
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];

    crearResponsable($identificacion, $nombre, $apellidos);

    header("Location: listar.php");
    exit;
}

// cargar layout
require_once "../layout.php";

?>

<h1>Crear Responsable</h1>

<form method="POST" class="formulario">

    <label>Identificacion</label>
    <input type="text" name="identificacion" required>

    <label>Nombre</label>
    <input type="text" name="nombre" required>

    <label>Apellidos</label>
    <input type="text" name="apellidos" required>

    <button type="submit">
        Guardar
    </button>

</form>

<br>

<a href="listar.php" class="btn-volver">
    Volver
</a>

<?php require_once "../footer.php"; ?>