<?php
require_once '../consultas/tareas.php';
require_once '../consultas/responsables.php';
require_once '../consultas/grupos.php';

$id = $_GET['id'];
$tarea = obtenerTareaPorId($conn, $id);
$responsables = listarResponsables();
$grupos = obtenerGrupos($conn);

$transiciones = [
    'Pendiente'   => ['En progreso'],
    'En progreso' => ['Pendiente', 'Bloqueada', 'Finalizada'],
    'Bloqueada'   => ['En progreso'],
    'Finalizada'  => ['Pendiente']
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $detalle        = $_POST['detalle'];
    $prioridad      = $_POST['prioridad'];
    $fecha_limite   = $_POST['fecha_limite'] != '' ? $_POST['fecha_limite'] : null;
    $id_responsable = $_POST['id_responsable'] != '' ? $_POST['id_responsable'] : null;
    $id_grupo       = $_POST['id_grupo'] != '' ? $_POST['id_grupo'] : null;
    $estado         = $_POST['estado'];

    if ($estado == 'Finalizada' && $tarea['estado'] != 'Finalizada') {
        $fecha_finalizacion = date('Y-m-d H:i:s');
    } elseif ($estado != 'Finalizada') {
        $fecha_finalizacion = null;
    } else {
        $fecha_finalizacion = $tarea['fecha_finalizacion'];
    }

    editarTarea($conn, $id, $detalle, $prioridad, $fecha_limite, $id_responsable, $id_grupo, $estado, $fecha_finalizacion);

    header("Location: listar.php");
    exit;
}

include '../layout.php';
?>

<div class="contenedor">
    <h1>Editar Tarea</h1>
    <a href="listar.php" class="btn btn-secundario">← Volver</a>

    <br><br>

    <div class="formulario">
        <form method="POST">
            <label>Detalle:</label>
            <textarea name="detalle" required><?php echo $tarea['detalle']; ?></textarea>

            <label>Prioridad:</label>
            <select name="prioridad" required>
                <?php foreach (['Baja', 'Media', 'Alta', 'Urgente'] as $p) { ?>
                    <option value="<?php echo $p; ?>" <?php echo $tarea['prioridad'] == $p ? 'selected' : ''; ?>>
                        <?php echo $p; ?>
                    </option>
                <?php } ?>
            </select>

            <label>Estado:</label>
            <select name="estado" required>
                <option value="<?php echo $tarea['estado']; ?>" selected>
                    <?php echo $tarea['estado']; ?> (actual)
                </option>
                <?php foreach ($transiciones[$tarea['estado']] as $e) { ?>
                    <option value="<?php echo $e; ?>"><?php echo $e; ?></option>
                <?php } ?>
            </select>

            <label>Fecha límite (opcional):</label>
            <input type="date" name="fecha_limite" value="<?php echo $tarea['fecha_limite']; ?>">

            <label>Responsable (opcional):</label>
            <select name="id_responsable">
                <option value="">Sin responsable</option>
                <?php while ($r = $responsables->fetch_assoc()) { ?>
                    <option value="<?php echo $r['id']; ?>" <?php echo $tarea['id_responsable'] == $r['id'] ? 'selected' : ''; ?>>
                        <?php echo $r['nombre'] . ' ' . $r['apellidos']; ?>
                    </option>
                <?php } ?>
            </select>

            <label>Grupo (opcional):</label>
            <select name="id_grupo">
                <option value="">Sin grupo</option>
                <?php foreach ($grupos as $g) { ?>
                    <option value="<?php echo $g['id']; ?>" <?php echo $tarea['id_grupo'] == $g['id'] ? 'selected' : ''; ?>>
                        <?php echo $g['nombre']; ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit" class="btn btn-primario">Guardar Cambios</button>
        </form>
    </div>
</div>

</main>
</body>
</html>