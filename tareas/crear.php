<?php
require_once '../consultas/tareas.php';
require_once '../consultas/responsables.php';
require_once '../consultas/grupos.php';

$responsables = listarResponsables();
$grupos = obtenerGrupos($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $detalle        = $_POST['detalle'];
    $prioridad      = $_POST['prioridad'];
    $fecha_limite   = $_POST['fecha_limite'] != '' ? $_POST['fecha_limite'] : null;
    $id_responsable = $_POST['id_responsable'] != '' ? $_POST['id_responsable'] : null;
    $id_grupo       = $_POST['id_grupo'] != '' ? $_POST['id_grupo'] : null;

    crearTarea($conn, $detalle, $prioridad, $fecha_limite, $id_responsable, $id_grupo);

    header("Location: listar.php");
    exit;
}

include '../layout.php';
?>

<div class="contenedor">
    <h1>Crear Tarea</h1>
    <a href="listar.php" class="btn btn-secundario">← Volver</a>

    <br><br>

    <div class="formulario">
        <form method="POST">
            <label>Detalle:</label>
            <textarea name="detalle" required></textarea>

            <label>Prioridad:</label>
            <select name="prioridad" required>
                <option value="Baja">Baja</option>
                <option value="Media" selected>Media</option>
                <option value="Alta">Alta</option>
                <option value="Urgente">Urgente</option>
            </select>

            <label>Fecha límite (opcional):</label>
            <input type="date" name="fecha_limite">

            <label>Responsable (opcional):</label>
            <select name="id_responsable">
                <option value="">Sin responsable</option>
                <?php while ($r = $responsables->fetch_assoc()) { ?>
                    <option value="<?php echo $r['id']; ?>">
                        <?php echo $r['nombre'] . ' ' . $r['apellidos']; ?>
                    </option>
                <?php } ?>
            </select>

            <label>Grupo (opcional):</label>
            <select name="id_grupo">
                <option value="">Sin grupo</option>
                <?php foreach ($grupos as $g) { ?>
                    <option value="<?php echo $g['id']; ?>">
                        <?php echo $g['nombre']; ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit" class="btn btn-primario">Crear Tarea</button>
        </form>
    </div>
</div>

</main>
</body>
</html>