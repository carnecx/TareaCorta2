<?php
require_once "conexion.php";

// Eliminar grupo
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("DELETE FROM grupos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: grupos.php");
    exit;
}

// Crear grupo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        $stmt = $conexion->prepare("INSERT INTO grupos (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $idGrupo = $conexion->insert_id;
        $stmt->close();

        // Asociar tareas pendientes seleccionadas (checkboxes), opcional
        if (isset($_POST['tareas']) && is_array($_POST['tareas'])) {
            $stmtUpd = $conexion->prepare("UPDATE tareas SET id_grupo = ? WHERE id = ?");
            foreach ($_POST['tareas'] as $idTarea) {
                $idTarea = intval($idTarea);
                $stmtUpd->bind_param("ii", $idGrupo, $idTarea);
                $stmtUpd->execute();
            }
            $stmtUpd->close();
        }
    }
    header("Location: grupos.php");
    exit;
}

// Editar grupo (cambiar nombre)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    if ($nombre !== '') {
        $stmt = $conexion->prepare("UPDATE grupos SET nombre = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: grupos.php");
    exit;
}

// Obtener todos los grupos
$grupos = $conexion->query("SELECT * FROM grupos ORDER BY nombre");

// Obtener tareas pendientes (para el checklist al crear grupo)
$tareasPendientes = $conexion->query("SELECT id, detalle FROM tareas WHERE estado = 'Pendiente' ORDER BY id DESC");

// Si se seleccionó un grupo para ver sus tareas
$grupoSeleccionado = null;
$tareasDelGrupo = null;
if (isset($_GET['ver'])) {
    $idVer = intval($_GET['ver']);
    $stmt = $conexion->prepare("SELECT * FROM grupos WHERE id = ?");
    $stmt->bind_param("i", $idVer);
    $stmt->execute();
    $grupoSeleccionado = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($grupoSeleccionado) {
        $stmt2 = $conexion->prepare("
            SELECT t.*, CONCAT(r.nombre, ' ', r.apellidos) AS responsable
            FROM tareas t
            LEFT JOIN responsables r ON t.id_responsable = r.id
            WHERE t.id_grupo = ?
            ORDER BY t.estado, t.id DESC
        ");
        $stmt2->bind_param("i", $idVer);
        $stmt2->execute();
        $tareasDelGrupo = $stmt2->get_result();
        $stmt2->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Grupos - Control de Tareas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f4f4; }
        h1, h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: #fff; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #333; color: #fff; }
        .btn { padding: 5px 10px; text-decoration: none; color: #fff; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; }
        .btn-editar { background: #007bff; }
        .btn-eliminar { background: #dc3545; }
        .btn-ver { background: #28a745; }
        .form-box { background: #fff; padding: 15px; border-radius: 6px; margin-bottom: 20px; max-width: 500px; }
        input[type=text] { padding: 6px; width: 100%; margin-bottom: 10px; box-sizing: border-box; }
        .checklist { max-height: 150px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; margin-bottom: 10px; }
        .tachado { text-decoration: line-through; color: #888; }
        nav a { margin-right: 15px; }
    </style>
</head>
<body>

<nav>
    <a href="index.php">Inicio</a>
    <a href="tareas.php">Tareas</a>
    <a href="grupos.php"><strong>Grupos</strong></a>
</nav>

<h1>Gestión de Grupos</h1>

<div class="form-box">
    <h2>Crear nuevo grupo</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="crear">
        <input type="text" name="nombre" placeholder="Nombre del grupo" required>

        <p><strong>Asociar tareas pendientes (opcional):</strong></p>
        <div class="checklist">
            <?php if ($tareasPendientes && $tareasPendientes->num_rows > 0): ?>
                <?php while ($t = $tareasPendientes->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="tareas[]" value="<?= $t['id'] ?>">
                        <?= htmlspecialchars($t['detalle']) ?>
                    </label><br>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay tareas pendientes.</p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-ver">Crear grupo</button>
    </form>
</div>

<h2>Listado de grupos</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Acciones</th>
    </tr>
    <?php if ($grupos && $grupos->num_rows > 0): ?>
        <?php while ($g = $grupos->fetch_assoc()): ?>
        <tr>
            <td><?= $g['id'] ?></td>
            <td>
                <form method="POST" style="display:flex; gap:5px;">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?= $g['id'] ?>">
                    <input type="text" name="nombre" value="<?= htmlspecialchars($g['nombre']) ?>">
                    <button type="submit" class="btn btn-editar">Guardar</button>
                </form>
            </td>
            <td>
                <a href="grupos.php?ver=<?= $g['id'] ?>" class="btn btn-ver">Ver tareas</a>
                <a href="grupos.php?eliminar=<?= $g['id'] ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar este grupo?');">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="3">No hay grupos creados.</td></tr>
    <?php endif; ?>
</table>

<?php if ($grupoSeleccionado): ?>
    <h2>Tareas del grupo: <?= htmlspecialchars($grupoSeleccionado['nombre']) ?></h2>
    <table>
        <tr>
            <th>Detalle</th>
            <th>Responsable</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Fecha límite</th>
        </tr>
        <?php if ($tareasDelGrupo && $tareasDelGrupo->num_rows > 0): ?>
            <?php while ($t = $tareasDelGrupo->fetch_assoc()): ?>
            <tr>
                <td class="<?= $t['estado'] === 'Finalizada' ? 'tachado' : '' ?>">
                    <?= htmlspecialchars($t['detalle']) ?>
                </td>
                <td><?= $t['responsable'] ?: 'Sin responsable asignado' ?></td>
                <td><?= $t['estado'] ?></td>
                <td><?= $t['prioridad'] ?></td>
                <td><?= $t['fecha_limite'] ?: '-' ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Este grupo no tiene tareas asociadas.</td></tr>
        <?php endif; ?>
    </table>
<?php endif; ?>

</body>
</html>