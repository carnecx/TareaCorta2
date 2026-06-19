<?php

// importar las funciones de responsables
require_once "../consultas/responsables.php";

// obtener la lista de responsables
$responsables = listarResponsables();

// cargar layout
require_once "../layout.php";

?>

<h1>Lista de Responsables</h1>

<a href="crear.php">
    Crear Responsable
</a>

<br><br>

<table>

    <tr>
        <th>ID</th>
        <th>Identificacion</th>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>Acciones</th>
    </tr>

    <?php while ($responsable = $responsables->fetch_assoc()) : ?>

        <tr>

            <td><?= $responsable['id'] ?></td>

            <td><?= $responsable['identificacion'] ?></td>

            <td><?= $responsable['nombre'] ?></td>

            <td><?= $responsable['apellidos'] ?></td>

            <td>

                <a href="editar.php?id=<?= $responsable['id'] ?>">
                    Editar
                </a>

                |

                <a href="eliminar.php?id=<?= $responsable['id'] ?>"
                    onclick="return confirm('Desea eliminar este responsable?')">
                    Eliminar
                </a>

            </td>

        </tr>

    <?php endwhile; ?>

</table>

<?php require_once "../footer.php"; ?>