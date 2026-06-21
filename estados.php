<?php
require_once "conexion.php";

// Transiciones válidas según las reglas del enunciado
$transicionesValidas = [
    'Pendiente'    => ['En progreso'],
    'En progreso'  => ['Pendiente', 'Bloqueada', 'Finalizada'],
    'Bloqueada'    => ['En progreso'],
    'Finalizada'   => [] // Solo se reactiva con la opción especial, no como cambio normal
];

$mensaje = "";

// Cambiar estado (respetando transiciones válidas)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_estado') {
    $id = intval($_POST['id']);
    $nuevoEstado = $_POST['nuevo_estado'];

    // Obtener estado actual
    $stmt = $conexion->prepare("SELECT estado FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($res) {
        $estadoActual = $res['estado'];

        if (in_array($nuevoEstado, $transicionesValidas[$estadoActual])) {
            if ($nuevoEstado === 'Finalizada') {
                // Al finalizar, se guarda la fecha de finalización
                $stmt = $conexion->prepare("UPDATE tareas SET estado = ?, fecha_finalizacion = NOW() WHERE id = ?");
                $stmt->bind_param("si", $nuevoEstado, $id);
            } else {
                $stmt = $conexion->prepare("UPDATE tareas SET estado = ? WHERE id = ?");
                $stmt->bind_param("si", $nuevoEstado, $id);
            }
            $stmt->execute();
            $stmt->close();
            $mensaje = "Estado actualizado correctamente.";
        } else {
            $mensaje = "Transición inválida: no se puede pasar de '$estadoActual' a '$nuevoEstado'.";
        }
    }
}

// Reactivar tarea (vuelve a Pendiente y borra fecha_finalizacion)
if (isset($_GET['reactivar'])) {
    $id = intval($_GET['reactivar']);
    $stmt = $conexion->prepare("UPDATE tareas SET estado = 'Pendiente', fecha_finalizacion = NULL WHERE id = ? AND estado = 'Finalizada'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: estados.php");
    exit;
}

// Listado de tareas con su estado actual
$tareas = $conexion->query("
    SELECT t.*, CONCAT(r.nombre, ' ', r.apellidos) AS responsable
    FROM tareas t
    LEFT JOIN responsables r ON t.id_responsable = r.id
    ORDER BY (t.estado = 'Finalizada') ASC, t.id DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estados de Tareas - Control de Tareas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f4f4; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #333; color: #fff; }
        .tachado { text-decoration: line-through; color: #888; }
        select { padding: 5px; }
        .btn { padding: 5px 10px; text-decoration: none; color: #fff; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; }
        .btn-cambiar { background: #007bff; }
        .btn-reactivar { background: #ffc107; color: #000; }
        .mensaje { background: #fff3cd; padding: 10px; border: 1px solid #ffc107; margin-bottom: 15px; border-radius: 4px; }
        .badge { padding: 3px 8px; border-radius: 4px; color: #fff; font-size: 12px; }
        .Pendiente { background: #6c757d; }
        .En-progreso { background: #007bff; }
        .Bloqueada { background: #dc3545; }
        .Finalizada { background: #28a745; }
        nav a { margin-right: 15px; }
    </style>
</head>
<body>

<nav>
    <a href="index.php">Inicio</a>
    <a href="tareas.php">Tareas</a>
    <a href="grupos.php">Grupos</a>
    <a href="estados.php"><strong>Estados</strong></a>
</nav>

<h1>Cambio de Estados de Tareas</h1>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<table>
    <tr>
        <th>Detalle</th>
        <th>Responsable</th>
        <th>Estado actual</th>
        <th>Fecha finalización</th>
        <th>Cambiar a</th>
        <th>Acciones</th>
    </tr>
    <?php if ($tareas && $tareas->num_rows > 0): ?>
        <?php while ($t = $tareas->fetch_assoc()): ?>
        <?php
            $estadoActual = $t['estado'];
            $claseEstado = str_replace(' ', '-', $estadoActual);
            $opciones = $transicionesValidas[$estadoActual];
        ?>
        <tr>
            <td class="<?= $estadoActual === 'Finalizada' ? 'tachado' : '' ?>">
                <?= htmlspecialchars($t['detalle']) ?>
            </td>
            <td><?= $t['responsable'] ?: 'Sin responsable asignado' ?></td>
            <td><span class="badge <?= $claseEstado ?>"><?= $estadoActual ?></span></td>
            <td><?= $t['fecha_finalizacion'] ?: '-' ?></td>
            <td>
                <?php if (count($opciones) > 0): ?>
                <form method="POST" style="display:flex; gap:5px;">
                    <input type="hidden" name="accion" value="cambiar_estado">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <select name="nuevo_estado">
                        <?php foreach ($opciones as $op): ?>
                            <option value="<?= $op ?>"><?= $op ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-cambiar">Cambiar</button>
                </form>
                <?php else: ?>
                    <em>Sin transiciones</em>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($estadoActual === 'Finalizada'): ?>
                    <a href="estados.php?reactivar=<?= $t['id'] ?>" class="btn btn-reactivar" onclick="return confirm('¿Reactivar esta tarea?');">Reactivar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6">No hay tareas registradas.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>