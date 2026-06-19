<?php
require_once '../consultas/tareas.php';

$tareas = obtenerTareas($conn);

// Manejar finalizar y reactivar
if (isset($_GET['finalizar'])) {
    $id = $_GET['finalizar'];
    $fecha = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE tareas SET estado = 'Finalizada', fecha_finalizacion = ? WHERE id = ? AND estado = 'En progreso'");
    $stmt->bind_param("si", $fecha, $id);
    $stmt->execute();
    header("Location: listar.php");
    exit;
}

if (isset($_GET['reactivar'])) {
    $id = $_GET['reactivar'];
    $stmt = $conn->prepare("UPDATE tareas SET estado = 'Pendiente', fecha_finalizacion = NULL WHERE id = ? AND estado = 'Finalizada'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: listar.php");
    exit;
}

include '../layout.php';
?>

<div class="contenedor">
    <h1>Lista de Tareas</h1>
    <a href="crear.php" class="btn btn-primario">+ Nueva Tarea</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Detalle</th>
            <th>Prioridad</th>
            <th>Estado</th>
            <th>Fecha límite</th>
            <th>Responsable</th>
            <th>Acciones</th>
        </tr>

        <?php foreach ($tareas as $tarea) { ?>
        <tr>
            <td><?php echo $tarea['id']; ?></td>
            <td>
                <?php if ($tarea['estado'] == 'Finalizada') { ?>
                    <span class="tachado"><?php echo $tarea['detalle']; ?></span>
                <?php } else { ?>
                    <?php echo $tarea['detalle']; ?>
                <?php } ?>
            </td>
            <td>
                <?php
                $clasePrioridad = [
                    'Baja'    => 'badge-baja',
                    'Media'   => 'badge-media',
                    'Alta'    => 'badge-alta',
                    'Urgente' => 'badge-urgente'
                ];
                ?>
                <span class="badge <?php echo $clasePrioridad[$tarea['prioridad']]; ?>">
                    <?php echo $tarea['prioridad']; ?>
                </span>
            </td>
            <td>
                <?php
                $claseEstado = [
                    'Pendiente'   => 'badge-pendiente',
                    'En progreso' => 'badge-progreso',
                    'Bloqueada'   => 'badge-bloqueada',
                    'Finalizada'  => 'badge-finalizada'
                ];
                ?>
                <span class="badge <?php echo $claseEstado[$tarea['estado']]; ?>">
                    <?php echo $tarea['estado']; ?>
                </span>
            </td>
            <td><?php echo $tarea['fecha_limite'] ?? 'Sin fecha'; ?></td>
            <td><?php echo $tarea['responsable'] ?? 'Sin responsable asignado'; ?></td>
            <td class="acciones">
                <?php if ($tarea['estado'] == 'En progreso') { ?>
                    <a href="listar.php?finalizar=<?php echo $tarea['id']; ?>" class="btn btn-primario" onclick="return confirm('¿Marcar como finalizada?')">Finalizar</a>
                <?php } ?>
                <?php if ($tarea['estado'] == 'Finalizada') { ?>
                    <a href="listar.php?reactivar=<?php echo $tarea['id']; ?>" class="btn btn-secundario" onclick="return confirm('¿Reactivar esta tarea?')">Reactivar</a>
                <?php } ?>
                <a href="editar.php?id=<?php echo $tarea['id']; ?>" class="btn btn-secundario">Editar</a>
                <a href="eliminar.php?id=<?php echo $tarea['id']; ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</main>
</body>
</html>